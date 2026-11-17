<?php
    $customizer_disabled = setting('show_live_customizer', '1') != '1';
    $currentUrl = url()->current();

    if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('ai-chat-pro')) {
        $customizer_disabled = $customizer_disabled || $currentUrl === route('dashboard.user.openai.chat.pro.index') || $currentUrl === route('chat.pro');
    }

    if ($customizer_disabled) {
        return;
    }

    if (!Auth::check()) {
        return;
    }

    if (Auth::user()->isUser()) {
        return;
    }

    $customizerOptions = [
        'colorMainForeground' => [
            'title' => __('Foreground'),
            'cssVar' => '--foreground',
            'type' => 'color',
            'values' => [],
        ],
        'colorMainBackground' => [
            'title' => __('Background Color'),
            'cssVar' => '--background',
            'type' => 'color',
            'values' => [],
        ],
        'colorMainBorder' => [
            'title' => __('Border Color'),
            'cssVar' => '--border',
            'type' => 'color',
            'values' => [],
        ],
        'colorMainHeadingForeground' => [
            'title' => __('Headings Foreground'),
            'cssVar' => '--heading-foreground',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorMainPrimary' => [
            'title' => __('Primary Color'),
            'cssVar' => '--primary',
            'type' => 'color',
            'values' => [],
        ],
        'colorMainPrimaryForeground' => [
            'title' => __('Primary Foreground'),
            'cssVar' => '--primary-foreground',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorMainSecondary' => [
            'title' => __('Secondary Color'),
            'cssVar' => '--secondary',
            'type' => 'color',
            'values' => [],
        ],
        'colorMainSecondaryForeground' => [
            'title' => __('Secondary Foreground'),
            'cssVar' => '--secondary-foreground',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorMainAccent' => [
            'title' => __('Accent Color'),
            'cssVar' => '--accent',
            'type' => 'color',
            'values' => [],
        ],
        'colorMainAccentForeground' => [
            'title' => __('Accent Foreground'),
            'cssVar' => '--accent-foreground',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorMainGradientFrom' => [
            'title' => __('Gradient Start Color'),
            'cssVar' => '--gradient-from',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorMainGradientVia' => [
            'title' => __('Gradient Middle Color'),
            'cssVar' => '--gradient-via',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorMainGradientTo' => [
            'title' => __('Gradient End Color'),
            'cssVar' => '--gradient-to',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],

        'navbarLinkBorderRadius' => [
            'title' => __('Navbar Links Radius'),
            'cssVar' => '--navbar-link-rounded',
            'type' => 'range',
            'value' => '12',
            'showInCustomizer' => false,
        ],
        'colorNavbarBackground' => [
            'title' => __('Background'),
            'cssVar' => '--navbar-background',
            'type' => 'color',
            'values' => [],
        ],
        'colorNavbarForeground' => [
            'title' => __('Foreground'),
            'cssVar' => '--navbar-foreground',
            'type' => 'color',
            'values' => [],
        ],
        'colorNavbarHoverBackground' => [
            'title' => __('Hover Background'),
            'cssVar' => '--navbar-background-hover',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorNavbarHoverForeground' => [
            'title' => __('Hover Foreground'),
            'cssVar' => '--navbar-foreground-hover',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorNavbarActiveBackground' => [
            'title' => __('Active Background'),
            'cssVar' => '--navbar-background-active',
            'type' => 'color',
            'values' => [],
            'showInCustomizer' => false,
        ],
        'colorNavbarActiveForeground' => [
            'title' => __('Active Foreground'),
            'cssVar' => '--navbar-foreground-active',
            'type' => 'color',
            'values' => [],
        ],

        'fontHeading' => [
            'title' => __('Heading Font'),
            'cssVar' => '--font-heading',
            'type' => 'font',
            'value' => '',
        ],
        'fontBody' => [
            'title' => __('Body Font'),
            'cssVar' => '--font-body',
            'type' => 'font',
            'value' => '',
        ],

        'buttonBorderRadius' => [
            'title' => __('Buttons Border Radius'),
            'cssVar' => '--button-rounded',
            'type' => 'range',
            'value' => '999px',
        ],
        'colorButtonBorder' => [
            'title' => __('Border'),
            'cssVar' => '--button-border',
            'type' => 'color',
            'values' => [],
        ],

        'cardBorderRadius' => [
            'title' => __('Cards Border Radius'),
            'cssVar' => '--card-rounded',
            'type' => 'range',
            'value' => '12px',
        ],
        'cardShadow' => [
            'title' => __('Cards Shadow'),
            'cssVar' => '--card-shadow',
            'type' => 'cssValues',
            'value' => '',
            'values' => [
                'none' => [
                    'label' => 'None',
                    'value' => 'none',
                ],
                'xs' => [
                    'label' => 'Extra Small',
                    'value' => '0 2px 1px var(--card-shadow-color, hsl(0 0% 0% / 5%))',
                ],
                'sm' => [
                    'label' => 'Small',
                    'value' => '0 1px 2px 0 var(--card-shadow-color, hsl(0 0% 0% / 5%))',
                ],
                'md' => [
                    'label' => 'Medium',
                    'value' => '0 4px 6px -1px var(--card-shadow-color, hsl(0 0% 0% / 5%)), 0 2px 4px -2px var(--card-shadow-color, hsl(0 0% 0% / 5%))',
                ],
                'lg' => [
                    'label' => 'Large',
                    'value' => '0 10px 15px -3px var(--card-shadow-color, hsl(0 0% 0% / 5%)), 0 4px 6px -4px var(--card-shadow-color, hsl(0 0% 0% / 5%))',
                ],
                'xl' => [
                    'label' => 'Extra Large',
                    'value' => '0 20px 25px -5px var(--card-shadow-color, hsl(0 0% 0% / 5%)), 0 8px 10px -6px var(--card-shadow-color, hsl(0 0% 0% / 5%))',
                ],
                '2xl' => [
                    'label' => '2Extra Large',
                    'value' => '0 25px 50px -12px var(--card-shadow-color, hsl(0 0% 0% / 5%))',
                ],
            ],
            'showInCustomizer' => false,
        ],
        'colorCardBackground' => [
            'title' => __('Background'),
            'cssVar' => '--card-background',
            'type' => 'color',
            'values' => [],
        ],
        'colorCardBorder' => [
            'title' => __('Border'),
            'cssVar' => '--card-border',
            'type' => 'color',
            'values' => [],
        ],

        'inputBorderRadius' => [
            'title' => __('Inputs Border Radius'),
            'cssVar' => '--input-rounded-multiplier',
            'type' => 'range',
            'value' => '1',
        ],
        'colorInputForeground' => [
            'title' => __('Foreground'),
            'cssVar' => '--input-foreground',
            'type' => 'color',
            'values' => [],
        ],
        'colorInputBackground' => [
            'title' => __('Background'),
            'cssVar' => '--input-background',
            'type' => 'color',
            'values' => [],
        ],
        'colorInputBorder' => [
            'title' => __('Border'),
            'cssVar' => '--input-border',
            'type' => 'color',
            'values' => [],
        ],
    ];

    $active_theme = setting('dash_theme') ?? 'default';

    $google_fonts = \App\Services\Common\FontsService::getGoogleFonts();
    $google_fonts_list = [];

    foreach ($google_fonts['items'] ?? [] as $font) {
        $family = $font['family'];
        $lowercase_family = strtolower($family);

        if (!str_contains($lowercase_family, 'icon') && !str_contains($lowercase_family, 'material symbols')) {
            $google_fonts_list[] = $family;
        }
    }
