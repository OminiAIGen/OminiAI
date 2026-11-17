<button
    class="<?php echo e($loop->first ? 'lqd-is-active' : ''); ?> group relative flex items-center gap-4 rounded-2xl bg-white/5 px-7 py-5 text-start text-xs font-semibold leading-none -tracking-wide text-white/60 transition-all lg:grow [&.lqd-is-active]:bg-black [&.lqd-is-active]:text-white"
    data-target="#<?php echo e(\Illuminate\Support\Str::slug($item->menu_title)); ?>"
    href="#<?php echo e(\Illuminate\Support\Str::slug($item->menu_title)); ?>"
>
    <?php if (isset($component)) { $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.outline-glow','data' => ['class' => 'lqd-outline-glow-custom opacity-0 [--outline-glow-w:3px] group-[&.lqd-is-active]:opacity-100']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('outline-glow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lqd-outline-glow-custom opacity-0 [--outline-glow-w:3px] group-[&.lqd-is-active]:opacity-100']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad)): ?>
<?php $attributes = $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad; ?>
<?php unset($__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad)): ?>
<?php $component = $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad; ?>
<?php unset($__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad); ?>
<?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($item->icon)): ?>
        <span class="[&_svg]:h-auto [&_svg]:w-6">
            <?php echo $item->icon; ?>

        </span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <span class="flex flex-col gap-1 tracking-normal">
        <?php echo __($item->menu_title); ?>

        <span class="text-[12px] font-normal opacity-50">
            <?php echo __($item->subtitle_one); ?>

        </span>
    </span>
</button>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/marketing-bot/landing-page/generators/item-trigger.blade.php ENDPATH**/ ?>