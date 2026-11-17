<?php
    $ugcViewer = isset($user) && $user ? $user : auth()?->user();
    $ugcPlan = isset($plan) && $plan && $plan->exists
        ? $plan
        : ($ugcViewer ? $ugcViewer->relationPlan : null);

    $showUgcRow = false;
    $ugcUnlimited = false;
    $ugcCredits = 0;
    $ugcLabel = __('UGC Creator (videos)');
    $ugcIsAdminView = ! (isset($plan) && $plan && $plan->exists)
        && $ugcViewer
        && method_exists($ugcViewer, 'isAdmin')
        && $ugcViewer->isAdmin();

    if ($ugcIsAdminView) {
        $showUgcRow = true;
        $ugcUnlimited = true;
    } elseif ($ugcPlan && $ugcPlan->exists && $ugcPlan->checkOpenAiItem('ugc_creator')) {
        $ugcLimit = (int) ($ugcPlan->ugc_creator_videos_limit ?? -1);

        if ($ugcLimit !== 0) {
            $ugcUnlimited = $ugcLimit === -1;

            if (isset($plan) && $plan && $plan->exists) {
                $ugcCredits = $ugcUnlimited ? 0 : $ugcLimit;
                $showUgcRow = true;
            } else {
                $ugcUserId = isset($user) && $user ? $user->id : auth()?->id();

                if ($ugcUserId) {
                    $ugcUsed = (int) \App\Extensions\UGCCreator\System\Models\UGCCreatorVideo::query()
                        ->forUser($ugcUserId)
                        ->currentMonth()
                        ->whereNotIn('status', [\App\Extensions\UGCCreator\System\Models\UGCCreatorVideo::STATUS_FAILED])
                        ->count();

                    $ugcCredits = $ugcUnlimited ? 0 : max(0, $ugcLimit - $ugcUsed);
                    $showUgcRow = $ugcUnlimited || $ugcCredits > 0;
                }
            }
        }
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showUgcRow): ?>
    <tr>
        <td class="flex justify-between border-b p-2">
            <?php echo e($ugcLabel); ?>

        </td>
        <td class="border p-2 text-end">
            <?php echo e($ugcUnlimited ? __('Unlimited') : $ugcCredits); ?>

        </td>
    </tr>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/UGCCreator/System/../resources/views/partials/credit-list-row.blade.php ENDPATH**/ ?>