?>

<div
    class="lqd-customizer fixed bottom-8 left-1/2 z-[9999] flex -translate-x-1/2 items-center gap-2 rounded-full border border-black/10 bg-white/80 px-2 py-1.5 shadow-xl shadow-black/[7%] backdrop-blur-lg backdrop-saturate-150 transition-colors max-md:hidden"
    x-data="lqdCustomizer"
>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customizerOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($option['type'] !== 'color' || !($option['showInCustomizer'] ?? true) || strpos($key, 'colorMain') !== 0) continue; ?>

        <?php
            $showInCustomizer = $option['showInCustomizer'] ?? true;
            $mainColorAssosiatedKeys = [];

            if ($key === 'colorMainPrimary') {
                $mainColorAssosiatedKeys = [
                    [
                        'key' => 'colorMainPrimaryForeground',
                        'manipulations' => [
                            [
                                'type' => 'autoBlackWhite',
                            ],
                        ],
                    ],
                ];
            }
            if ($key === 'colorMainSecondary') {
                $mainColorAssosiatedKeys = [
                    [
                        'key' => 'colorMainSecondaryForeground',
                        'manipulations' => [
                            [
                                'type' => 'autoBlackWhite',
                                'value' => '--secondary',
                            ],
                        ],
                    ],
                ];
            }
            if ($key === 'colorMainAccent') {
                $mainColorAssosiatedKeys = [
                    [
                        'key' => 'colorMainAccentForeground',
                        'manipulations' => [
                            [
                                'type' => 'autoBlackWhite',
                                'value' => '--accent',
                            ],
                        ],
                    ],
                ];
            }
            if ($key === 'colorMainForeground') {
                $mainColorAssosiatedKeys = [
                    [
                        'key' => 'colorMainHeadingForeground',
                        'manipulations' => [
                            [
                                'type' => 'darken',
                                'value' => 22,
                                'condition' => 'lightMode',
                            ],
                            [
                                'type' => 'lighten',
                                'value' => 30,
                                'condition' => 'darkMode',
                            ],
                            [
                                'type' => 'desaturate',
                                'value' => 7,
                                'condition' => 'lightMode',
                            ],
                            [
                                'type' => 'desaturate',
                                'value' => 30,
                                'condition' => 'darkMode',
                            ],
                        ],
                    ],
                ];
            }
        ?>

        <label
            class="relative inline-block size-9 shrink-0 grow-0 cursor-pointer rounded-full border border-black/10 transition-all before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100"
            type="button"
            title="<?php echo e($option['title']); ?>"
            style="background-color: hsl(var(<?php echo e($option['cssVar']); ?>));"
            for="lqd-customizer-<?php echo e($key); ?>"
        >
            <input
                class="absolute inset-0 size-full cursor-pointer opacity-0"
                id="lqd-customizer-<?php echo e($key); ?>"
                type="color"
                @input="onColorInput({key: '<?php echo e($key); ?>', color: $event.target.value, assosiatedKeys: <?php echo e(json_encode($mainColorAssosiatedKeys)); ?>})"
                :value="options.<?php echo e($key); ?>.values?.[darkMode ? 'dark' : 'light']?.hex || parseColor({ color: options.<?php echo e($key); ?>.cssVar, format: 'hex' })"
                value="#000000"
            >
        </label>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['type' => 'inline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:modal' => 'bottom-full overflow-visible end-0 start-auto min-w-[320px] max-w-[90vw] top-auto mb-3 mt-0','class:modal-backdrop' => 'invisible','class:modal-content' => 'max-h-[calc(100vh-110px)] overflow-visible border border-black/5 bg-white shadow-xl shadow-black/5','class:modal-body' => 'p-3']); ?>
         <?php $__env->slot('trigger', null, ['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','variant' => 'none','size' => 'none','title' => ''.e(__('Gradients')).'','href' => '#']); ?> 
            <span class="inline-block size-6 rounded-full bg-gradient-to-r from-gradient-from via-gradient-via to-gradient-to shadow-sm"></span>
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('modal', null, []); ?> 
            <div class="space-y-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = ['colorMainGradientFrom', 'colorMainGradientVia', 'colorMainGradientTo']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label
                        class="flex cursor-pointer items-center gap-2 text-2xs font-medium text-black/90"
                        type="button"
                        for="lqd-customizer-<?php echo e($key); ?>"
                    >
                        <span
                            class="relative inline-block size-7 shrink-0 grow-0 rounded-full border border-black/10 transition-all"
                            style="background-color: hsl(var(<?php echo e($customizerOptions[$key]['cssVar']); ?>));"
                        >
                            <input
                                class="absolute inset-0 size-full cursor-pointer opacity-0"
                                id="lqd-customizer-<?php echo e($key); ?>"
                                type="color"
                                @input="onColorInput({key: '<?php echo e($key); ?>', color: $event.target.value})"
                                :value="options.<?php echo e($key); ?>.values?.[darkMode ? 'dark' : 'light']?.hex || parseColor({ color: options.<?php echo e($key); ?>.cssVar, format: 'hex' })"
                                value="#000000"
                            >
                        </span>
                        <?php echo e($customizerOptions[$key]['title']); ?>

                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

    <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'none','size' => 'none','href' => '#'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','title' => ''.e(__('Random Colors')).'','@click.prevent' => 'randomColors']); ?>
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-arrows-shuffle'); ?>
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

    <span class="mx-1 inline-block h-6 w-px bg-black/10 transition-colors"></span>

    <?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['type' => 'inline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:modal' => 'bottom-full overflow-visible end-0 start-auto min-w-[320px] max-w-[90vw] top-auto mb-3 mt-0','class:modal-backdrop' => 'invisible','class:modal-content' => 'max-h-[calc(100vh-110px)] overflow-visible border border-black/5 bg-white shadow-xl shadow-black/5','class:modal-body' => 'p-3']); ?>
         <?php $__env->slot('trigger', null, ['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','variant' => 'none','size' => 'none','title' => ''.e(__('Typography')).'','href' => '#']); ?> 
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-letter-case'); ?>
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
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('modal', null, []); ?> 
            <div class="flex flex-col gap-4">

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customizerOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($option['type'] !== 'font') continue; ?>

                    <div x-data="lqdCustomizerFontPicker('<?php echo e($key); ?>')">
                        <p class="text-[12px] font-medium text-black/60">
                            <?php echo e($option['title']); ?>

                        </p>

                        <div class="relative">
                            <button
                                class="flex w-full cursor-pointer items-center justify-between gap-1 rounded-lg border border-black/5 p-2 text-2xs text-black/90 transition-all hover:bg-black/[2%]"
                                type="button"
                                @click="dropdownOpen = !dropdownOpen"
                                :style="{ 'fontFamily': selectedFont + ', sans-serif' }"
                            >
                                <span x-text="selectedFont || '<?php echo e(__('Choose a Font')); ?>'"></span>

                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-chevron-down'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4 transition-transform',':class' => '{ \'rotate-180\': dropdownOpen }']); ?>
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

                            <div
                                class="absolute -inset-x-1 bottom-full z-2 mb-2 rounded-lg border border-black/5 bg-white pb-3 text-xs shadow-lg shadow-black/5"
                                x-cloak
                                x-show="dropdownOpen"
                                @click.outside="dropdownOpen = false"
                                x-transition
                                @keydown.escape.window="dropdownOpen = false"
                            >
                                <form class="relative p-3">
                                    <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'absolute start-6 top-1/2 size-4 -translate-y-1/2']); ?>
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
                                        class="w-full rounded-md border border-black/5 py-2 pe-2 ps-8 text-2xs"
                                        type="search"
                                        placeholder="<?php echo e(__('Search for a font')); ?>"
                                        @input.debounce.50ms="$event.target.value.length>= 3 && onSearchInput()"
                                        x-model="searchString"
                                        x-trap="dropdownOpen"
                                    />
                                </form>

                                <ul class="max-h-80 overflow-y-auto overscroll-contain px-3">
                                    <template
                                        x-for="(font, index) in showingGoogleFonts"
                                        :key="font"
                                    >
                                        <li
                                            class="cursor-pointer rounded-md p-2 text-2xs font-medium text-black/90 transition-all hover:bg-black/[2%]"
                                            @click="addToGoogleFontLoadQueue(font); selectedFont = font; dropdownOpen = false"
                                            x-init="index <= 10 && addToGoogleFontLoadQueue(font, true)"
                                            x-intersect:enter.once="addToGoogleFontLoadQueue(font, true)"
                                            :style="{ 'fontFamily': font + ', sans-serif' }"
                                        >
                                            <span x-text="font"></span>
                                        </li>
                                    </template>
                                    <li
                                        class="min-h-px opacity-0"
                                        x-show="!searchString.trim()"
                                        x-intersect="loadMoreFonts"
                                    ></li>
                                    <li
                                        class="cursor-default rounded-md p-2 text-2xs font-medium text-black/60 transition-all"
                                        x-show="showingGoogleFonts.length === 0"
                                    >
                                        <?php echo e(__('No fonts found')); ?>

                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

    <span class="mx-1 inline-block h-6 w-px bg-black/10 transition-colors"></span>

    <?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['type' => 'inline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:modal' => 'bottom-full overflow-visible end-0 start-auto min-w-[320px] max-w-[90vw] top-auto mb-3 mt-0','class:modal-content' => 'max-h-[calc(100vh-110px)] overflow-visible border border-black/5 bg-white shadow-xl shadow-black/5','class:modal-backdrop' => 'invisible','class:modal-body' => 'p-3']); ?>
         <?php $__env->slot('trigger', null, ['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','variant' => 'none','size' => 'none','title' => ''.e(__('Navbar')).'','href' => '#']); ?> 
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-layout-sidebar'); ?>
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
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('modal', null, []); ?> 
            <div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($active_theme !== 'bolt' && $active_theme !== 'social-media' && $active_theme !== 'social-media-dashboard'): ?>
                    <?php if (isset($component)) { $__componentOriginala97611b31e90fc7dc431a34465dcc851 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala97611b31e90fc7dc431a34465dcc851 = $attributes; } ?>
