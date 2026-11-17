<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex flex-col','class:body' => 'flex flex-col justify-center grow min-h-[350px] w-full','id' => ''.e('admin-card-' . ($widget?->name?->value ?? 'user-client')).'']); ?>
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
<?php $component->withName('tabler-device-mobile-question'); ?>
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
                <?php echo e(__('User Client')); ?>

                <?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['text' => ''.e(__('See which devices are being used to access the platform.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'link','href' => ''.e(route('dashboard.admin.users.index')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <span class="text-nowrap font-bold text-foreground"> <?php echo e(__('View User Clients')); ?> </span>
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 rtl:rotate-180']); ?>
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
<?php if (isset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $attributes = $__attributesOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__attributesOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale67687e3e4e61f963b25a6bcf3983629)): ?>
<?php $component = $__componentOriginale67687e3e4e61f963b25a6bcf3983629; ?>
<?php unset($__componentOriginale67687e3e4e61f963b25a6bcf3983629); ?>
<?php endif; ?>
     <?php $__env->endSlot(); ?>
    <?php
        $mobileCount = \App\Helpers\Classes\Helper::appIsDemo() ? 44 : cache()->get('user_behavior_data')[0]['value'];
        $desktopCount = \App\Helpers\Classes\Helper::appIsDemo() ? 63 : cache()->get('user_behavior_data')[1]['value'];

        $all = $mobileCount + $desktopCount;
        if ($all == 0) {
            $all = 1;
        }

        $mobilePercent = round(($mobileCount / $all) * 100, 2);
        $desktopPercent = round(($desktopCount / $all) * 100, 2);
    ?>
    <div id="user-behaviour-chart">
        <div>
            <div class="lqd-progress flex h-2 overflow-hidden rounded-full">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($desktopPercent != 0): ?>
                    <div
                        class="lqd-progress-bar h-full grow"
                        style="width: <?php echo e($desktopPercent); ?>%; background-color: #1FBA96;"
                    >
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($mobilePercent != 0): ?>
                    <div
                        class="lqd-progress-bar h-full grow"
                        style="width: <?php echo e($mobilePercent); ?>%; background-color: #20C69F33;"
                    >
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        <div class="flex h-32">
            <div class="group flex shrink-0 grow basis-0 flex-col justify-center space-y-3 px-5 pt-9 text-xs text-heading-foreground sm:px-9 md:max-lg:px-2">
                <div class="flex items-center gap-2 group-last:flex-row-reverse">
                    <span
                        class="h-[18px] w-1 rounded-full"
                        style="background-color: #1FBA96"
                    ></span>
                    <?php echo e(__('Desktop')); ?>

                </div>
                <div class="font-heading text-[28px] font-bold text-heading-foreground/80">
                    <?php echo e(number_format($desktopPercent, 2)); ?>%
                </div>
            </div>
            
            <div class="relative flex w-px items-center justify-center bg-border">
                <div class="inline-flex size-[50px] shrink-0 items-center justify-center rounded-full border bg-background text-sm font-medium shadow-sm">
                    <?php echo app('translator')->get('vs'); ?>
                </div>
            </div>
            
            <div class="group flex shrink-0 grow basis-0 flex-col justify-center space-y-3 px-5 pt-9 text-end text-xs text-heading-foreground sm:px-9 md:max-lg:px-2">
                <div class="flex items-center gap-2 group-last:flex-row-reverse">
                    <span
                        class="h-[18px] w-1 rounded-full"
                        style="background-color: #20C69F33"
                    ></span>
                    <?php echo e(__('Mobile')); ?>

                </div>
                <div class="font-heading text-[28px] font-bold text-heading-foreground/80">
                    <?php echo e(number_format($mobilePercent, 2)); ?>%
                </div>
            </div>
        </div>
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
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/admin/dashboard/user-client.blade.php ENDPATH**/ ?>