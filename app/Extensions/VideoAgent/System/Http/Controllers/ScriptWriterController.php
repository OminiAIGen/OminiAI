<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Usage;
use App\Services\Ai\AiCompletionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * HeyGen has no script-writing endpoint, so the composer's Script Writer runs
 * on MagicAI's own word model and charges the user's AI credits, following the
 * same shape as AiPersonaService::enhanceScript().
 */
class ScriptWriterController extends Controller
{
    public const TOPIC_MAX = 2000;

    /**
     * Speech runs at roughly this pace, so a word budget lands the duration far
     * more reliably than asking the model for a number of seconds.
     */
    private const WORDS_PER_MINUTE = 140;

    public function __construct(private readonly AiCompletionService $completion) {}

    /**
     * The word model this bills — the app default, not a HeyGen entity, since
     * the drafting happens entirely on MagicAI's side.
     */
    public static function entity(): EntityEnum
    {
        return EntityEnum::fromSlug((string) Setting::getCache()?->openai_default_model)
            ?? EntityEnum::GPT_5_MINI;
    }

    public function store(Request $request): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('This feature is disabled in demo mode.')], 422);
        }

        $data = $request->validate([
            'topic'    => ['required', 'string', 'max:' . self::TOPIC_MAX],
            'duration' => ['nullable', 'integer', 'in:' . implode(',', (array) config('video-agent.durations', [15, 30, 60]))],
        ]);

        $driver = Entity::driver(self::entity());

        // A JSON endpoint cannot redirect, so check the balance rather than
        // calling redirectIfNoCreditBalance() like the Blade-backed tools do.
        if (! $driver->hasCreditBalance()) {
            return response()->json(['message' => __('You do not have enough credits to generate a script.')], 422);
        }

        try {
            $script = self::clean($this->completion->completeUserOnly($this->prompt($data)));
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => __('The script could not be generated. Please try again.')], 422);
        }

        if ($script === '') {
            return response()->json(['message' => __('The script came back empty. Please try again.')], 422);
        }

        // Word models are billed on what they produced, not what they were asked.
        $driver->input($script)->calculateCredit()->decreaseCredit();
        Usage::getSingle()->updateWordCounts($driver->calculate());

        return response()->json(['script' => $script]);
    }

    /**
     * Models still label or quote their answer now and then, and whatever comes
     * back is spoken aloud verbatim — a stray `Script:` becomes a word the
     * avatar says.
     */
    public static function clean(string $script): string
    {
        $script = trim($script);
        $script = (string) preg_replace('/^(?:script|voice-?over|vo|narration)\s*:\s*/iu', '', $script);
        $script = trim($script);

        // Curly quotes are spelled out: a literal “ inside a character class is
        // three bytes, and the class would match each of them on its own.
        $open = '"\x{201C}\x{2018}\x{00AB}\'';
        $close = '"\x{201D}\x{2019}\x{00BB}\'';

        // Only unwrap when the whole thing is quoted; a script that merely ends
        // on a quoted phrase keeps its punctuation.
        if (preg_match('/^[' . $open . '](.*)[' . $close . ']$/su', $script, $matches)
            && ! preg_match('/[' . $open . $close . ']/u', $matches[1])) {
            $script = trim($matches[1]);
        }

        return $script;
    }

    /**
     * The script is the video's marketing copy, spoken in the brand's own voice
     * — not a presenter talking *about* a subject. Asking for "an AI avatar
     * presenter" is what produced hosts who introduce themselves by name and
     * then explain the topic like a lecture, which is never what someone
     * writing a promo wants.
     *
     * @param  array{topic: string, duration?: int|null}  $data
     */
    private function prompt(array $data): string
    {
        $seconds = (int) ($data['duration'] ?? 30);
        $words = max(25, (int) round($seconds / 60 * self::WORDS_PER_MINUTE));

        return implode("\n", [
            __('You are writing the voice-over for a short promotional video. Write it as the brand speaking directly to the viewer.'),
            '',
            __('Rules:'),
            __('- Speak as the company: "we", "our", "us". Address the viewer as "you".'),
            __('- Open with the benefit or the promise, never with a greeting or an introduction.'),
            __('- Never invent a presenter, a host or a name, and never have anyone introduce themselves.'),
            __('- Never describe the video itself. No "in this video", "let\'s talk about", "today I will explain".'),
            __('- Do not define or explain the subject as a concept. Sell what it does for the viewer.'),
            __('- Close with a clear call to action.'),
            __('- Confident, warm, plain language. No hype stacking, no rhetorical questions, no emoji.'),
            __('- The topic may be a few keywords rather than a sentence; treat it as the subject of the promo and write the copy for it.'),
            '',
            __('Length: about :seconds seconds when read aloud, which is roughly :words words. Two or three short paragraphs.', [
                'seconds' => $seconds,
                'words'   => $words,
            ]),
            '',
            __('Return only the spoken words as plain text. No markdown, no headings, no stage directions, no scene or speaker labels, no surrounding quotes.'),
            '',
            __('Topic:'),
            trim($data['topic']),
        ]);
    }
}
