<div class="pb-12 md:pb-20">
    <div class="container max-w-5xl">
        <div
            class="mb-14"
            x-data="{ activeIndex: 0 }"
        >
            <div class="flex gap-8 border-b pb-px max-md:overflow-x-auto max-md:whitespace-nowrap md:justify-center">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $generatorsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button
                        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            '-mb-px border-b border-transparent px-2 py-4 text-2xs font-semibold text-heading-foreground opacity-60 transition [&.active]:border-b-current [&.active]:opacity-100',
                            'active' => $loop->first,
                        ]); ?>"
                        type="button"
                        @click="activeIndex = <?php echo e($loop->index); ?>"
                        :class="{ 'active': activeIndex === <?php echo e($loop->index); ?> }"
                    >
                        <?php echo e($item->menu_title); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 place-items-start">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $generatorsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div
                            class="col-start-1 col-end-1 row-start-1 row-end-1 flex w-full flex-wrap items-center justify-between gap-y-8"
                            x-show="activeIndex === <?php echo e($loop->index); ?>"
                            <?php if(!$loop->first): ?> x-cloak <?php endif; ?>
                            x-transition.opacity
                        >
                            <div class="w-full lg:w-1/2">
                                <img
                                    class="h-auto w-full rounded-xl"
                                    src="<?php echo e(custom_theme_url($item->image, true)); ?>"
                                    alt="<?php echo e(__($item->image_title)); ?>"
                                >
                            </div>

                            <div class="w-full lg:w-5/12">
                                <h5 class="mb-3.5 text-[19px] font-semibold">
                                    <?php echo $item->title; ?>

                                </h5>

                                <p class="mb-3.5 text-pretty text-[18px] leading-[1.4em]">
                                    <?php echo $item->text; ?>

                                </p>

                                
                                <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['size' => 'xl','href' => '#'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'text-sm font-medium']); ?>
                                    <?php echo e(__('Try it free')); ?>

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
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="inline-flex h-px grow bg-foreground/10"></span>
            
            <a
                class="text-[12px] text-heading-foreground"
                href="#"
            >
                <strong class="me-1.5">
                    <?php echo e(__('Need Help?')); ?>

                </strong>
                <?php echo app('translator')->get('Learn more about <u>Image Pro</u>'); ?>
            </a>
            <span class="inline-flex h-px grow bg-foreground/10"></span>
        </div>
    </div>
</div>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/home/sections/generators.blade.php ENDPATH**/ ?>