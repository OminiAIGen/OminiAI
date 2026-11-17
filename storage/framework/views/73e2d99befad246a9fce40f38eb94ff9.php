<?php
    $base_class = 'flex min-h-7 min-w-7 items-center justify-center rounded-full bg-foreground/[6%] px-2 text-center text-foreground/70 text-sm font-medium';
?>
<span <?php echo e($attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class'))); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_string($value)): ?>
        <?php echo e($value); ?>

    <?php else: ?>
        <?php echo e($value > 1000 ? floatval($value / 1000) . 'k' : $value); ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</span>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/money-with-unit.blade.php ENDPATH**/ ?>