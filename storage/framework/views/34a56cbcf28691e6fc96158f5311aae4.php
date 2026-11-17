<?php
    $base_class = 'lqd-tooltip-container group relative inline-flex cursor-default';
    $content_class =
        'lqd-tooltip-content invisible fixed z-50 w-max max-w-72 text-pretty rounded-xl bg-background/80 px-4 py-3 text-center text-xs leading-normal text-foreground opacity-0 shadow-lg shadow-black/5 backdrop-blur-sm backdrop-saturate-150 transition';
?>

<span
    <?php echo e($attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class'))); ?>

    x-data="lqdInfoTooltip({ preferredPlacement: '<?php echo e($anchor === 'top' ? 'bottom' : 'top'); ?>' })"
    x-ref="trigger"
    @mouseenter="show()"
    @mouseleave="hide()"
    @focusin="show()"
    @focusout="hide()"
>
    <span <?php echo e($attributes->twMergeFor('icon', 'lqd-tooltip-icon opacity-40')); ?>>
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-info-circle-filled'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
    </span>
    <span
        <?php echo e($attributes->twMergeFor('content', $content_class)); ?>

        x-ref="tooltip"
        :class="{ 'invisible opacity-0': !open }"
    >
        <?php echo $text; ?>


        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($drivers->isNotEmpty()): ?>
            <div>
                <h5 class="font-semibold"><?php echo e(__('Credits Details')); ?></h5>
                <hr class="my-3 border-heading-foreground/10" />

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$driver->hasCreditBalance()): ?>
                        <?php continue; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="flex justify-between gap-x-1 border-b py-1.5 text-2xs last:border-b-0">
                        <span class="text-start"><?php echo e($driver->enum()->value); ?></span>
                        <span class="text-end font-medium"><?php echo e($driver->isUnlimitedCredit() ? __('Unlimited') : $driver->creditBalance()); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </span>
</span>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/components/info-tooltip.blade.php ENDPATH**/ ?>