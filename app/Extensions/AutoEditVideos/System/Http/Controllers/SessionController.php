<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Controllers;

use App\Extensions\AutoEditVideos\System\Http\Requests\StoreAutoEditMessageRequest;
use App\Extensions\AutoEditVideos\System\Models\AutoEditRender;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSession;
use App\Extensions\AutoEditVideos\System\Services\AutoEditBillingService;
use App\Extensions\AutoEditVideos\System\Services\AutoEditService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class SessionController extends Controller
{
    public function __construct(
        private readonly AutoEditService $autoEdit,
        private readonly AutoEditBillingService $billing,
    ) {}

    public function show(AutoEditSession $session): View
    {
        $this->authorizeSession($session);

        $session->load(['messages', 'sources', 'renders']);

        $sessions = AutoEditSession::query()
            ->where('user_id', auth()->id())
            ->latest('id')
            ->limit(50)
            ->get()
            ->map(fn (AutoEditSession $item) => [
                'uuid'       => $item->uuid,
                'title'      => $item->displayTitle(),
                'status'     => $item->status->value,
                'label'      => $item->status->label(),
                'variant'    => $item->status->badgeVariant(),
                'terminal'   => $item->status->isTerminal(),
                'needs_user' => $item->status->needsUser(),
                'today'      => (bool) $item->created_at?->isToday(),
                'url'        => route('dashboard.user.auto-edit-videos.sessions.show', $item),
            ])
            ->values();

        return view('auto-edit-videos::session', [
            'session'  => $session,
            'usage'    => $this->billing->usage(auth()->user()),
            'isDemo'   => Helper::appIsDemo(),
            'sessions' => $sessions,
        ]);
    }

    /**
     * Poll endpoint for a single session: returns JSON for Alpine consumption.
     */
    public function check(AutoEditSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        $session->load(['messages', 'renders']);

        return response()->json([
            'title'      => $session->displayTitle(),
            'status'     => $session->status->value,
            'label'      => $session->status->label(),
            'variant'    => $session->status->badgeVariant(),
            'terminal'   => $session->status->isTerminal(),
            'needs_user' => $session->status->needsUser(),
            'error'      => $session->error_message,
            'messages'   => $session->messages->map(fn ($message) => [
                'id'   => $message->id,
                'role' => $message->isFromUser() ? 'user' : 'agent',
                'body' => $message->body,
                'at'   => $message->created_at?->diffForHumans(),
            ])->values(),
            'renders' => $session->renders->map(fn (AutoEditRender $render) => [
                'uuid'        => $render->uuid,
                'title'       => $render->displayTitle(),
                'status'      => $render->status->value,
                'label'       => $render->status->label(),
                'variant'     => $render->status->badgeVariant(),
                'terminal'    => $render->isTerminal(),
                'url'         => $render->playbackUrl(),
                'thumbnail'   => $render->thumbnail_url,
                'duration'    => $render->formattedDuration(),
                'error'       => $render->error_message,
            ])->values(),
            'pending' => $session->status->isAwaitingAgent()
                || $session->renders->contains(fn (AutoEditRender $render) => ! $render->isTerminal()),
        ]);
    }

    public function message(StoreAutoEditMessageRequest $request, AutoEditSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('This feature is disabled in demo mode.')], 422);
        }

        $result = $this->autoEdit->sendMessage($session, (string) $request->validated('body'));

        if ($result !== true) {
            return response()->json(['message' => $result], 422);
        }

        $session->load(['messages', 'renders']);

        return response()->json([
            'message' => __('Sent to the editor.'),
            'data'    => [
                'status'   => $session->status->value,
                'label'    => $session->status->label(),
                'variant'  => $session->status->badgeVariant(),
                'terminal' => $session->status->isTerminal(),
                'messages' => $session->messages->map(fn ($message) => [
                    'id'   => $message->id,
                    'role' => $message->isFromUser() ? 'user' : 'agent',
                    'body' => $message->body,
                    'at'   => $message->created_at?->diffForHumans(),
                ])->values(),
            ],
        ]);
    }

    /**
     * What this render would cost, asked before the user commits to it.
     */
    public function cost(AutoEditSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        $cost = $this->autoEdit->estimateExportCost($session);

        if (isset($cost['error'])) {
            return response()->json(['message' => $cost['message']], 422);
        }

        return response()->json(['credits' => $cost['credits']]);
    }

    public function export(AutoEditSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('This feature is disabled in demo mode.')], 422);
        }

        $result = $this->autoEdit->requestExport($session);

        if (is_string($result)) {
            return response()->json(['message' => $result], 422);
        }

        return response()->json([
            'message' => __('Rendering started. This usually takes a few minutes.'),
        ]);
    }

    public function destroy(AutoEditSession $session): JsonResponse
    {
        $this->authorizeSession($session);

        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('This feature is disabled in demo mode.')], 422);
        }

        $session->delete();

        return response()->json([
            'message' => __('Edit deleted.'),
        ]);
    }

    private function authorizeSession(AutoEditSession $session): void
    {
        abort_unless($session->user_id === auth()->id(), 403);
    }
}
