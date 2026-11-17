<div x-data="aiImageProImageStyleModal(input)">
    <?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:modal-content' => 'flex flex-col w-[min(calc(100%-40px),1140px)] min-h-[min(90vh,850px)]','class' => 'static','class:modal-head' => 'hidden','class:modal-body' => 'p-5 lg:px-10 grow flex flex-col','class:modal-container' => 'max-w-none w-full flex flex-col grow','class:modal-backdrop' => 'backdrop-blur bg-black/50']); ?>
         <?php $__env->slot('trigger', null, ['class' => 'whitespace-nowrap text-center outline-2 outline-foreground/5 hover:bg-primary hover:text-primary-foreground hover:outline-primary','variant' => 'outline']); ?> 
            <span x-text="selectedStyle || '<?php echo e(__('Style')); ?>'">
                <?php echo e(__('Style')); ?>

            </span>
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('modal', null, []); ?> 
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'sticky top-0 z-10 -mx-5 px-5 lg:-mx-10 lg:px-10',
                'bg-background' => ($theme ?? null) !== 'social-media-agent-dashboard',
                'bg-surface-background' => ($theme ?? null) === 'social-media-agent-dashboard',
            ]); ?>">
                <div class="flex gap-5 border-b">
                    <button
                        class="-mb-px border-b border-b-transparent p-0 py-2.5 text-start text-[12px] font-semibold text-heading-foreground opacity-50 [&.active]:border-b-current [&.active]:opacity-100"
                        variant="none"
                        type="button"
                        :class="{ 'active': activeTab === 'pick' }"
                        @click="activeTab = 'pick'"
                    >
                        <?php echo e(__('Pick a Style')); ?>

                    </button>

                    <button
                        class="-mb-px border-b border-b-transparent p-0 py-2.5 text-start text-[12px] font-semibold text-heading-foreground opacity-50 [&.active]:border-b-current [&.active]:opacity-100"
                        variant="none"
                        type="button"
                        :class="{ 'active': activeTab === 'favorites' }"
                        @click="activeTab = 'favorites'"
                    >
                        <?php echo e(__('Favorites')); ?>

                    </button>

                    <button
                        class="ms-auto inline-grid size-10 place-content-center"
                        type="button"
                        @click.prevent="modalOpen = false"
                    >
                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-x'); ?>
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
                    </button>
                </div>
            </div>

            
            <div
                class="grid grow grid-cols-2 gap-4 py-4 sm:grid-cols-3 md:gap-5 lg:grid-cols-5"
                x-show="!isLoading && itemsToShow.length"
            >
                <template
                    x-for="style in itemsToShow"
                    :key="style.styleId"
                >
                    <div
                        class="group relative cursor-pointer before:pointer-events-none before:absolute before:-inset-3 before:z-0 before:scale-95 before:rounded-[10px] before:bg-foreground/10 before:opacity-0 before:transition [&.selected]:before:scale-100 [&.selected]:before:opacity-100"
                        @click="selectStyle(style); modalOpen = false;"
                        :class="{ 'selected': selectedStyleId === style.styleId }"
                    >
                        
                        <template x-if="style.isAddCard && input?.props?.refImageAllowed">
                            <div class="group relative z-1 w-full">
                                <div
                                    class="relative mb-2 inline-grid aspect-square w-full place-items-center rounded-lg bg-foreground/5 shadow-md shadow-black/15 transition group-hover:scale-105 group-hover:shadow-xl group-hover:shadow-black/10">
                                    <div class="inline-grid size-[42px] place-items-center rounded-full bg-foreground/80 text-background transition group-hover:scale-110">
                                        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-plus'); ?>
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
                                    </div>
                                </div>

                                <p
                                    class="mb-0.5 text-[11px] font-medium opacity-50"
                                    x-text="style.category"
                                ></p>
                                <p
                                    class="m-0 text-[13px] font-medium"
                                    x-text="style.styleName"
                                ></p>
                            </div>
                        </template>

                        
                        <div
                            class="group relative z-1 w-full"
                            x-show="!style.isAddCard"
                        >
                            
                            <div
                                class="relative mb-2 inline-grid aspect-square w-full place-items-center overflow-hidden rounded-lg bg-foreground/5 shadow-md shadow-black/15 transition group-hover:scale-105 group-hover:shadow-xl group-hover:shadow-black/10">
                                <img
                                    class="size-full scale-105 object-cover transition group-hover:scale-100"
                                    :src="style.previewImage"
                                    :alt="style.styleName"
                                    loading="lazy"
                                />

                                
                                <button
                                    class="absolute end-3 top-3 z-1 inline-grid size-6 place-items-center rounded-full bg-background opacity-0 transition hover:scale-110 group-hover:opacity-100"
                                    type="button"
                                    @click="toggleFavorite(style.styleId, $event)"
                                    :class="isFavorite(style.styleId) ? 'text-red-500' : 'text-foreground/40'"
                                >
                                    <svg
                                        class="size-[18px] text-heading-foreground"
                                        :fill="isFavorite(style.styleId) ? 'currentColor' : 'none'"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                        />
                                    </svg>
                                </button>
                            </div>

                            
                            <p
                                class="mb-0.5 text-[11px] font-medium opacity-50"
                                x-text="style.category || 'Impressions'"
                            ></p>
                            <p
                                class="m-0 text-[13px] font-medium"
                                x-text="style.styleName"
                            ></p>
                        </div>
                    </div>
                </template>
            </div>

            
            <div
                class="flex grow flex-col items-center justify-center py-10 text-center"
                x-show="styleSearch.trim() && !isLoading && !itemsToShow.length"
            >
                <div class="mb-3 inline-grid size-28 place-items-center rounded-full bg-foreground/5">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-search-off'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-10']); ?>
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
                <p class="mb-0 text-base font-medium text-heading-foreground">
                    <?php echo e(__('No styles found matching your search')); ?>

                </p>
                <p class="mb-0 text-sm font-medium text-heading-foreground/50">
                    <?php echo e(__('Try adjusting your search term')); ?>

                </p>
            </div>

            
            <div
                class="flex grow flex-col items-center justify-center py-10 text-center"
                x-show="!isLoading && activeTab === 'favorites' && !itemsToShow.length && !styleSearch.trim()"
            >
                <div class="mb-3 inline-grid size-28 place-items-center rounded-full bg-foreground/5">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-heart-off'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-10']); ?>
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
                <p class="mb-1 text-base font-medium text-heading-foreground">
                    <?php echo e(__('No favorite styles yet')); ?>

                </p>
                <p class="mb-0 text-sm font-medium text-heading-foreground/50">
                    <?php echo e(__('Click the heart icon on any style to add it to favorites')); ?>

                </p>
            </div>

            
            <div
                class="flex grow flex-col items-center justify-center py-10 text-center"
                x-show="isLoading"
            >
                <p class="mb-0 flex items-center gap-2 text-base font-medium text-heading-foreground">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-loader-2'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 shrink-0 animate-spin']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                    <?php echo e(__('Loading styles...')); ?>

                </p>
            </div>

            <div class="sticky inset-x-0 bottom-7 z-2">
                <div class="relative mx-auto w-full max-w-[760px]">
                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'pointer-events-none absolute start-6 top-1/2 z-1 size-4 -translate-y-1/2 text-heading-foreground']); ?>
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
                    <input
                        class="h-16 w-full rounded-full bg-background/60 ps-14 shadow-2xl shadow-black/20 backdrop-blur-lg placeholder:text-foreground"
                        type="text"
                        placeholder="<?php echo e(__('Search for styles')); ?>"
                        x-model="styleSearch"
                    />
                </div>
            </div>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $attributes = $__attributesOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__attributesOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale6a555649da86b3de44465cdfe004aa4)): ?>
