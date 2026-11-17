<div class="lqd-feature-item group relative flex flex-col rounded-[36px] bg-black/5 py-10 backdrop-blur-lg transition-all hover:scale-[1.025] hover:bg-[hsl(0_0%_11%/80%)]">
    <?php if (isset($component)) { $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.outline-glow','data' => ['class' => 'lqd-outline-glow-custom rounded-[39px] opacity-0 [--gradient-1:#993DFC] [--gradient-2:#10111A] [--gradient-3:#10111A] [--gradient-4:#EE554A] [--gradient-5:#70B4AF] [--outline-glow-w:3px] group-hover:opacity-100','class:inner' => '![animation-play-state:paused] group-hover:![animation-play-state:running]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('outline-glow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lqd-outline-glow-custom rounded-[39px] opacity-0 [--gradient-1:#993DFC] [--gradient-2:#10111A] [--gradient-3:#10111A] [--gradient-4:#EE554A] [--gradient-5:#70B4AF] [--outline-glow-w:3px] group-hover:opacity-100','class:inner' => '![animation-play-state:paused] group-hover:![animation-play-state:running]']); ?>
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
    <div class="mb-24 px-10">
        <span class="lqd-feature-item-icon inline-grid size-12 place-items-center rounded-full bg-white/10 transition-all group-hover:scale-110 [&_svg]:h-auto [&_svg]:w-full">
            <span class="inline-flex size-5">
                <?php echo $item->image; ?>

            </span>
        </span>
    </div>
    <h6 class="mb-3 border-b border-white/10 px-10 pb-3 text-current">
        <?php echo __($item->title); ?>

    </h6>
    <p class="mb-0 px-10 text-sm font-normal leading-[1.4em]">
        <?php echo __($item->description); ?>

    </p>
</div>

<?php if (! $__env->hasRenderedOnce('2f3dec9f-642e-4d5c-a0f8-3693399de592')): $__env->markAsRenderedOnce('2f3dec9f-642e-4d5c-a0f8-3693399de592');
$__env->startPush('css'); ?>
    <style>
        .lqd-feature-item-icon {
            box-shadow: inset 1px 1px 0 hsl(0 0% 100%/40%), inset 2px 2px 4px -1px hsl(0 0% 100%/20%), inset -1px -1px 3px -2px hsl(0 0% 100%/40%), inset -1px -1px 2px -2px hsl(0 0% 100%/40%), inset -3px -3px 4px -4px hsl(0 0% 100%/40%);
        }

        .lqd-feature-item:hover .lqd-feature-item-icon {
            box-shadow: inset -1px 1px 0 hsl(0 0% 100%/60%), inset -2px 2px 5px -1px hsl(0 0% 100%/40%), inset -1px -1px 3px -1px hsl(0 0% 100%/10%), 3px -3px 6px -2px hsl(0 0% 100%/20%);
        }
    </style>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/marketing-bot/landing-page/features/item.blade.php ENDPATH**/ ?>