<?php $component = App\View\Components\Forms\Input::resolve(['type' => 'checkbox','size' => 'sm','switcher' => true,'label' => ''.e(__('Collapse Navbar')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('forms.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Forms\Input::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:label' => 'flex-row-reverse justify-between text-black/90','class' => 'border-black/10','@change' => '$store.navbarShrink.toggle()',':checked' => '$store.navbarShrink.active']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala97611b31e90fc7dc431a34465dcc851)): ?>
<?php $attributes = $__attributesOriginala97611b31e90fc7dc431a34465dcc851; ?>
<?php unset($__attributesOriginala97611b31e90fc7dc431a34465dcc851); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala97611b31e90fc7dc431a34465dcc851)): ?>
<?php $component = $__componentOriginala97611b31e90fc7dc431a34465dcc851; ?>
<?php unset($__componentOriginala97611b31e90fc7dc431a34465dcc851); ?>
<?php endif; ?>

                    <hr class="!my-3 border-black/10" />
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="flex w-full flex-col text-black dark:text-black">
                    <label
                        class="text-[12px] font-medium text-black/90"
                        for="navbar-link-border-radius"
                    >
                        <?php echo e(__('Links Border Radius')); ?>

                    </label>
                    <input
                        class="z-10 mb-2 mt-2 h-1 w-full appearance-none rounded-full bg-black/10 focus:outline-primary [&::-moz-range-thumb]:size-3 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:bg-primary active:[&::-moz-range-thumb]:scale-110 [&::-webkit-slider-thumb]:size-3 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:bg-primary active:[&::-webkit-slider-thumb]:scale-110"
                        id="navbar-link-border-radius"
                        type="range"
                        min="0"
                        max="30"
                        :value="parseInt(options.navbarLinkBorderRadius.value)"
                        @input="options.navbarLinkBorderRadius.value = $event.target.value + 'px'"
                    />
                </div>

                <hr class="border-black/10" />

                <div class="space-y-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customizerOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($option['type'] !== 'color' || !($option['showInCustomizer'] ?? true) || strpos($key, 'colorNavbar') !== 0) continue; ?>

                        <?php
                            $navbarColorAssosiatedKeys = [];

                            if ($key === 'colorNavbarActiveForeground') {
                                $navbarColorAssosiatedKeys = [
                                    [
                                        'key' => 'colorNavbarActiveBackground',
                                    ],
                                    [
                                        'key' => 'colorNavbarHoverForeground',
                                    ],
                                    [
                                        'key' => 'colorNavbarHoverBackground',
                                    ],
                                ];
                            }
                        ?>

                        <label
                            class="flex cursor-pointer items-center gap-2 text-2xs font-medium text-black/90"
                            type="button"
                            for="lqd-customizer-<?php echo e($key); ?>"
                        >
                            <span
                                class="relative inline-block size-7 shrink-0 grow-0 rounded-full border border-black/10 transition-all"
                                style="background-color: hsl(var(<?php echo e($option['cssVar']); ?>));"
                            >
                                <input
                                    class="absolute inset-0 size-full cursor-pointer opacity-0"
                                    id="lqd-customizer-<?php echo e($key); ?>"
                                    type="color"
                                    @input="onColorInput({key: '<?php echo e($key); ?>', color: $event.target.value, assosiatedKeys: <?php echo e(json_encode($navbarColorAssosiatedKeys)); ?>})"
                                    :value="options.<?php echo e($key); ?>.values?.[darkMode ? 'dark' : 'light']?.hex || parseColor({
                                        color: options.<?php echo e($key); ?>.cssVar,
                                        format: 'hex'
                                    })")"
                                    value="#000000"
                                >
                            </span>
                            <?php echo e($option['title']); ?>

                        </label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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

    <span class="mx-1 inline-block h-6 w-px bg-black/10 transition-colors"></span>

    <?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['type' => 'inline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:modal' => 'bottom-full overflow-visible end-0 start-auto min-w-[320px] max-w-[90vw] top-auto mb-3 mt-0','class:modal-content' => 'max-h-[calc(100vh-110px)] overflow-y-auto border border-black/5 bg-white shadow-xl shadow-black/5','class:modal-backdrop' => 'invisible','class:modal-body' => 'p-3']); ?>
         <?php $__env->slot('trigger', null, ['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','variant' => 'none','size' => 'none','title' => ''.e(__('Components')).'','href' => '#']); ?> 
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-triangle-square-circle'); ?>
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
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('modal', null, []); ?> 
            <div class="space-y-5 py-4">
                <div class="rounded-lg border border-black/10 p-4">
                    <h4 class="-mt-7 mb-5 text-black">
                        <span class="-ms-1 inline-block bg-white pe-3 ps-1">
                            <?php echo e(__('Buttons')); ?>

                        </span>
                    </h4>

                    <div class="space-y-2">
                        <div class="flex w-full flex-col text-black dark:text-black">
                            <label
                                class="text-[12px] font-medium text-black/90"
                                for="buttons-border-radius"
                            >
                                <?php echo e(__('Border Radius')); ?>

                            </label>
                            <input
                                class="z-10 mb-2 mt-2 h-1 w-full appearance-none rounded-full bg-black/10 focus:outline-primary [&::-moz-range-thumb]:size-3 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:bg-primary active:[&::-moz-range-thumb]:scale-110 [&::-webkit-slider-thumb]:size-3 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:bg-primary active:[&::-webkit-slider-thumb]:scale-110"
                                id="buttons-border-radius"
                                type="range"
                                min="0"
                                max="30"
                                :value="parseInt(options.buttonBorderRadius.value)"
                                @input="options.buttonBorderRadius.value = $event.target.value + 'px'"
                            />
                        </div>

                        <hr class="!my-3 border-black/10" />

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customizerOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($option['type'] !== 'color' || !($option['showInCustomizer'] ?? true) || strpos($key, 'colorButton') !== 0) continue; ?>

                            <label
                                class="flex cursor-pointer items-center gap-2 text-2xs font-medium text-black/90"
                                type="button"
                                for="lqd-customizer-<?php echo e($key); ?>"
                            >
                                <span
                                    class="relative inline-block size-7 shrink-0 grow-0 rounded-full border border-black/10 transition-all"
                                    style="background-color: hsl(var(<?php echo e($option['cssVar']); ?>));"
                                >
                                    <input
                                        class="absolute inset-0 size-full cursor-pointer opacity-0"
                                        id="lqd-customizer-<?php echo e($key); ?>"
                                        type="color"
                                        @input="onColorInput({key: '<?php echo e($key); ?>', color: $event.target.value })"
                                        :value="options.<?php echo e($key); ?>.values?.[darkMode ? 'dark' : 'light']?.hex || parseColor({
                                            color: options.<?php echo e($key); ?>.cssVar,
                                            format: 'hex'
                                        })")"
                                        value="#000000"
                                    >
                                </span>
                                <?php echo e($option['title']); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="rounded-lg border border-black/10 p-4">
                    <h4 class="-mt-7 mb-5 text-black">
                        <span class="-ms-1 inline-block bg-white pe-3 ps-1">
                            <?php echo e(__('Cards')); ?>

                        </span>
                    </h4>

                    <div class="space-y-2">
                        <div class="flex w-full flex-col text-black dark:text-black">
                            <label
                                class="text-[12px] font-medium text-black/90"
                                for="cards-border-radius"
                            >
                                <?php echo e(__('Border Radius')); ?>

                            </label>
                            <input
                                class="z-10 mb-2 mt-2 h-1 w-full appearance-none rounded-full bg-black/10 focus:outline-primary [&::-moz-range-thumb]:size-3 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:bg-primary active:[&::-moz-range-thumb]:scale-110 [&::-webkit-slider-thumb]:size-3 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:bg-primary active:[&::-webkit-slider-thumb]:scale-110"
                                id="cards-border-radius"
                                type="range"
                                value="20"
                                min="0"
                                max="30"
                                :value="parseInt(options.cardBorderRadius.value)"
                                @input="options.cardBorderRadius.value = $event.target.value + 'px'"
                            />
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <label class="text-[12px] font-medium text-black/90">
                                <?php echo e(__('Shadow')); ?>

                            </label>
                            <div class="flex items-center gap-1">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customizerOptions['cardShadow']['values']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $shadow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label
                                        class="relative inline-grid size-7 cursor-pointer place-content-center place-items-center rounded border border-black/10 bg-white text-black/90 shadow-card transition-all [&.active]:bg-black/90 [&.active]:text-white"
                                        for="lqd-customizer-shadow-<?php echo e($key); ?>-card"
                                        style="--card-shadow-color: hsl(0 0% 0% / 20%); --card-shadow: <?php echo e($key === '2xl' ? '0 20px 30px 10px var(--card-shadow-color, hsl(0 0% 0% / 50%))' : $shadow['value']); ?>; z-index: <?php echo e($loop->count - $loop->index); ?>;"
                                        :class="{ 'active': options.cardShadow.value === '<?php echo e($shadow['value']); ?>' }"
                                    >
                                        <input
                                            class="absolute size-full cursor-pointer opacity-0"
                                            id="lqd-customizer-shadow-<?php echo e($key); ?>-card"
                                            x-model="options.cardShadow.value"
                                            type="radio"
                                            value="<?php echo e($shadow['value']); ?>"
                                        />
                                        <span class="w-full overflow-hidden text-ellipsis whitespace-nowrap text-center text-[10px] font-semibold uppercase tracking-wide">
                                            <?php echo e($key); ?>

                                        </span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>

                        <hr class="!my-3 border-black/10" />

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customizerOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($option['type'] !== 'color' || !($option['showInCustomizer'] ?? true) || strpos($key, 'colorCard') !== 0) continue; ?>

                            <label
                                class="flex cursor-pointer items-center gap-2 text-2xs font-medium text-black/90"
                                type="button"
                                for="lqd-customizer-<?php echo e($key); ?>"
                            >
                                <span
                                    class="relative inline-block size-7 shrink-0 grow-0 rounded-full border border-black/10 transition-all"
                                    style="background-color: hsl(var(<?php echo e($option['cssVar']); ?>));"
                                >
                                    <input
                                        class="absolute inset-0 size-full cursor-pointer opacity-0"
                                        id="lqd-customizer-<?php echo e($key); ?>"
                                        type="color"
                                        @input="onColorInput({key: '<?php echo e($key); ?>', color: $event.target.value })"
                                        :value="options.<?php echo e($key); ?>.values?.[darkMode ? 'dark' : 'light']?.hex || parseColor({
                                            color: options.<?php echo e($key); ?>.cssVar,
                                            format: 'hex'
                                        })")"
                                        value="#000000"
                                    >
                                </span>
                                <?php echo e($option['title']); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <div class="rounded-lg border border-black/10 p-4">
                    <h4 class="-mt-7 mb-5 text-black">
                        <span class="-ms-1 inline-block bg-white pe-3 ps-1">
                            <?php echo e(__('Inputs')); ?>

                        </span>
                    </h4>

                    <div class="space-y-2">
                        <div class="flex w-full flex-col text-black dark:text-black">
                            <label
                                class="text-[12px] font-medium text-black/90"
                                for="inputs-border-radius"
                            >
                                <?php echo e(__('Border Radius')); ?>

                            </label>
                            <input
                                class="z-10 mb-2 mt-2 h-1 w-full appearance-none rounded-full bg-black/10 focus:outline-primary [&::-moz-range-thumb]:size-3 [&::-moz-range-thumb]:appearance-none [&::-moz-range-thumb]:rounded-full [&::-moz-range-thumb]:border-none [&::-moz-range-thumb]:bg-primary active:[&::-moz-range-thumb]:scale-110 [&::-webkit-slider-thumb]:size-3 [&::-webkit-slider-thumb]:appearance-none [&::-webkit-slider-thumb]:rounded-full [&::-webkit-slider-thumb]:border-none [&::-webkit-slider-thumb]:bg-primary active:[&::-webkit-slider-thumb]:scale-110"
                                id="inputs-border-radius"
                                type="range"
                                value="1"
                                min="0"
                                max="3"
                                step="0.2"
                                x-model="options.inputBorderRadius.value"
                            />
                        </div>

                        <hr class="!my-3 border-black/10" />

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $customizerOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($option['type'] !== 'color' || !($option['showInCustomizer'] ?? true) || strpos($key, 'colorInput') !== 0) continue; ?>

                            <label
                                class="flex cursor-pointer items-center gap-2 text-2xs font-medium text-black/90"
                                type="button"
                                for="lqd-customizer-<?php echo e($key); ?>"
                            >
                                <span
                                    class="relative inline-block size-7 shrink-0 grow-0 rounded-full border border-black/10 transition-all"
                                    style="background-color: hsl(var(<?php echo e($option['cssVar']); ?>));"
                                >
                                    <input
                                        class="absolute inset-0 size-full cursor-pointer opacity-0"
                                        id="lqd-customizer-<?php echo e($key); ?>"
                                        type="color"
                                        @input="onColorInput({key: '<?php echo e($key); ?>', color: $event.target.value })"
                                        :value="options.<?php echo e($key); ?>.values?.[darkMode ? 'dark' : 'light']?.hex || parseColor({
                                            color: options.<?php echo e($key); ?>.cssVar,
                                            format: 'hex'
                                        })")"
                                        value="#000000"
                                    >
                                </span>
                                <?php echo e($option['title']); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
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

    <span class="mx-1 inline-block h-6 w-px bg-black/10 transition-colors"></span>

    <?php if (isset($component)) { $__componentOriginaleecbb9db3a7b2f7d8583df3373bbdf39 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleecbb9db3a7b2f7d8583df3373bbdf39 = $attributes; } ?>
