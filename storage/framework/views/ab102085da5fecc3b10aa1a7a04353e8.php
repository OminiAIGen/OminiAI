<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((! $vip_membership) && $app_is_not_demo): ?>
    <?php if (isset($component)) { $__componentOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal740c66ff9bbfcb19a96a45ba2fa42d64 = $attributes; } ?>
<?php $component = App\View\Components\Card::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Card::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'relative flex items-center border-4','class:body' => 'static rounded-[inherit] only:grow-0 lg:p-10 w-full','id' => ''.e('admin-card-' . ($widget?->name?->value ?? 'premium-advantages')).'']); ?>
        <?php if (isset($component)) { $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.outline-glow','data' => ['class' => '[--glow-color-primary:238deg_71%_79%] [--glow-color-secondary:166deg_74%_45%] [--outline-glow-iteration:2] [--outline-glow-w:4px]','effect' => '3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('outline-glow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '[--glow-color-primary:238deg_71%_79%] [--glow-color-secondary:166deg_74%_45%] [--outline-glow-iteration:2] [--outline-glow-w:4px]','effect' => '3']); ?>
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

        <div class="absolute end-8 mb-6 inline-grid size-14 place-content-center rounded-xl bg-gradient-to-br from-gradient-from via-gradient-via to-gradient-to text-white">
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-diamond'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-10','stroke-width' => '1.5']); ?>
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
        </div>
        <h3 class="mb-6">
            <?php echo app('translator')->get('Premium Advantages'); ?>
        </h3>
        <ul class="mb-11 space-y-4 self-center text-xs font-medium">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $premium_features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature => $tooltip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="flex items-center gap-3.5">
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 16 16"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            d="M2.09635 7.37072C1.80296 7.37154 1.51579 7.45542 1.26807 7.61264C1.02035 7.76986 0.822208 7.994 0.696564 8.25914C0.570919 8.52427 0.522908 8.81956 0.558084 9.11084C0.59326 9.40212 0.710186 9.67749 0.895335 9.9051L4.84228 14.7401C4.98301 14.9148 5.1634 15.0535 5.36847 15.1445C5.57353 15.2355 5.79736 15.2763 6.02136 15.2635C6.50043 15.2377 6.93295 14.9815 7.20871 14.5601L15.4075 1.35593C15.4089 1.35373 15.4103 1.35154 15.4117 1.34939C15.4886 1.23127 15.4637 0.997192 15.3049 0.850142C15.2613 0.809761 15.2099 0.778736 15.1538 0.75898C15.0977 0.739223 15.0382 0.731153 14.9789 0.735266C14.9196 0.739379 14.8618 0.755589 14.809 0.782896C14.7562 0.810204 14.7095 0.848031 14.6719 0.894048C14.669 0.897666 14.6659 0.90123 14.6628 0.904739L6.39421 10.247C6.36275 10.2826 6.32454 10.3115 6.28179 10.3322C6.23905 10.3528 6.19263 10.3648 6.14522 10.3674C6.09782 10.3699 6.05038 10.363 6.00565 10.3471C5.96093 10.3312 5.91982 10.3065 5.88471 10.2746L3.14051 7.77735C2.8555 7.51608 2.48299 7.37102 2.09635 7.37072Z"
                            fill="url(#paint0_linear_9208_560_<?php echo e($loop->index); ?>)"
                        />
                        <defs>
                            <linearGradient
                                id="paint0_linear_9208_560_<?php echo e($loop->index); ?>"
                                x1="0.546875"
                                y1="3.69866"
                                x2="12.7738"
                                y2="14.7613"
                                gradientUnits="userSpaceOnUse"
                            >
                                <stop stop-color="hsl(var(--gradient-from))" />
                                <stop
                                    offset="0.502"
                                    stop-color="hsl(var(--gradient-via))"
                                />
                                <stop
                                    offset="1"
                                    stop-color="hsl(var(--gradient-to))"
                                />
                            </linearGradient>
                        </defs>
                    </svg>
                    <?php echo $feature; ?> <?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['text' => $tooltip] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
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
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </ul>

        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => '/subscription','variant' => 'outline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-full shadow-md shadow-black/[7%] hover:bg-primary hover:text-primary-foreground hover:shadow-xl hover:shadow-primary/20 hover:outline-primary']); ?>
            <?php echo app('translator')->get('Join Premium'); ?>
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
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/admin/dashboard/premium-advantages.blade.php ENDPATH**/ ?>