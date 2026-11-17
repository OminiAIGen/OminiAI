<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex flex-col','class:body' => 'flex flex-col justify-center grow','id' => ''.e('admin-card-' . ($widget?->name?->value ?? 'cost-management')).'']); ?>
     <?php $__env->slot('head', null, ['class' => 'flex items-center justify-between px-5 py-3.5']); ?> 
        <div class="flex items-center gap-4">
            <?php if (isset($component)) { $__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9 = $attributes; } ?>
<?php $component = App\View\Components\LqdIcon::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('lqd-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\LqdIcon::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'bg-background text-heading-foreground dark:bg-foreground/5']); ?>
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-cash-banknote'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6','stroke-width' => '1.5']); ?>
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
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9)): ?>
<?php $attributes = $__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9; ?>
<?php unset($__attributesOriginalfcf158437a8f91c7c912d5fdba2a4dd9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9)): ?>
<?php $component = $__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9; ?>
<?php unset($__componentOriginalfcf158437a8f91c7c912d5fdba2a4dd9); ?>
<?php endif; ?>
            <h4 class="m-0 flex items-center gap-1 text-base font-medium">
                <?php echo e(__('Cost Management')); ?>

                <?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['text' => ''.e(__('See how much each user costs vs. what they bring in.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('info-tooltip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\InfoTooltip::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4)): ?>
<?php $attributes = $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4; ?>
<?php unset($__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4)): ?>
<?php $component = $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4; ?>
<?php unset($__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4); ?>
<?php endif; ?>
            </h4>
        </div>
     <?php $__env->endSlot(); ?>

    <?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve(['size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mt-4','class:body' => 'flex items-center justify-between flex-nowrap py-4']); ?>
        <div class="flex grow flex-col items-start gap-px">
            <p class="mb-0 text-sm font-medium text-heading-foreground">
                <?php echo app('translator')->get('Cost Per User'); ?>
            </p>
            <p class="mb-0 text-base font-semibold opacity-70 sm:text-[22px]">
                <?php echo e(currency()->symbol . (\App\Helpers\Classes\Helper::appIsDemo()  ? 7 : cache()->get('cost_per_user', 0.0))); ?>

            </p>
            <p class="mb-0 text-2xs font-medium opacity-70">
                <?php echo app('translator')->get('Average'); ?>
            </p>
        </div>
        <span class="h-28 w-px bg-border"></span>
        <div class="flex grow flex-col items-end gap-px">
            <p class="mb-0 text-sm font-medium text-heading-foreground">
                <?php echo app('translator')->get('Income Per User'); ?>
            </p>
            <p class="mb-0 text-base font-semibold opacity-70 sm:text-[22px]">
                <?php echo e(currency()->symbol .(\App\Helpers\Classes\Helper::appIsDemo()  ? 5 : cache()->get('income_per_user', 0.0))); ?>

            </p>
            <p class="mb-0 text-2xs font-medium opacity-70">
                <?php echo app('translator')->get('Net Profit'); ?>
				<?php echo e(currency()->symbol . (\App\Helpers\Classes\Helper::appIsDemo() ? 2 : cache()->get('net_profit', 0.0))); ?>

            </p>
        </div>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $attributes = $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $component = $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>

    <div class="my-8 flex h-full flex-col justify-center gap-11">
        <div class="relative w-full">
            <ul class="flex h-2 w-full flex-nowrap items-center overflow-hidden rounded-7xl">
                <li class="h-full w-full bg-[#DC524C]"></li>
                <li class="h-full w-full bg-[#E77B35]"></li>
                <li class="h-full w-full bg-[#E0B43E]"></li>
                <li class="h-full w-full bg-[#20C69F]"></li>
            </ul>
            <div
                class="absolute top-1/2 flex -translate-y-1/2 bg-card-background px-2"
                style="left: <?php echo e(max(0, min(99, \App\Helpers\Classes\Helper::appIsDemo() ? 64 : cache()->get('net_profit', 0.0) ))); ?>%"
            >
                <span class="h-5 w-1.5 rounded-[55px] bg-foreground/30"></span>
            </div>
        </div>
        <ul class="flex flex-nowrap justify-between">
            <li class="flex items-center gap-2">
                <span class="h-[5px] w-4 rounded-[55px] bg-[#DC524C]"></span>
                <p class="mb-0 font-normal text-foreground">
                    <?php echo e(__('Very Poor')); ?>

                </p>
            </li>
            <li class="flex items-center gap-2">
                <span class="h-[5px] w-4 rounded-[55px] bg-[#E77B35]"></span>
                <p class="mb-0 font-normal text-foreground">
                    <?php echo e(__('Poor')); ?>

                </p>
            </li>
            <li class="flex items-center gap-2">
                <span class="h-[5px] w-4 rounded-[55px] bg-[#E0B43E]"></span>
                <p class="mb-0 font-normal text-foreground">
                    <?php echo e(__('Good')); ?>

                </p>
            </li>
            <li class="flex items-center gap-2">
                <span class="h-[5px] w-4 rounded-[55px] bg-[#20C69F]"></span>
                <p class="mb-0 font-normal text-foreground">
                    <?php echo e(__('Excellent')); ?>

                </p>
            </li>
        </ul>
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $attributes = $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64)): ?>
<?php $component = $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64; ?>
<?php unset($__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64); ?>
<?php endif; ?>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/admin/dashboard/cost-management.blade.php ENDPATH**/ ?>