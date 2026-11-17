<?php
    $menu_items = app(App\Services\Common\FrontMenuService::class)->generate();
?>

<header
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        'site-header group/header absolute inset-x-0 top-0 z-50 transition-[background,shadow]',
    ]); ?>"
    x-data="{
        windowScrollY: window.scrollY,
        navbarHeight: $refs.navbar.offsetHeight,
        get sections() {
            return [...document.querySelectorAll('.site-section')].map(section => {
                const rect = section.getBoundingClientRect();
                return {
                    el: section,
                    rect: {
                        top: rect.top + this.windowScrollY,
                        bottom: rect.bottom + this.windowScrollY,
                        height: rect.height,
                    },
                    isDark: section.getAttribute('data-color-scheme') === 'dark'
                }
            })
        },
        checkColorScheme: function() {
            const sectionBehindNavbar = this.sections.find(section => {
                return (
                    section.rect.top <= this.windowScrollY + this.navbarHeight &&
                    section.rect.bottom >= this.windowScrollY + this.navbarHeight
                );
            });
            if (sectionBehindNavbar) {
                $el.classList.toggle('is-dark', sectionBehindNavbar.isDark)
            }
        },
        checkScroll() {
            this.windowScrollY = window.scrollY;
        }
    }"
    x-init="checkColorScheme();"
    @scroll.window="checkScroll"
    @scroll.window.throttle.50ms="checkColorScheme"
    @scroll.window.debounce.100ms="checkColorScheme"
