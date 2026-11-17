<div class="header-focus-mode-nav-toggle-container me-3 hidden items-center gap-3 lg:group-[&.focus-mode]/body:flex">
    <?php if (isset($component)) { $__componentOriginal151f9c9d62d883b4660892a1d6edcc67 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal151f9c9d62d883b4660892a1d6edcc67 = $attributes; } ?>
<?php $component = App\Extensions\FocusMode\System\View\Components\DropDown::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('focus-mode-drop-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\Extensions\FocusMode\System\View\Components\DropDown::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal151f9c9d62d883b4660892a1d6edcc67)): ?>
<?php $attributes = $__attributesOriginal151f9c9d62d883b4660892a1d6edcc67; ?>
<?php unset($__attributesOriginal151f9c9d62d883b4660892a1d6edcc67); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal151f9c9d62d883b4660892a1d6edcc67)): ?>
<?php $component = $__componentOriginal151f9c9d62d883b4660892a1d6edcc67; ?>
<?php unset($__componentOriginal151f9c9d62d883b4660892a1d6edcc67); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($title ?? true): ?>
        <?php if (isset($component)) { $__componentOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.header-logo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('header-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e)): ?>
<?php $attributes = $__attributesOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e; ?>
<?php unset($__attributesOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e)): ?>
<?php $component = $__componentOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e; ?>
<?php unset($__componentOriginalfcd9f9dda10a0d29a59ffd5e02ae1d1e); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\omini_customization\OminiAI\app\Extensions\FocusMode\System/../resources/views/header.blade.php ENDPATH**/ ?>