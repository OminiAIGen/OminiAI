<?php
    $base_class = 'lqd-form-step flex items-center gap-3 rounded-xl bg-primary/5 px-4 py-3 text-sm font-semibold dark:bg-primary/10';
    $step_base_class = 'lqd-form-step-num inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-primary text-sm text-primary-foreground';

    if ($autoIncrement) {
        $step_base_class .= ' lqd-form-step-num-auto';
    }
?>

<h3
    <?php echo e($attributes->withoutTwMergeClasses()->twMerge($base_class)); ?>

    <?php echo e($attributes); ?>

>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($step != '' || $autoIncrement): ?>
        <span <?php echo e($attributes->twMergeFor('step', $step_base_class)); ?>>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$autoIncrement): ?>
                <?php echo e($step); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php echo e($label); ?>

    <?php echo e($slot); ?>

</h3>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/form-step.blade.php ENDPATH**/ ?>