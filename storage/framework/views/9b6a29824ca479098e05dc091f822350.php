<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps([
    'itemSelector' => '.masonry-grid-item',
]) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps([
    'itemSelector' => '.masonry-grid-item',
]); ?>
<?php foreach (array_filter(([
    'itemSelector' => '.masonry-grid-item',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div
    <?php echo e($attributes->merge(['class' => 'masonry-grid'])); ?>

    x-masonry="{
        itemSelector: '<?php echo e($itemSelector); ?>',
        percentPosition: true,
        masonry: {
            columnWidth: '<?php echo e($itemSelector); ?>',
        },
		transitionDuration: 100
    }"
>
    <?php echo e($slot); ?>

</div>

<?php if (! $__env->hasRenderedOnce('2337d37d-bcc2-45a1-9669-8d8e1c6cdfee')): $__env->markAsRenderedOnce('2337d37d-bcc2-45a1-9669-8d8e1c6cdfee');
$__env->startPush('script'); ?>
    <script src="<?php echo e(custom_theme_url('/assets/libs/isotope/isotope.pkgd.min.js')); ?>"></script>
    <script src="<?php echo e(custom_theme_url('/assets/libs/imagesloaded/imagesloaded.pkgd.min.js')); ?>"></script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/masonry-grid.blade.php ENDPATH**/ ?>