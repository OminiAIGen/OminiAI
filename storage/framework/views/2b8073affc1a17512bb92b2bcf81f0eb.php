<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'base_val' => 1,
    'dir' => null,
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'base_val' => 1,
    'dir' => null,
]); ?>
<?php foreach (array_filter(([
    'base_val' => 1,
    'dir' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div
    <?php echo e($attributes->twMerge('lqd-progressive-blur-container overflow-hidden absolute top-0 start-0 end-0 bottom-0 pointer-events-none')); ?>

    style="--dir-y:<?php echo e($dir === 'reverse' ? '1' : '-1'); ?>; --base-val:<?php echo e($base_val); ?>"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i < 7; $i++): ?>
        <div class="lqd-progressive-blur-filter <?php echo e($dir === 'reverse' ? 'reverse' : ''); ?> absolute bottom-0 end-0 start-0 top-0"></div>
    <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="lqd-progressive-blur-gradient absolute bottom-0 end-0 start-0 top-0"></div>
</div>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/progressive-blur.blade.php ENDPATH**/ ?>