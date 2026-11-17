<div
    class="pb-20"
    x-show="images.length"
    x-cloak
    x-data="galleryPreview"
>
    
    <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <h2>
            <span class="block text-[0.7em] opacity-50">
                <?php echo e(__('Hello') . ' ' . auth()?->user()?->name); ?>,
            </span>
            <?php echo e(__('Get inspired')); ?>

        </h2>

        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => '#','variant' => 'link'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click.prevent' => 'switchView(\'community\')']); ?>
            <?php echo e(__('View All')); ?>

            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-arrow-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 transition group-hover:translate-x-1 rtl:rotate-180']); ?>
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
    </div>

    
    <?php if (isset($component)) { $__componentOriginalb3ec01e52e27892fef51b672e40a5969 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb3ec01e52e27892fef51b672e40a5969 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.masonry-grid','data' => ['class' => '-mx-0.5 flex flex-wrap','xRef' => 'masonryGrid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('masonry-grid'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '-mx-0.5 flex flex-wrap','x-ref' => 'masonryGrid']); ?>
        <template
            x-for="(image, index) in images"
            
            
        >
            <?php if (isset($component)) { $__componentOriginal768a4b514ba05d1a91411e6411599a1e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal768a4b514ba05d1a91411e6411599a1e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.masonry-grid-item','data' => ['class' => 'mb-0.5 w-1/2 px-px md:w-1/3']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('masonry-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-0.5 w-1/2 px-px md:w-1/3']); ?>
                <?php echo $__env->make('ai-image-pro::includes.image-grid-item', ['aspect_ratio' => 'dynamic'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <template x-if="index === images.length - 1">
                    <div x-init="$nextTick(() => {
                        if ($refs.masonryGrid.classList.contains('masonry-grid-initialized')) {
                            $dispatch('masonry:layout');
                        } else {
                            $dispatch('masonry:init');
                        }
                        $el.remove();
                    });"></div>
                </template>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal768a4b514ba05d1a91411e6411599a1e)): ?>
<?php $attributes = $__attributesOriginal768a4b514ba05d1a91411e6411599a1e; ?>
<?php unset($__attributesOriginal768a4b514ba05d1a91411e6411599a1e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal768a4b514ba05d1a91411e6411599a1e)): ?>
<?php $component = $__componentOriginal768a4b514ba05d1a91411e6411599a1e; ?>
<?php unset($__componentOriginal768a4b514ba05d1a91411e6411599a1e); ?>
<?php endif; ?>
        </template>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb3ec01e52e27892fef51b672e40a5969)): ?>
<?php $attributes = $__attributesOriginalb3ec01e52e27892fef51b672e40a5969; ?>
<?php unset($__attributesOriginalb3ec01e52e27892fef51b672e40a5969); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb3ec01e52e27892fef51b672e40a5969)): ?>
<?php $component = $__componentOriginalb3ec01e52e27892fef51b672e40a5969; ?>
<?php unset($__componentOriginalb3ec01e52e27892fef51b672e40a5969); ?>
<?php endif; ?>

    <div class="mt-20 flex justify-center">
        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['href' => '#','variant' => 'outline','hoverVariant' => 'primary'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'rounded-xl text-sm leading-relaxed outline-2','@click.prevent' => 'switchView(\'community\')']); ?>
            <?php echo e(__('View More')); ?>

            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-arrow-right'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 transition group-hover:translate-x-1 rtl:rotate-180']); ?>
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
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('27287ad2-caaf-4c84-8988-536636bd3877')): $__env->markAsRenderedOnce('27287ad2-caaf-4c84-8988-536636bd3877');
$__env->startPush('script'); ?>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('galleryPreview', () => ({
                images: [],
                galleryPrefix: 'preview',

                async init() {
                    await this.loadImages();
                },

                async loadImages() {
                    try {
                        const response = await fetch('<?php echo e(route('ai-image-pro.community.images')); ?>?page=1');
                        if (!response.ok) {
                            throw new Error('Failed to fetch images');
                        }
                        const data = await response.json();
                        this.images = data.images || data;
                    } catch (error) {
                        console.error('Failed to load images:', error);
                    }
                },

                openImageModal(image, prefix) {
                    const parentComponent = this.$el.closest('.lqd-adv-img-editor');
                    if (parentComponent && parentComponent._x_dataStack) {
                        const parentData = parentComponent._x_dataStack[0];
                        if (parentData.setActiveModal) {
                            parentData.setActiveModal(image, prefix || this.galleryPrefix);
                        }
                    }
                },

                toggleFavorite(imageId) {
                    const parentComponent = this.$el.closest('.lqd-adv-img-editor');
                    if (parentComponent && parentComponent._x_dataStack) {
                        const parentData = parentComponent._x_dataStack[0];
                        if (parentData.toggleFavorite) {
                            parentData.toggleFavorite(imageId);
                        }
                    }
                },

                isFavorite(imageId) {
                    const parentComponent = this.$el.closest('.lqd-adv-img-editor');
                    if (parentComponent && parentComponent._x_dataStack) {
                        const parentData = parentComponent._x_dataStack[0];
                        return parentData.favorites?.includes(imageId) || false;
                    }
                    return false;
                },

                switchView(view) {
                    const parentComponent = this.$el.closest('.lqd-adv-img-editor');
                    if (parentComponent && parentComponent._x_dataStack) {
                        const parentData = parentComponent._x_dataStack[0];
                        if (parentData.switchView) {
                            parentData.switchView(view);
                        }
                    }
                },

                showNotification(message, type = 'info') {
                    if (window.toastr) {
                        window.toastr[type](message);
                    }
                }
            }));
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/home/sections/community-gallery/preview.blade.php ENDPATH**/ ?>