>
    <?php echo $__env->renderWhen($fSectSettings->preheader_active, 'landing-page.header.preheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <nav
        class="site-header-nav relative z-10 py-[18px] transition-colors duration-500 group-[.lqd-is-sticky]/header:fixed group-[.lqd-is-sticky]/header:top-0 group-[.lqd-is-sticky]/header:w-full max-sm:py-3"
        id="frontend-local-navbar"
        x-ref="navbar"
        x-init="ScrollTrigger.create({ trigger: $el, pin: true, start: 'top top', end: 'max', pinSpacing: false })"
    >
        <div class="container">
            <div
                class="relative flex items-center justify-between rounded-xl p-4 shadow-[0_44px_44px_hsl(0_0%_0%/5%),inset_0_0_33px_hsl(0_0%_100%/30%)] transition-all duration-300 before:pointer-events-none before:absolute before:-inset-px before:rounded-[13px] before:bg-gradient-to-b before:from-white before:to-transparent before:p-px before:opacity-40 before:![mask-composite:exclude] before:[mask:linear-gradient(#fff_0_0)_content-box,linear-gradient(#fff_0_0)] group-[&.is-dark]/header:bg-black/10 group-[&.is-dark]/header:shadow-[0_44px_44px_hsl(0_0%_0%/5%),inset_0_0_33px_hsl(0_0%_0%/30%)] group-[&.is-dark]/header:before:from-white/30 max-xl:gap-10 max-lg:gap-5 lg:bg-white/10 lg:px-5 lg:py-4">

                <?php if (isset($component)) { $__componentOriginal0771f55ab00a25b66c062c4a13450933 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0771f55ab00a25b66c062c4a13450933 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.progressive-blur','data' => ['class' => 'h-full rounded-xl group-[&.is-dark]/header:[--background:315_29%_3%]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('progressive-blur'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'h-full rounded-xl group-[&.is-dark]/header:[--background:315_29%_3%]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0771f55ab00a25b66c062c4a13450933)): ?>
<?php $attributes = $__attributesOriginal0771f55ab00a25b66c062c4a13450933; ?>
<?php unset($__attributesOriginal0771f55ab00a25b66c062c4a13450933); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0771f55ab00a25b66c062c4a13450933)): ?>
<?php $component = $__componentOriginal0771f55ab00a25b66c062c4a13450933; ?>
<?php unset($__componentOriginal0771f55ab00a25b66c062c4a13450933); ?>
<?php endif; ?>

                <a
                    class="site-logo relative shrink-0 grow"
                    href="<?php echo e(route('index')); ?>"
                >
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($setting->logo_dark)): ?>
                        <img
                            class="peer absolute start-0 top-1/2 -translate-y-1/2 opacity-0 transition-all duration-300 group-[.is-dark]/header:opacity-100"
                            src="<?php echo e(custom_theme_url($setting->logo_dark_path, true)); ?>"
                            <?php if(isset($setting->logo_dark_2x_path)): ?> srcset="/<?php echo e($setting->logo_dark_2x_path); ?> 2x" <?php endif; ?>
                            alt="<?php echo e(custom_theme_url($setting->site_name)); ?> logo"
                        >
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <img
                        class="transition-all duration-300 group-[.is-dark]/header:peer-first:opacity-0"
                        src="<?php echo e(custom_theme_url($setting->logo_path, true)); ?>"
                        <?php if(isset($setting->logo_2x_path)): ?> srcset="/<?php echo e($setting->logo_2x_path); ?> 2x" <?php endif; ?>
                        alt="<?php echo e($setting->site_name); ?> logo"
                    >
                </a>

                <div
                    class="site-nav-container grow text-sm font-semibold transition-all max-lg:absolute max-lg:start-0 max-lg:top-full max-lg:mt-2 max-lg:max-h-0 max-lg:w-full max-lg:overflow-hidden max-lg:rounded-xl max-lg:bg-white/5 max-lg:backdrop-blur-2xl max-lg:group-[&.is-dark]/header:hover:bg-black/5 lg:max-w-[60%] [&.lqd-is-active]:max-lg:max-h-[calc(100vh-150px)]">
                    <div class="max-lg:max-h-[inherit] max-lg:overflow-y-scroll max-lg:overscroll-contain max-lg:p-5">
                        <ul class="flex flex-col items-center justify-between gap-3 whitespace-nowrap text-center lg:flex-row lg:gap-5 xl:gap-10">
                            <?php
                                $setting->menu_options = $setting->menu_options
                                    ? $setting->menu_options
                                    : '[{"title": "Home","url": "#banner","target": false},{"title": "Features","url": "#features","target": false},{"title": "How it Works","url": "#how-it-works","target": false},{"title": "Testimonials","url": "#testimonials","target": false},{"title": "Pricing","url": "#pricing","target": false},{"title": "FAQ","url": "#faq","target": false}]';
                                $menu_options = json_decode($setting->menu_options, true);
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $menu_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu_item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $has_children = !empty($menu_item['mega_menu_id']);
                                ?>
                                <li
                                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                        'group/li relative flex w-full items-center justify-between gap-2 after:pointer-events-none after:absolute after:-inset-x-4 after:bottom-[calc(var(--sub-offset,0)*-1)] after:top-full max-lg:flex-wrap lg:justify-center [&.is-hover]:after:pointer-events-auto',
                                        'has-children' => $has_children,
                                        'has-mega-menu' => !empty($menu_item['mega_menu_id']),
                                    ]); ?>"
                                    x-data="{ hover: false }"
                                    x-on:mouseover="if(window.innerWidth < 992 ) return; hover = true"
                                    x-on:mouseleave="if(window.innerWidth < 992 ) return; hover = false"
                                    :class="{ 'is-hover': hover }"
                                >
                                    <a
                                        class="group/link relative flex items-center justify-center gap-1.5 text-black/40 transition-colors hover:text-black group-[&.is-dark]/header:text-white/40 group-[&.is-dark]/header:hover:text-white max-lg:ps-5 [&.active]:text-black group-[&.is-dark]/header:[&.active]:text-white"
                                        href="<?php echo e($menu_item['url']); ?>"
                                        <?php if($menu_item['target']): ?> target="_blank" <?php endif; ?>
                                    >
                                        <svg
                                            class="pointer-events-none invisible absolute start-0 top-1/2 shrink-0 -translate-y-1/2 translate-x-[3px] rotate-45 opacity-0 transition-all group-hover/link:visible group-hover/link:translate-x-[-15px] group-hover/link:rotate-0 group-hover/link:opacity-100 group-[&.active]/link:visible group-[&.active]/link:translate-x-[-15px] group-[&.active]/link:rotate-0 group-[&.active]/link:opacity-100 max-lg:start-4"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 13 12"
                                        >
                                            <use href="#menu-active-ind" />
                                        </svg>
                                        <?php echo e(__($menu_item['title'])); ?>

                                    </a>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($has_children): ?>
                                        <span
                                            class="relative inline-grid size-8 shrink-0 place-content-center align-middle text-black before:absolute before:inset-0 before:rounded-xl before:bg-current before:opacity-5 group-[&.is-dark]/header:text-white lg:hidden"
                                            @click="hover = !hover"
                                        >
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-down'); ?>
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
                                        </span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($menu_item['mega_menu_id'])): ?>
                                        <?php echo $__env->first(['mega-menu::partials.frontend-megamenu', 'vendor.empty'], ['menu_item' => $menu_item], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </ul>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count(explode(',', $settings_two->languages)) > 1): ?>
                            <div class="group relative mt-4 block border-t border-black/5 py-5 group-[&.is-dark]/header:border-white/5 md:hidden">
                                <p class="mb-3 flex items-center gap-2 group-[&.is-dark]/header:text-white/50">
                                    
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" > <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M3.6 9h16.8"></path> <path d="M3.6 15h16.8"></path> <path d="M11.5 3a17 17 0 0 0 0 18"></path> <path d="M12.5 3a17 17 0 0 1 0 18"></path> </svg>
									
                                    <?php echo e(__('Languages')); ?>

                                </p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($localeCode, explode(',', $settings_two->languages))): ?>
                                        <a
                                            class="block py-3 transition-colors group-[&.is-dark]/header:text-white/50"
                                            href="<?php echo e(route('language.change', $localeCode)); ?>"
                                            rel="alternate"
                                            hreflang="<?php echo e($localeCode); ?>"
                                        ><?php echo e(country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1))); ?>

                                            <?php echo e($properties['native']); ?></a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="flex grow justify-end gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count(explode(',', $settings_two->languages)) > 1): ?>
                        <div class="group relative hidden md:block">
                            <button
                                class="relative inline-flex size-11 items-center justify-center rounded-full border-[3px] border-foreground/5 text-foreground/30 transition-all before:absolute before:-bottom-5 before:top-full before:w-full hover:scale-105 hover:border-background hover:bg-background hover:text-foreground hover:shadow-lg hover:shadow-black/5 group-[&.is-dark]/header:border-white/5 group-[&.is-dark]/header:text-white/50 group-[&.is-dark]/header:hover:text-foreground"
                            >
                                
								<svg class="relative z-1" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" > <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path> <path d="M3.6 9h16.8"></path> <path d="M3.6 15h16.8"></path> <path d="M11.5 3a17 17 0 0 0 0 18"></path> <path d="M12.5 3a17 17 0 0 1 0 18"></path> </svg>
								
                            </button>
                            <div
                                class="pointer-events-none absolute end-0 top-[calc(100%+1rem)] min-w-[145px] translate-y-2 rounded-md bg-white text-black opacity-0 shadow-lg transition-all group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = LaravelLocalization::getSupportedLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $localeCode => $properties): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($localeCode, explode(',', $settings_two->languages))): ?>
                                        <a
                                            class="block border-b border-black border-opacity-5 px-3 py-3 transition-colors last:border-none hover:bg-black hover:bg-opacity-5"
                                            href="<?php echo e(route('language.change', $localeCode)); ?>"
                                            rel="alternate"
                                            hreflang="<?php echo e($localeCode); ?>"
                                        ><?php echo e(country2flag(substr($properties['regional'], strrpos($properties['regional'], '_') + 1))); ?>

                                            <?php echo e($properties['native']); ?></a>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a
                            class="group relative inline-flex whitespace-nowrap rounded-full border-[3px] border-foreground/5 px-3.5 py-3 text-xs/none font-semibold text-foreground transition-all hover:scale-105 hover:border-background hover:bg-background hover:text-foreground hover:shadow-lg hover:shadow-black/5 group-[&.is-dark]/header:text-white group-[&.is-dark]/header:hover:text-foreground"
                            href="<?php echo e(route('dashboard.index')); ?>"
                        >
                            <?php if (isset($component)) { $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.outline-glow','data' => ['class' => 'lqd-outline-glow-custom [--outline-glow-w:3px] group-hover:opacity-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('outline-glow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lqd-outline-glow-custom [--outline-glow-w:3px] group-hover:opacity-0']); ?>
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
                            <span class="relative z-1">
                                <?php echo __('Dashboard'); ?>

                            </span>
                        </a>
                    <?php else: ?>
                        <a
                            class="group relative hidden whitespace-nowrap rounded-full border-[3px] border-foreground/5 px-3.5 py-3 text-xs/none font-semibold text-foreground/40 transition-all hover:scale-105 hover:border-background hover:bg-background hover:text-foreground hover:shadow-lg hover:shadow-black/5 group-[&.is-dark]/header:border-white/5 group-[&.is-dark]/header:text-white group-[&.is-dark]/header:hover:text-foreground sm:inline-flex"
                            href="<?php echo e(route('login')); ?>"
                        >
                            <span class="relative z-1">
                                <?php echo __($fSetting->sign_in); ?>

                            </span>
                        </a>
                        <a
                            class="group relative inline-flex whitespace-nowrap rounded-full border-[3px] border-foreground/5 px-3.5 py-3 text-xs/none font-semibold text-foreground transition-all hover:scale-105 hover:border-background hover:bg-background hover:text-foreground hover:shadow-lg hover:shadow-black/5 group-[&.is-dark]/header:text-white group-[&.is-dark]/header:hover:text-foreground"
                            href="<?php echo e(route('register')); ?>"
                        >
                            <?php if (isset($component)) { $__componentOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal77a08a15bb8fd56a2ef1c4a7aa26a8ad = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.outline-glow','data' => ['class' => 'lqd-outline-glow-custom [--outline-glow-w:3px] group-hover:opacity-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('outline-glow'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lqd-outline-glow-custom [--outline-glow-w:3px] group-hover:opacity-0']); ?>
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
                            <span class="relative z-1">
                                <?php echo __($fSetting->join_hub); ?>

                            </span>
                        </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <button
                        class="mobile-nav-trigger group relative z-2 flex size-10 shrink-0 items-center justify-center rounded-full bg-black/10 group-[&.is-dark]/header:bg-white/10 lg:hidden"
                    >
                        <span class="flex w-4 flex-col gap-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php for($i = 0; $i <= 1; $i++): ?>
                                <span
                                    class="inline-flex h-[2px] w-full bg-heading-foreground transition-transform first:origin-left last:origin-right group-[.is-dark]/header:bg-white group-[.lqd-is-sticky]/header:bg-black group-[&.lqd-is-active]:first:-translate-y-[2px] group-[&.lqd-is-active]:first:translate-x-[3px] group-[&.lqd-is-active]:first:rotate-45 group-[&.lqd-is-active]:last:-translate-x-[2px] group-[&.lqd-is-active]:last:-translate-y-[8px] group-[&.lqd-is-active]:last:-rotate-45"
                                ></span>
                            <?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <?php echo $__env->renderWhen($fSetting->floating_button_active, 'landing-page.header.floating-button', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</header>

<?php echo $__env->renderWhen($app_is_demo, 'landing-page.header.envato-link', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

<?php echo $__env->renderWhen(in_array($settings_two->chatbot_status, ['frontend', 'both']) &&
        ($settings_two->chatbot_login_require == false || ($settings_two->chatbot_login_require == true && auth()->check())),
    'panel.chatbot.widget',
    ['page' => 'landing-page']
, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

<svg
    class="hidden"
    width="13"
    height="12"
    viewBox="0 0 13 12"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
>
    <path
        id="menu-active-ind"
        fill="currentColor"
        d="M6.19009 11.95C5.95767 10.6174 5.01249 8.94401 3.21509 7.59596C2.33189 6.92969 1.43319 6.49583 0.549988 6.30989V5.65911C2.3009 5.24075 4.02082 4.06315 5.12095 2.46719C5.67876 1.66146 6.03514 0.871223 6.19009 0.0499992H6.84087C7.10429 1.61497 8.31288 3.35039 9.95533 4.5125C10.7611 5.08581 11.5978 5.47318 12.45 5.65911V6.30989C10.7301 6.66628 8.73124 8.20026 7.73957 9.76523C7.24374 10.5555 6.94934 11.2837 6.84087 11.95H6.19009Z"
    />
</svg>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/marketing-bot/layout/header.blade.php ENDPATH**/ ?>