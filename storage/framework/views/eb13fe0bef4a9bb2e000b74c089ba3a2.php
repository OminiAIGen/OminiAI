<div class="py-12 md:pb-32 md:pt-20">
    <div class="container max-w-5xl">
        <div class="mx-auto mb-12 text-center md:w-1/2">
            <h2 class="m-0">
                <span class="block">
                    <span class="text-[0.7em] opacity-60">
                        <?php echo app('translator')->get('Have a question?'); ?>
                    </span>
                    👋
                </span>
                <?php echo app('translator')->get('We’ve got you covered.'); ?>
            </h2>
        </div>

        <div x-data="{ activeIndex: -1 }">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $faq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div
                    class="group border-b"
                    :class="{ 'active': activeIndex == <?php echo e($loop->index); ?> }"
                >
                    <h4 class="m-0">
                        <button
                            class="flex w-full items-center justify-between gap-1 px-5 py-8 text-start text-base font-medium leading-tight"
                            type="button"
                            @click="activeIndex = activeIndex == <?php echo e($loop->index); ?> ? -1 : <?php echo e($loop->index); ?>"
                        >
                            <?php echo e($item->question); ?>


                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 shrink-0 transition-transform group-[&.active]:rotate-180']); ?>
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
                        </button>
                    </h4>
                    <div
                        x-show="activeIndex == <?php echo e($loop->index); ?>"
                        x-collapse
                        x-cloak
                    >
                        <div class="px-5 pb-7">
                            <p>
                                <?php echo e($item->answer); ?>

                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/home/sections/faq.blade.php ENDPATH**/ ?>