<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Enums\SessionStatusEnum;
use App\Extensions\VideoAgent\System\Http\Controllers\Concerns\PresentsVideoAgent;
use App\Extensions\VideoAgent\System\Http\Requests\StoreVideoAgentMessageRequest;
use App\Extensions\VideoAgent\System\Models\VideoAgentMessage;
use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Services\VideoAgentBillingService;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    use PresentsVideoAgent;

    public function __construct(
        private readonly VideoAgentService $service,
        private readonly VideoAgentBillingService $billing,
    ) {}

    public function show(VideoAgentSession $session): View
    {
        $this->authorizeSession($session);

        $user = auth()->user();

        return view('video-agent::session', [
            'session'  => $session->load(['messages', 'videos']),
            'payload'  => $this->sessionPayload($session),
            'usage'    => $this->billing->usage($user),
            'sessions' => VideoAgentSession::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(24)
                ->get(),
        ]);
    }

    /**
     * Polled by the session page. Also drives the session forward when no queue
     * worker is running.
     */
    public function check(VideoAgentSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        $session = $this->service->sync($session);

        foreach ($session->videos()->unfinished()->visibleToUser()->get() as $video) {
            $this->service->syncVideo($video);
        }

        return response()->json($this->sessionPayload($session->refresh()));
    }

    public function message(StoreVideoAgentMessageRequest $request, VideoAgentSession $session): RedirectResponse|JsonResponse
    {
        $this->authorizeSession($session);

        if (Helper::appIsDemo()) {
            return $this->refuse($request, $session, __('This feature is disabled in demo mode.'));
        }

        $kind = $session->status === SessionStatusEnum::Reviewing
            ? VideoAgentMessage::KIND_REVISION
            : VideoAgentMessage::KIND_MESSAGE;

        $result = $this->service->sendMessage($session, $request->string('message')->toString(), $kind);

        return $result === true
            ? $this->accept($request, $session)
            : $this->refuse($request, $session, $result);
    }

    public function approve(Request $request, VideoAgentSession $session): RedirectResponse|JsonResponse
    {
        $this->authorizeSession($session);

        if (Helper::appIsDemo()) {
            return $this->refuse($request, $session, __('This feature is disabled in demo mode.'));
        }

        $result = $this->service->approve($session);

        return $result === true
            ? $this->accept($request, $session, __('Storyboard approved. Rendering has started.'))
            : $this->refuse($request, $session, $result);
    }

    public function stop(Request $request, VideoAgentSession $session): RedirectResponse|JsonResponse
    {
        $this->authorizeSession($session);

        $result = $this->service->stop($session);

        return $result === true
            ? $this->accept($request, $session, __('Session stopped.'))
            : $this->refuse($request, $session, $result);
    }

    public function destroy(Request $request, VideoAgentSession $session): RedirectResponse|JsonResponse
    {
        $this->authorizeSession($session);

        $session->delete();

        if ($request->wantsJson()) {
            return response()->json(['deleted' => true]);
        }

        return to_route('dashboard.user.video-agent.index')
            ->with(['type' => 'success', 'message' => __('Deleted successfully.')]);
    }

    /**
     * The thread patches itself from the returned payload instead of reloading,
     * so every success hands back the same shape `check()` does.
     */
    private function accept(Request $request, VideoAgentSession $session, ?string $message = null): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json(array_merge($this->sessionPayload($session->refresh()), ['message' => $message]));
        }

        return $message === null
            ? back()
            : back()->with(['type' => 'success', 'message' => $message]);
    }

    private function refuse(Request $request, VideoAgentSession $session, string $message): RedirectResponse|JsonResponse
    {
        if ($request->wantsJson()) {
            return response()->json(array_merge($this->sessionPayload($session->refresh()), ['error' => $message]), 422);
        }

        return back()->with(['type' => 'error', 'message' => $message]);
    }

    private function authorizeSession(VideoAgentSession $session): void
    {
        abort_unless($session->user_id === auth()->id(), 403);
    }
}
