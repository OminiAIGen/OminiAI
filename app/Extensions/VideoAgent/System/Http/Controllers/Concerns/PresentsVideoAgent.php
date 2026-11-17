<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers\Concerns;

use App\Extensions\VideoAgent\System\Enums\SessionModeEnum;
use App\Extensions\VideoAgent\System\Enums\SessionStatusEnum;
use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Extensions\VideoAgent\System\Models\VideoAgentMessage;
use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;

/**
 * One shape per entity, shared by the pages that render them server-side and by
 * the JSON the browser polls. Keeping them together is what stops the Alpine
 * bootstrap payload and the poll payload from drifting apart.
 */
trait PresentsVideoAgent
{
    /**
     * @return array<string, mixed>
     */
    protected function sessionRow(VideoAgentSession $session): array
    {
        return [
            'uuid'       => $session->uuid,
            'title'      => $session->displayTitle(),
            'status'     => $session->status->value,
            'label'      => $session->status->label(),
            'variant'    => $session->status->badgeVariant(),
            'terminal'   => $session->isTerminal(),
            'needs_user' => $session->status->needsUser(),
            'at'         => $session->created_at?->diffForHumans(),
            'today'      => (bool) $session->created_at?->isToday(),
            'url'        => route('dashboard.user.video-agent.sessions.show', $session),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function videoRow(VideoAgentVideo $video): array
    {
        return [
            'uuid'       => $video->uuid,
            'title'      => $video->displayTitle(),
            'kind'       => $video->kind->value,
            'kind_label' => $video->kind->label(),
            'audio'      => $video->kind->isAudio(),
            'status'     => $video->status->value,
            'label'      => $video->status->label(),
            'duration'   => $video->formattedDuration(),
            'url'        => $video->playbackUrl(),
            'thumbnail'  => $video->thumbnail_url,
            'error'      => $video->error_message,
            'at'         => $video->created_at?->diffForHumans(),
        ];
    }

    /**
     * Studio card shape for the dashboard My Videos grid. Cloned locally so
     * the VideoAgent views never depend on another extension or core partial.
     *
     * @return array<string, mixed>
     */
    protected function studioEntry(VideoAgentVideo $video): array
    {
        return [
            'source'             => 'video-agent',
            'source_label'       => __('Video Agent'),
            'id'                 => $video->uuid,
            'title'              => $video->displayTitle(),
            'thumb_url'          => $video->displayThumbnailUrl(),
            'video_url'          => $video->playbackUrl(),
            'status'             => $video->status->value,
            'error'              => $video->error_message,
            'status_url'         => route('dashboard.user.video-agent.check') . '?uuids[]=' . $video->uuid,
            'destroy_url'        => route('dashboard.user.video-agent.videos.destroy', $video),
            'created_at'         => $video->created_at?->diffForHumans(),
            'prompt'             => $video->session?->prompt,
            'model'              => $video->kind->label(),
            'formatted_duration' => $video->duration_seconds ? $video->formattedDuration() : null,
            '_ts'                => $video->created_at?->getTimestamp() ?? 0,
        ];
    }

    /**
     * The canonical session payload: what the thread renders on first paint and
     * what every poll, message, approve and stop hands back.
     *
     * @return array<string, mixed>
     */
    protected function sessionPayload(VideoAgentSession $session): array
    {
        $storyboard = $session->storyboard();
        $messages = $session->messages()->get()->sort(function (VideoAgentMessage $first, VideoAgentMessage $second): int {
            $firstAt = is_numeric($first->meta['created_at'] ?? null)
                ? (int) $first->meta['created_at']
                : $first->created_at?->timestamp;
            $secondAt = is_numeric($second->meta['created_at'] ?? null)
                ? (int) $second->meta['created_at']
                : $second->created_at?->timestamp;

            return ($firstAt <=> $secondAt) ?: ($first->getKey() <=> $second->getKey());
        });

        $hasOpeningMessage = $messages->contains(fn (VideoAgentMessage $message): bool => $message->role === VideoAgentMessage::ROLE_USER
            && $message->body === $session->prompt);

        if ($hasOpeningMessage) {
            $openingEcho = $messages->first(fn (VideoAgentMessage $message): bool => $message->role === VideoAgentMessage::ROLE_USER
                && ($message->meta['role'] ?? null) === 'user'
                && VideoAgentService::isOpeningPromptEcho(
                    $session,
                    (string) $message->body,
                    is_numeric($message->meta['created_at'] ?? null) ? (int) $message->meta['created_at'] : null,
                ));

            if ($openingEcho !== null) {
                $messages = $messages->reject(fn (VideoAgentMessage $message): bool => $message->getKey() === $openingEcho->getKey());
            }
        }

        $latestAgentMessage = $messages->filter(fn (VideoAgentMessage $message): bool => $message->role === VideoAgentMessage::ROLE_AGENT)->last();
        $canApprove = $session->mode === SessionModeEnum::Chat && (
            $session->status === SessionStatusEnum::Reviewing
            || ($session->status === SessionStatusEnum::WaitingForInput
                && ($latestAgentMessage?->meta['type'] ?? null) === 'resource'
                && filled($latestAgentMessage?->meta['resource_ids'] ?? null))
        );
        $sessionVideos = $session->videos()->latest()->get();
        $failedVideo = $sessionVideos->first(fn (VideoAgentVideo $video): bool => $video->status === VideoStatusEnum::Failed);
        $videoError = $failedVideo === null ? null : ($failedVideo->error_message ?: __('HeyGen could not render this video.'));
        $outputPending = $sessionVideos->contains(fn (VideoAgentVideo $video): bool => in_array($video->status, [
            VideoStatusEnum::Queued,
            VideoStatusEnum::Processing,
        ], true) && ! ($video->status === VideoStatusEnum::Queued && $session->status->needsUser()));

        return [
            'uuid'           => $session->uuid,
            'title'          => $session->displayTitle(),
            'status'         => $session->status->value,
            'label'          => $session->status->label(),
            'variant'        => $session->status->badgeVariant(),
            'needs_user'     => $session->status->needsUser(),
            'can_approve'    => $canApprove,
            'terminal'       => $session->isTerminal(),
            'error'          => $session->error_message ?: $videoError,
            'output_pending' => $outputPending,
            'messages'       => $messages
                ->map(fn (VideoAgentMessage $message): array => [
                    'id'   => $message->getKey(),
                    'role' => $message->role,
                    'kind' => $message->kind,
                    'body' => $message->body,
                    'at'   => $message->created_at?->diffForHumans(),
                ])
                ->values()
                ->all(),
            'scenes' => $storyboard?->scenes() ?? [],
            'videos' => $sessionVideos
                ->filter(fn (VideoAgentVideo $video): bool => $video->status === VideoStatusEnum::Completed && filled($video->playbackUrl()))
                ->map(fn (VideoAgentVideo $video): array => $this->videoRow($video))
                ->values()
                ->all(),
        ];
    }
}