<?php $component = App\View\Components\LightDarkSwitch::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('light-dark-switch'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\LightDarkSwitch::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 hover:translate-y-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleecbb9db3a7b2f7d8583df3373bbdf39)): ?>
<?php $attributes = $__attributesOriginaleecbb9db3a7b2f7d8583df3373bbdf39; ?>
<?php unset($__attributesOriginaleecbb9db3a7b2f7d8583df3373bbdf39); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleecbb9db3a7b2f7d8583df3373bbdf39)): ?>
<?php $component = $__componentOriginaleecbb9db3a7b2f7d8583df3373bbdf39; ?>
<?php unset($__componentOriginaleecbb9db3a7b2f7d8583df3373bbdf39); ?>
<?php endif; ?>

    <span class="mx-1 inline-block h-6 w-px bg-black/10 transition-colors"></span>

    <?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['type' => 'inline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:modal' => 'bottom-full overflow-visible end-0 start-auto top-auto mb-3 mt-0 w-[min(400px,100%)]','class:modal-backdrop' => 'invisible','class:modal-content' => 'max-h-[calc(100vh-110px)] overflow-visible border border-black/5 bg-white shadow-xl shadow-black/5','class:modal-body' => 'p-3']); ?>
         <?php $__env->slot('trigger', null, ['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','variant' => 'none','size' => 'none','title' => ''.e(__('Export Styles')).'','href' => '#']); ?> 
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-download'); ?>
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
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('modal', null, []); ?> 
            <div class="relative rounded-lg border border-black/5 bg-slate-50 text-black/90">
                <p
                    class="m-0 p-4 text-2xs font-medium"
                    x-show="!styleString.length"
                >
                    <?php echo e(__('Start Customizing colors and other properties.')); ?>

                </p>
                <div
                    class="relative w-full"
                    x-show="styleString.length"
                >
                    <pre class="max-h-[min(440px,75vh)] w-full overflow-y-auto px-5 py-4"><code class="w-full text-2xs/6 font-medium" x-text="styleString"></code></pre>

                    <div class="absolute end-3 top-3 z-2 flex gap-3">
                        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'none','size' => 'none'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-9 shrink-0 rounded-full border border-black/10 text-black/90 backdrop-blur hover:scale-105','title' => ''.e(__('Copy')).'','@click.prevent' => 'navigator.clipboard.writeText(styleString); toastr.success(\''.e(__('Copied to clipboard')).'\')']); ?>
                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-copy'); ?>
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

    <?php if (isset($component)) { $__componentOriginale6a555649da86b3de44465cdfe004aa4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale6a555649da86b3de44465cdfe004aa4 = $attributes; } ?>
