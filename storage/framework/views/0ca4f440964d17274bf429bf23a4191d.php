<?php
    $base_class = 'lqd-card text-card-foreground bg-card-background w-full transition-all group/card';
    $head_base_class = 'lqd-card-head border-b border-card-border px-6 py-3.5 relative transition-border';
    $body_base_class = 'lqd-card-body relative only:grow';
    $foot_base_class = 'lqd-card-foot border-t border-card-border relative transition-border';

    $variations = [
        'variant' => [
            'none' => 'lqd-card-variant-none',
            'solid' => 'lqd-card-solid rounded-card',
            'outline' => 'lqd-card-outline border border-card-border rounded-card',
            'shadow' => 'lqd-card-shadow rounded-card',
            'outline-shadow' => 'lqd-card-outline-shadow border border-card-border rounded-card',
        ],
        'size' => [
            'none' => 'lqd-card-size-none',
            'xs' => 'lqd-card-xs px-5 py-3',
            'sm' => 'lqd-card-sm p-4',
            'md' => 'lqd-card-md py-5 px-7',
            'lg' => 'lqd-card-lg py-8 px-10',
        ],
    ];

    $variant = isset($variations['variant'][$variant]) ? $variations['variant'][$variant] : $variations['variant'][Theme::getSetting('defaultVariations.card.variant', 'outline')];
    $size = isset($variations['size'][$size]) ? $variations['size'][$size] : $variations['size'][Theme::getSetting('defaultVariations.card.size', 'md')];
?>

<div <?php echo e($attributes->withoutTwMergeClasses()->twMerge($base_class, $variant, $attributes->get('class'))); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($head)): ?>
        <div <?php echo e($attributes->twMergeFor('head', $head_base_class, $head->attributes->get('class'))); ?>>
            <?php echo e($head); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div <?php echo e($attributes->twMergeFor('body', $body_base_class, $size)); ?>>
        <?php echo e($slot); ?>

    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($foot)): ?>
        <div <?php echo e($attributes->twMergeFor('foot', $foot_base_class, $foot->attributes->get('class'))); ?>>
            <?php echo e($foot); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/card.blade.php ENDPATH**/ ?>