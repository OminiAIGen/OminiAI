<?php
    $dubbingViewer = isset($user) && $user ? $user : auth()?->user();
    $dubbingPlan = isset($plan) && $plan && $plan->exists
        ? $plan
        : ($dubbingViewer ? $dubbingViewer->relationPlan : null);

    $showDubbingRow = false;
    $dubbingUnlimited = false;
    $dubbingCredits = 0;
    $dubbingLabel = __('Video Dubbing (seconds)');
    $dubbingIsAdminView = ! (isset($plan) && $plan && $plan->exists)
        && $dubbingViewer
        && method_exists($dubbingViewer, 'isAdmin')
        && $dubbingViewer->isAdmin();

    if ($dubbingIsAdminView) {
        $showDubbingRow = true;
        $dubbingUnlimited = true;
    } elseif ($dubbingPlan && $dubbingPlan->exists && $dubbingPlan->checkOpenAiItem(\App\Enums\Introduction::AI_VIDEO_DUBBING->value)) {
        $dubbingLimit = (int) ($dubbingPlan->video_dubbing_seconds_limit ?? -1);

        if ($dubbingLimit !== 0) {
            $dubbingUnlimited = $dubbingLimit === -1;

            if (isset($plan) && $plan && $plan->exists) {
                $dubbingCredits = $dubbingUnlimited ? 0 : $dubbingLimit;
                $showDubbingRow = true;
            } else {
                $dubbingUserId = isset($user) && $user ? $user->id : auth()?->id();

                if ($dubbingUserId) {
                    $dubbingUsed = (int) \App\Extensions\VideoDubbing\System\Models\VideoDubbing::query()
                        ->where('user_id', $dubbingUserId)
                        ->whereIn('status', ['complete', 'pending', 'running'])
                        ->where('created_at', '>=', now()->startOfMonth())
                        ->sum('duration_seconds');

                    $dubbingCredits = $dubbingUnlimited ? 0 : max(0, $dubbingLimit - $dubbingUsed);
                    $showDubbingRow = $dubbingUnlimited || $dubbingCredits > 0;
                }
            }
        }
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDubbingRow): ?>
    <tr>
        <td class="flex justify-between border-b p-2">
            <?php echo e($dubbingLabel); ?>

        </td>
        <td class="border p-2 text-end">
            <?php echo e($dubbingUnlimited ? __('Unlimited') : $dubbingCredits); ?>

        </td>
    </tr>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/VideoDubbing/System/../resources/views/partials/credit-list-row.blade.php ENDPATH**/ ?>