<?php $component = App\View\Components\Modal::resolve(['type' => 'inline'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Modal::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class:modal' => 'bottom-full overflow-visible end-0 start-auto top-auto mb-3 mt-0','class:modal-backdrop' => 'invisible','class:modal-content' => 'max-h-[calc(100vh-110px)] overflow-visible border border-black/5 bg-white shadow-xl shadow-black/5','class:modal-body' => 'p-3']); ?>
         <?php $__env->slot('trigger', null, ['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','variant' => 'none','size' => 'none','title' => ''.e(__('Import Styles')).'','href' => '#']); ?> 
            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-upload'); ?>
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
         <?php $__env->endSlot(); ?>

         <?php $__env->slot('modal', null, []); ?> 
            <textarea
                class="w-full rounded-lg border border-black/5 bg-slate-50 px-5 py-4 font-mono text-2xs/6 font-medium text-black/90"
                rows="12"
                placeholder="<?php echo e(__('Paste your styles here')); ?>"
                @input.throttle.30ms="onImportStyles($event.target.value)"
            ></textarea>
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

    <span class="mx-1 inline-block h-6 w-px bg-black/10 transition-colors"></span>

    <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'none','size' => 'none','href' => '#'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:bg-orange-400 hover:text-white hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','title' => ''.e(__('Reset Styles')).'','x-cloak' => true,'x-show' => 'styleString.replace(/[\n\t]/g, \'\').trim().length','@click.prevent' => 'resetStyles']); ?>
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-refresh'); ?>
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

    <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'none','size' => 'none','href' => '#'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:bg-lime-600 hover:text-white hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','title' => ''.e(__('Save & Close Customizer')).'','@click.prevent' => 'saveAndClose']); ?>
        <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-check'); ?>
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

    <div class="relative">
        <?php if (isset($component)) { $__componentOriginale67687e3e4e61f963b25a6bcf3983629 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale67687e3e4e61f963b25a6bcf3983629 = $attributes; } ?>
<?php $component = App\View\Components\Button::resolve(['variant' => 'none','size' => 'none','href' => '#'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\Button::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-9 shrink-0 grow-0 rounded-full border border-black/10 text-black/90 before:pointer-events-none before:invisible before:absolute before:bottom-full before:start-1/2 before:mb-2 before:-translate-x-1/2 before:translate-y-1 before:whitespace-nowrap before:rounded-full before:border before:border-black/10 before:bg-white/90 before:px-2 before:py-0.5 before:text-[12px] before:font-medium before:text-black/90 before:opacity-0 before:transition-all before:content-[attr(title)] hover:scale-105 hover:bg-red-500 hover:text-white hover:before:visible hover:before:translate-y-0 hover:before:opacity-100','title' => ''.e(__('Discard Changes')).'','@click.prevent' => 'if ( confirm(\''.e(__('Are you sure you want to discard changes?')).'\') ) { discardChanges() }']); ?>
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

        <?php if (isset($component)) { $__componentOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9acd6c99af8d8c9491f2759be41ef2c4 = $attributes; } ?>
<?php $component = App\View\Components\InfoTooltip::resolve(['text' => ''.e(__('Your changes are not saved and will be lost if you close the customizer.')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('info-tooltip'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\InfoTooltip::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'absolute -end-2 -top-2 z-2','class:icon' => '[&_svg]:size-5 text-orange-500 opacity-100','x-show' => 'styleString.replace(/[\n\t]/g, \'\').trim().length','x-cloak' => true]); ?>
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
    </div>
</div>

<?php $__env->startPush('script'); ?>
    <script>
        window.lqdGoogleFontsList = <?php echo json_encode($google_fonts_list, 15, 512) ?>;
        window.lqdCustomizerOptions = <?php echo json_encode($customizerOptions, 15, 512) ?>;
    </script>
    <script src="<?php echo e(custom_theme_url('/assets/libs/tinycolor/tinycolor-min.js')); ?>"></script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\omini_customization\OminiAI\app\Extensions\LiveCustomizer\System/../resources/views/particles/customizer.blade.php ENDPATH**/ ?>