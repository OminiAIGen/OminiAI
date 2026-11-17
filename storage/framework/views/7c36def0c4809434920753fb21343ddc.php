<?php
    $generatedContent = cache()->get('generated_content');
    $all = $generatedContent->count();
    $all = $all == 0 ? 1 : $all;

    $contents = [
        [
            'type' => 'text',
            'label' => 'Text',
            'color' => '#3C82F6',
            'percent' => round(($generatedContent->filter(fn($entity) => $entity->generator?->type == 'text')->count() / $all) * 100, 0),
        ],
        [
            'type' => 'image',
            'label' => 'Image',
            'color' => '#9F77F8',
            'percent' => round(($generatedContent->filter(fn($entity) => $entity->generator?->type == 'image')->count() / $all) * 100, 0),
        ],
        [
            'type' => 'audio',
            'label' => 'Audio',
            'color' => '#60A5FA',
            'percent' => round(($generatedContent->filter(fn($entity) => $entity->generator?->type == 'audio')->count() / $all) * 100, 0),
        ],
        [
            'type' => 'video',
            'label' => 'Video',
            'color' => '#20C69F',
            'percent' => round(($generatedContent->filter(fn($entity) => $entity->generator?->type == 'video')->count() / $all) * 100, 0),
        ],
        [
            'type' => 'code',
            'label' => 'Code',
            'color' => '#E0B43E',
            'percent' => round(($generatedContent->filter(fn($entity) => $entity->generator?->type == 'code')->count() / $all) * 100, 0),
        ],
    ];

    /**
     * sort the content using percent
     * @param array $contents
     */
    usort($contents, function ($a, $b) {
        return $a['percent'] < $b['percent'];
    });
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
<?php $component->withAttributes(['class' => 'flex flex-col','class:body' => 'flex flex-col justify-center grow','id' => ''.e('admin-card-' . ($widget?->name?->value ?? 'generated-content')).'']); ?>
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
<?php $component->withName('tabler-clipboard-text'); ?>
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
                <?php echo e(__('Generated Content')); ?>

                <?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['text' => ''.e(__('Track how much and what kind of content users are creating.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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

    <div class="flex flex-col gap-4">
        <div class="flex w-full rounded-7xl border p-2.5">
            <div class="flex h-3 w-full flex-nowrap gap-0.5 overflow-hidden rounded-7xl">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($content['percent'] != 0): ?>
                        <span style="width: <?php echo e($content['percent']); ?>%; background-color: <?php echo e($content['color']); ?>;"></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <ul class="flex flex-col gap-2.5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $contents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex items-center justify-between border-b border-card-border py-2">
                    <div class="flex items-center gap-2.5">
                        <span
                            class="size-2.5 rounded-sm"
                            style="background-color: <?php echo e($content['color']); ?>"
                        ></span>
                        <p class="mb-0 text-[15px] font-medium text-foreground"><?php echo e(__($content['label'])); ?></p>
                    </div>
                    <span class="text-center text-foreground/50"><?php echo e($content['percent']); ?>%</span>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/admin/dashboard/generated-content.blade.php ENDPATH**/ ?>