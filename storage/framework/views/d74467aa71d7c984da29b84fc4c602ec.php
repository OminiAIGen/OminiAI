<?php
    if (auth()->check()) {
        $user_avatar = auth()->user()->avatar;
        if (!auth()->user()->github_token && !auth()->user()->google_token && !auth()->user()->facebook_token) {
            $user_avatar = '/' . $user_avatar;
        }
    } else {
        $user_avatar = '/assets/img/auth/default-avatar.png';
    }
?>

<div
    class="fixed inset-0 z-50 overflow-y-auto overscroll-contain bg-background"
    x-show="currentView === 'community'"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-95"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-95"
    x-data="communityGalleryFull"
>
    <div class="min-h-screen bg-gradient-to-b from-background to-foreground/5 py-8 md:py-12 lg:ps-[--navbar-width]">
        <div class="container">
            
            <div class="mb-12 flex flex-col items-center">
                <div class="grid w-full grid-cols-3 items-center gap-4">
                    <div class="flex gap-1">
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click.prevent' => 'switchView(\'<\')']); ?>
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-arrow-left'); ?>
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
                            <?php echo e(__('Back')); ?>

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

                    <div>
                        
                        <div class="mx-auto mb-2.5 size-24 overflow-hidden rounded-full md:size-[120px]">
                            <template x-if="filterUserData && filterUserData.avatar">
                                <img
                                    class="h-full w-full object-cover"
                                    :src="filterUserData.avatar"
                                    aria-hidden="true"
                                    alt=""
                                >
                            </template>
                            <template x-if="filterUserData && !filterUserData.avatar">
                                <div class="flex h-full w-full items-center justify-center bg-foreground/10">
                                    <span
                                        class="text-4xl font-bold text-foreground"
                                        x-text="filterUserData.initial"
                                    ></span>
                                </div>
                            </template>
                            <template x-if="!filterUserData">
                                <img
                                    class="h-full w-full object-cover"
                                    src="<?php echo e(custom_theme_url($user_avatar)); ?>"
                                    aria-hidden="true"
                                    alt=""
                                >
                            </template>
                        </div>

                        
                        <h2 class="text-center text-xl font-medium md:text-[23px]">
                            <template x-if="filterUserData">
                                <span x-text="`${filterUserData.name}'s <?php echo e(__('Archive')); ?>`"></span>
                            </template>
                            <template x-if="!filterUserData">
                                <span><?php echo e(__('Community Gallery')); ?></span>
                            </template>
                        </h2>
                    </div>

                    <div class="flex justify-end gap-2">
                        
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'ghost','size' => 'sm'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click.prevent' => 'clearUserFilter()','x-show' => 'filterUserId']); ?>
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
                            <?php echo e(__('Clear filter')); ?>

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
            </div>

            
            <div
                class="flex items-center justify-center py-20"
                x-show="loading && images.length === 0"
            >
                <div class="h-12 w-12 animate-spin rounded-full border-b-2 border-primary"></div>
            </div>

            <?php echo $__env->make('ai-image-pro::includes.image-grid', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            
            <p
                class="flex items-center justify-center gap-2 px-4 py-12 text-center text-xs font-medium"
                x-show="loading && images.length > 0"
            >
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
                <?php echo e(__('Loading more images...')); ?>

            </p>

            
            <p
                class="flex items-center justify-center gap-2 px-4 py-12 text-center text-xs font-medium"
                x-show="!hasMore && images.length > 0 && !loading"
            >
                <?php echo e(__('All images loaded')); ?>

                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-check'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5']); ?>
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
            </p>

            
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'tabler-photo-off','title' => __('No Images Yet'),'description' => __('The community gallery is empty. Be the first to share your creations!'),'show' => '!loading && images.length === 0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'tabler-photo-off','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('No Images Yet')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('The community gallery is empty. Be the first to share your creations!')),'show' => '!loading && images.length === 0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
        </div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('50050b61-c765-41bb-939e-9075e10e6fdc')): $__env->markAsRenderedOnce('50050b61-c765-41bb-939e-9075e10e6fdc');
$__env->startPush('script'); ?>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('communityGalleryFull', () => ({
                images: [],
                page: 1,
                loading: false,
                hasMore: true,
                filterUserId: null,
                filterUserData: null,
                galleryPrefix: 'community',

                async init() {
                    // Listen for filter event FIRST - this sets filterUserId before images load
                    window.addEventListener('filter-user-gallery', (e) => {
                        this.filterByUser(e.detail.userId, e.detail.userData);
                    });

                    this.$watch('currentView', (newView) => {
                        // Only auto-load if switching to community AND no filter is pending
                        // If filterUserId is set, filterByUser already handles loading
                        if (newView === 'community' && this.images.length === 0 && !this.filterUserId) {
                            this.loadImages();
                        }
                    });

                    this.$nextTick(() => {
                        if (this.getParentView() === 'community' && this.images.length === 0 && !this.filterUserId) {
                            this.loadImages().then(() => {
                                this.checkUrlParams();
                            });
                        }
                    });
                },

                checkUrlParams() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const imageId = urlParams.get('image');
                    const userId = urlParams.get('user');

                    if (userId) {
                        this.filterByUser(userId);
                    } else if (imageId) {
                        const image = this.images.find(img => img.id == imageId);
                        if (image) {
                            this.$nextTick(() => {
                                this.openImageModal(image, 'community');
                            });
                        }
                    }
                },

                async filterByUser(userId, userData = null) {
                    this.filterUserId = userId;
                    this.filterUserData = userData;
                    this.images = [];
                    this.page = 1;
                    this.hasMore = true;
                    await this.loadImages();

                    const url = new URL(window.location);
                    url.searchParams.set('user', userId);
                    window.history.pushState({}, '', url);
                },

                clearUserFilter() {
                    this.filterUserId = null;
                    this.filterUserData = null;
                    this.images = [];
                    this.page = 1;
                    this.hasMore = true;
                    this.loadImages();

                    const url = new URL(window.location);
                    url.searchParams.delete('user');
                    window.history.pushState({}, '', url);
                },

                getParentView() {
                    const parentComponent = this.$el.closest('.lqd-adv-img-editor');
                    if (parentComponent && parentComponent._x_dataStack) {
                        const parentData = parentComponent._x_dataStack[0];
                        return parentData?.currentView || 'home';
                    }
                    return 'home';
                },

                async loadImages() {
                    if (this.loading || !this.hasMore) return;
                    this.loading = true;

                    try {
                        const url = new URL('<?php echo e(route('ai-image-pro.community.images')); ?>');
                        url.searchParams.append('page', this.page);

                        if (this.filterUserId) {
                            url.searchParams.append('user_id', this.filterUserId);
                        }

                        const response = await fetch(url);

                        if (!response.ok) {
                            throw new Error('Failed to fetch images');
                        }

                        const data = await response.json();
                        if (data.images && data.images.length > 0) {
                            this.images = [...this.images, ...data.images];
                            this.hasMore = data.hasMore !== false;
                            this.page++;
                        } else {
                            this.hasMore = false;
                        }
                    } catch (error) {
                        console.error('Failed to load images:', error);
                        this.hasMore = false;
                    } finally {
                        this.loading = false;
                    }
                },

                loadMore() {
                    if (!this.loading && this.hasMore) {
                        this.loadImages();
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
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/home/sections/community-gallery/full.blade.php ENDPATH**/ ?>