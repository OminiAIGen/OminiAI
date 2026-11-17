<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Requests;

use App\Extensions\VideoAgent\System\Enums\SessionModeEnum;
use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVideoAgentSessionRequest extends FormRequest
{
    public const INSTRUCTIONS_MAX = 2000;

    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'prompt'       => ['required', 'string', 'min:' . config('video-agent.limits.prompt_min', 1), 'max:' . config('video-agent.limits.prompt_max', 10000)],
            'mode'         => ['nullable', 'string', Rule::in(array_column(SessionModeEnum::cases(), 'value'))],
            'style_id'     => ['nullable', 'string', 'max:191'],
            'brand_kit_id' => ['nullable', 'string', 'max:191'],
            'avatar_id'    => ['nullable', 'string', 'max:191', VideoAgentAvatar::selectionRule((int) $this->user()?->id)],
            'voice_id'     => ['nullable', 'string', 'max:191'],
            // HeyGen accepts landscape or portrait only; omitting the field is
            // what makes it auto-detect from the content.
            'orientation'  => ['nullable', 'string', Rule::in(['landscape', 'portrait'])],
            'duration'     => ['nullable', 'integer', Rule::in(config('video-agent.durations', [15, 30, 60]))],
            'instructions' => ['nullable', 'string', 'max:' . self::INSTRUCTIONS_MAX],
            'asset_ids'    => ['nullable', 'array', 'max:' . config('video-agent.limits.max_files', 20)],
            'asset_ids.*'  => ['string', 'max:191'],
        ];
    }

    /**
     * Duration and instructions are appended to the prompt because HeyGen has
     * no parameter for either, so the ceiling applies to the composed string,
     * not to the textarea alone. Failing here beats silently truncating
     * someone's instructions on the way out.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->hasAny(['prompt', 'duration', 'instructions'])) {
                return;
            }

            $max = (int) config('video-agent.limits.prompt_max', 10000);

            $composed = VideoAgentService::composePrompt(
                (string) $this->string('prompt'),
                $this->filled('duration') ? $this->integer('duration') : null,
                $this->string('instructions')->toString(),
            );

            if (mb_strlen($composed) > $max) {
                $validator->errors()->add('prompt', __(
                    'The prompt, target length and instructions add up to :count characters, which is over the :max limit.',
                    ['count' => mb_strlen($composed), 'max' => $max],
                ));
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prompt.required' => __('Describe the video you want the agent to make.'),
            'prompt.max'      => __('The prompt cannot be longer than :max characters.', ['max' => config('video-agent.limits.prompt_max', 10000)]),
            'asset_ids.max'   => __('You can attach at most :max files to a session.', ['max' => config('video-agent.limits.max_files', 20)]),
        ];
    }
}
