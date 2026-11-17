<?php
    $templates_list_url = url('/vendor/ai-image-pro/templates/templates.json?v=' . time());
?>

<div
    class="flex h-full flex-wrap items-start gap-6"
    x-data="marketingTemplates"
>
    
    <div class="sticky top-0 flex w-full shrink-0 gap-x-4 gap-y-4 overflow-y-auto max-md:overflow-x-auto md:w-52 md:flex-col">
        <template
            x-for="category in categories"
            :key="category"
        >
            <button
                class="group text-start text-base font-medium text-heading-foreground transition-all max-md:whitespace-nowrap"
                @click="selectedCategory = category"
                :class="{ 'active': selectedCategory === category }"
            >
                <span
                    class="group-[&.active]:font-semibold group-[&.active]:underline"
                    x-text="category === 'all' ? '<?php echo e(__('All')); ?>' : category"
                ></span>
                <sup
                    class="ms-1 text-4xs no-underline"
                    x-show="selectedCategory === category"
                    x-text="getCategoryCount(category)"
                ></sup>
            </button>
        </template>
    </div>

    
    <div class="flex-1">
        
        <div
            class="flex items-center justify-center py-20"
            x-show="loadingTemplatesFailed"
            x-cloak
        >
            <div class="text-center">
                <div class="mx-auto mb-3 grid size-28 place-items-center rounded-full bg-foreground/5">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-alert-circle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-10 text-foreground']); ?>
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
                <p class="mb-1 text-lg font-medium text-heading-foreground">
                    <?php echo e(__('Failed to load templates')); ?>

                </p>
                <p class="mb-3 opacity-70">
                    <?php echo e(__('Please try again later')); ?>

                </p>

                <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click.prevent' => 'retryFetch()']); ?>
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-refresh'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4',':class' => '{ \'animate-spin\': loading }']); ?>
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
                    <?php echo e(__('Retry')); ?>

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

        
        <div
            class="flex items-center justify-center py-20"
            x-show="!templatesList.length && !loadingTemplatesFailed"
        >
            <p class="flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 animate-spin']); ?>
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
                <?php echo e(__('Loading Templates')); ?>

            </p>
        </div>

        
        <?php if (isset($component)) { $__componentOriginalb3ec01e52e27892fef51b672e40a5969 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb3ec01e52e27892fef51b672e40a5969 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.masonry-grid','data' => ['class' => '-mx-2 flex flex-wrap','xRef' => 'masonryGrid','xShow' => 'templatesList.length','xCloak' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('masonry-grid'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '-mx-2 flex flex-wrap','x-ref' => 'masonryGrid','x-show' => 'templatesList.length','x-cloak' => true]); ?>
            <template
                x-for="(template, index) in filteredTemplates"
                
                
            >
                <?php if (isset($component)) { $__componentOriginal768a4b514ba05d1a91411e6411599a1e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal768a4b514ba05d1a91411e6411599a1e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.masonry-grid-item','data' => ['class' => 'mb-4 w-1/2 px-2 md:w-1/3',':dataCategory' => 'template.category']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('masonry-grid-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-4 w-1/2 px-2 md:w-1/3',':data-category' => 'template.category']); ?>
                    <a
                        class="group relative flex overflow-hidden rounded-lg shadow-md transition-all hover:scale-[1.02] hover:shadow-xl"
                        href="#"
                        @click.prevent="sessionStorage.setItem('pendingTemplate', JSON.stringify({id: template.id, preview: template.preview, name: template.name}));window.location.href = '/dashboard/user/creative-suite';"
                    >
                        <img
                            class="masonry-image h-auto w-full"
                            alt="<?php echo e(__('Template Preview')); ?>"
                            :src="template.preview"
                            :width="template.preview_dimensions.width"
                            :height="template.preview_dimensions.height"
                            loading="lazy"
                        >

                        
                        <span class="absolute inset-0 flex items-center justify-center bg-black/40 opacity-0 transition-opacity group-hover:opacity-100">
                            <span class="flex size-12 items-center justify-center rounded-full bg-white text-black">
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-plus'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6']); ?>
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
                        </span>
                    </a>

                    <template x-if="index === filteredTemplates.length - 1">
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

        
        <div
            class="flex items-center justify-center py-20"
            x-show="templatesList.length && !filteredTemplates.length"
            x-cloak
        >
            <p>
                <?php echo e(__('No templates found in this category')); ?>

            </p>
        </div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('93e76865-ade0-4072-aea6-a3df00b423ad')): $__env->markAsRenderedOnce('93e76865-ade0-4072-aea6-a3df00b423ad');
$__env->startPush('script'); ?>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('marketingTemplates', () => ({
                templatesList: [],
                loadingTemplatesFailed: false,
                templatesListUrl: '<?php echo e($templates_list_url); ?>',
                selectedCategory: 'all',
                loading: false,

                init() {
                    this.fetchTemplates();
                },

                fetchTemplates() {
                    this.loading = true;
                    this.loadingTemplatesFailed = false;

                    fetch(this.templatesListUrl)
                        .then(res => res.json())
                        .then(data => this.templatesList = data)
                        .catch(() => this.loadingTemplatesFailed = true)
                        .finally(() => this.loading = false);
                },

                get categories() {
                    if (!this.templatesList.length) return ['all'];
                    const cats = new Set(['all']);
                    this.templatesList.forEach(t => {
                        if (t.category) cats.add(t.category);
                    });
                    return Array.from(cats);
                },

                get filteredTemplates() {
                    if (this.selectedCategory === 'all') {
                        return this.templatesList;
                    }
                    return this.templatesList.filter(t => t.category === this.selectedCategory);
                },

                getCategoryCount(category) {
                    if (category === 'all') return this.templatesList.length;
                    return this.templatesList.filter(t => t.category === category).length;
                },

                retryFetch() {
                    this.loadingTemplatesFailed = false;
                    this.fetchTemplates();
                }
            }));
        });
    </script>
<?php $__env->stopPush(); endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/home/sections/partials/modals/marketing.blade.php ENDPATH**/ ?>