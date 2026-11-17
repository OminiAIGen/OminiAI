<?php
    $wrapper_class = 'lqd-navbar-label-wrap flex w-full min-w-0 items-center gap-2 pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt';
    $text_class =
        'lqd-navbar-label inline-block max-w-full min-w-0 flex-1 overflow-hidden text-ellipsis whitespace-nowrap text-4xs uppercase tracking-widest lg:group-[&.navbar-shrinked]/body:w-full lg:group-[&.navbar-shrinked]/body:px-0 lg:group-[&.navbar-shrinked]/body:text-center';
?>

<span <?php echo e($attributes->withoutTwMergeClasses()->twMerge($wrapper_class, $attributes->get('class'))); ?>>
    <span class="<?php echo e($text_class); ?>"><?php echo e($slot); ?></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($badge)): ?>
        <?php if (isset($component)) { $__componentOriginald30cf9cba6bb540c6bffcc9785239679 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald30cf9cba6bb540c6bffcc9785239679 = $attributes; } ?>
<?php $component = App\View\Components\Badge::resolve(['variant' => 'secondary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Badge::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'shrink-0 rounded-md text-[0.5625rem] group-[&.navbar-shrinked]/body:hidden']); ?>
            <?php echo e(mb_strtoupper($badge)); ?>

         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald30cf9cba6bb540c6bffcc9785239679)): ?>
<?php $attributes = $__attributesOriginald30cf9cba6bb540c6bffcc9785239679; ?>
<?php unset($__attributesOriginald30cf9cba6bb540c6bffcc9785239679); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald30cf9cba6bb540c6bffcc9785239679)): ?>
<?php $component = $__componentOriginald30cf9cba6bb540c6bffcc9785239679; ?>
<?php unset($__componentOriginald30cf9cba6bb540c6bffcc9785239679); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</span>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/navbar/label.blade.php ENDPATH**/ ?>