<div
    x-data="updateAvailable({ routes: { check: '<?php echo e(route('dashboard.user.check.update-available')); ?>', appUpdate: '<?php echo e(route('dashboard.admin.update.index')); ?>', extensionUpdate: '<?php echo e(route('dashboard.admin.marketplace.index')); ?>' } })"
    x-cloak
    x-show="isAvailable"
>
    <?php if (isset($component)) { $__componentOriginal0feec11b8470b4bf00c37924b86dc0af = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0feec11b8470b4bf00c37924b86dc0af = $attributes; } ?>
<?php $component = App\View\Components\Dropdown\Dropdown::resolve(['anchor' => 'end','offsetY' => '10px'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('dropdown.dropdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Dropdown\Dropdown::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('trigger', null, ['class' => 'gap-2.5 text-[#56462E] shadow-none transition-none dark:text-foreground',':href' => 'route','@click.prevent' => 'isVersionUpdateAvailable ? window.location.href = $el.href : null']); ?> 
            <span><?php echo e(__('Update Available')); ?></span>
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-circle-chevron-up'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['stroke-width' => '1.5']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('dropdown', null, ['class' => 'max-h-[40vh] overflow-hidden overflow-y-auto','x-bind:class' => '{ \'hidden\': isVersionUpdateAvailable }']); ?> 
            <template x-for="item in updateAvailableExtensions">
                <a
                    class="flex items-center gap-2 border-b px-3 py-2 text-heading-foreground transition-colors last:border-b-0 hover:bg-foreground/5 hover:no-underline"
                    rel="alternate"
                    :href="'/dashboard/admin/marketplace/' + item.slug"
                >
                    <span x-text="item.name"></span>
                </a>
            </template>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0feec11b8470b4bf00c37924b86dc0af)): ?>
<?php $attributes = $__attributesOriginal0feec11b8470b4bf00c37924b86dc0af; ?>
<?php unset($__attributesOriginal0feec11b8470b4bf00c37924b86dc0af); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0feec11b8470b4bf00c37924b86dc0af)): ?>
<?php $component = $__componentOriginal0feec11b8470b4bf00c37924b86dc0af; ?>
<?php unset($__componentOriginal0feec11b8470b4bf00c37924b86dc0af); ?>
<?php endif; ?>
</div>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/update-available.blade.php ENDPATH**/ ?>