<?php $component = $__componentOriginale6a555649da86b3de44465cdfe004aa4; ?>
<?php unset($__componentOriginale6a555649da86b3de44465cdfe004aa4); ?>
<?php endif; ?>

    
    <input
        type="hidden"
        name="style"
        x-model="selectedStyle"
    >
    <input
        type="hidden"
        name="style_id"
        x-model="selectedStyleId"
    >

    
    <input
        class="hidden"
        type="file"
        <?php if(!auth()->check()): ?> data-exclude-media-manager="true" <?php endif; ?>
        x-ref="styleReferenceInput"
        @change="handleStyleReferenceUpload($event)"
        accept="image/*"
        name="style_reference"
    >
</div>

<?php $__env->startPush('script'); ?>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('aiImageProImageStyleModal', (input) => ({
                input,
                selectedStyle: null,
                selectedStyleId: null,
                showModal: false,
                styleSearch: '',
                activeTab: 'pick',
                styles: [],
                favorites: [],
                isLoading: true,
                styleReferenceFile: null,
                async init() {
                    try {
                        // Load styles from JSON file
                        const response = await fetch('/vendor/ai-image-pro/styles.json');
                        const data = await response.json();
                        this.styles = data.styles;
                    } catch (error) {
                        console.error('Failed to load styles:', error);
                        // Fallback to empty array or show error message
                        this.styles = [];
                    } finally {
                        this.isLoading = false;
                    }

                    // Load favorites from localStorage if available
                    const savedFavorites = localStorage.getItem('styleFavorites');
                    if (savedFavorites) {
                        this.favorites = JSON.parse(savedFavorites);
                    }
                },
                get itemsToShow() {
                    const refImageAllowed = this.input?.props?.refImageAllowed;
                    return this.activeTab === 'pick' ?
                        this.filteredStyles.filter(s => refImageAllowed || !s.isAddCard) :
                        this.filteredStyles.filter(s => this.isFavorite(s.styleId) && (!s.isAddCard || refImageAllowed))
                },
                get filteredStyles() {
                    let filtered = this.styles;
                    // Filter by search
                    if (this.styleSearch) {
                        filtered = filtered.filter(style =>
                            style.styleName.toLowerCase().includes(this.styleSearch.toLowerCase()) ||
                            (style.description && style.description.toLowerCase().includes(this.styleSearch.toLowerCase())) ||
                            (style.category && style.category.toLowerCase().includes(this.styleSearch.toLowerCase()))
                        );
                    }
                    return filtered;
                },
                selectStyle(style) {
                    if (style.isAddCard) {
                        if (!this.input?.props?.refImageAllowed) return;
                        this.$refs.styleReferenceInput.click();
                        return;
                    }
                    this.selectedStyle = style.styleName;
                    this.selectedStyleId = style.styleId;
                    this.showModal = false;
                },
                handleStyleReferenceUpload(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.styleReferenceFile = file;
                        this.selectedStyle = file.name;
                        this.selectedStyleId = 'custom-reference';
                        this.showModal = false;
                    }
                },
                toggleFavorite(styleId, event) {
                    event.stopPropagation();
                    const index = this.favorites.indexOf(styleId);
                    if (index > -1) {
                        this.favorites.splice(index, 1);
                    } else {
                        this.favorites.push(styleId);
                    }
                    // Save to localStorage
                    localStorage.setItem('styleFavorites', JSON.stringify(this.favorites));
                },
                isFavorite(styleId) {
                    return this.favorites.includes(styleId);
                }
            }))
        })
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/home/image-style-modal.blade.php ENDPATH**/ ?>