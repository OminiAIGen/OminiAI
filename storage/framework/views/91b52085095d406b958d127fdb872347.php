<?php
    $online_user_percentage = round((cache()->get('online_user', 0) / cache()->get('total_user', 1)) * 100, 0);
?>
<?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'flex flex-col','class:body' => 'flex flex-col justify-center grow','id' => ''.e('admin-card-' . ($widget?->name?->value ?? 'users')).'']); ?>
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
<?php $component->withName('tabler-user-circle'); ?>
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
                <?php echo e(__('Users')); ?>

                <?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['text' => ''.e(__('Overview of current user types and status.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
<?php $component = App\View\Components\Button::resolve(['variant' => 'link','href' => ''.e(route('dashboard.admin.users.dashboard')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <span class="text-nowrap font-bold"> <?php echo e(__('Manage Users')); ?> </span>
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
    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-4">
            <div class="flex justify-between">
                <span class="text-base font-medium"><strong class="me-2 text-xl font-bold"><?php echo e(\App\Helpers\Classes\Helper::appIsDemo() ? 23 : cache()->get('online_user')); ?></strong>Online
                    Users</span>
                <span class="text-xs font-bold"><?php echo e($online_user_percentage); ?>%</span>
            </div>
            <div class="flex w-full rounded-7xl border p-4">
                <div class="relative h-2.5 w-full rounded-7xl bg-foreground/10">
                    <span
                        class="absolute left-0 top-0 h-2.5 rounded-7xl bg-[#1FBA96]"
                        style="width: <?php echo e($online_user_percentage); ?>%"
                    ></span>
                </div>
            </div>
        </div>
        <ul class="flex flex-col">
            <li class="flex items-center justify-between border-b border-card-border py-2.5">
                <div class="flex items-center gap-2.5">
                    <span class="size-2.5 rounded-sm bg-[#818B99]"></span>
                    <p class="mb-0 text-base font-medium"><?php echo e(__('Total Users')); ?></p>
                </div>
                <?php if (isset($component)) { $__componentOriginal2a26950522e239ef27658002235982d9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2a26950522e239ef27658002235982d9 = $attributes; } ?>
<?php $component = App\View\Components\MoneyWithUnit::resolve(['value' => cache()->get('total_user', 0)] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('money-with-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\MoneyWithUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $attributes = $__attributesOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__attributesOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $component = $__componentOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__componentOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
            </li>
            <li class="flex items-center justify-between border-b border-card-border py-2.5">
                <div class="flex items-center gap-2.5">
                    <span class="size-2.5 rounded-sm bg-[#20C69F]"></span>
                    <p class="mb-0 text-base font-medium"><?php echo e(__('Free Users')); ?></p>
                </div>
                <?php if (isset($component)) { $__componentOriginal2a26950522e239ef27658002235982d9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2a26950522e239ef27658002235982d9 = $attributes; } ?>
<?php $component = App\View\Components\MoneyWithUnit::resolve(['value' => cache()->get('free_user', 0)] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('money-with-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\MoneyWithUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $attributes = $__attributesOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__attributesOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $component = $__componentOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__componentOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
            </li>
            <li class="flex items-center justify-between border-b border-card-border py-2.5">
                <div class="flex items-center gap-2.5">
                    <span class="size-2.5 rounded-sm bg-primary"></span>
                    <p class="mb-0 text-base font-medium"><?php echo e(__('Paid Users')); ?></p>
                </div>
                <?php if (isset($component)) { $__componentOriginal2a26950522e239ef27658002235982d9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2a26950522e239ef27658002235982d9 = $attributes; } ?>
<?php $component = App\View\Components\MoneyWithUnit::resolve(['value' => cache()->get('paid_user', 0)] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('money-with-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\MoneyWithUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $attributes = $__attributesOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__attributesOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $component = $__componentOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__componentOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
            </li>
            <li class="flex items-center justify-between py-2.5">
                <div class="flex items-center gap-2.5">
                    <span class="size-2.5 rounded-sm bg-[#93C5FD]"></span>
                    <p class="mb-0 text-base font-medium"><?php echo e(__('Trial Users')); ?></p>
                </div>
                <?php if (isset($component)) { $__componentOriginal2a26950522e239ef27658002235982d9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2a26950522e239ef27658002235982d9 = $attributes; } ?>
<?php $component = App\View\Components\MoneyWithUnit::resolve(['value' => cache()->get('trial_user', 0)] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('money-with-unit'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\MoneyWithUnit::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $attributes = $__attributesOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__attributesOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2a26950522e239ef27658002235982d9)): ?>
<?php $component = $__componentOriginal2a26950522e239ef27658002235982d9; ?>
<?php unset($__componentOriginal2a26950522e239ef27658002235982d9); ?>
<?php endif; ?>
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
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/admin/dashboard/users.blade.php ENDPATH**/ ?>