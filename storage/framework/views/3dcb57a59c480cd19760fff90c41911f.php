<?php
    use App\Helpers\Classes\MarketplaceHelper;
    $theme = get_theme();
    $is_auth = Auth::check();
    $disable_floating_menu = true;
    $wide_layout_px_class = Theme::getSetting('wideLayoutPaddingX', '');
    $theme_google_fonts = Theme::getSetting('dashboard.googleFonts');
    $sidebarEnabledPages = Theme::getSetting('dashboard.sidebarEnabledPages') ?? [];
    $has_sidebar = in_array(Route::currentRouteName(), $sidebarEnabledPages, true) || (isset($has_sidebar) && $has_sidebar);
    $body_classname = Theme::getSetting('dashboard.bodyClass', '');
    $disable_navbar = isset($disable_navbar) ? $disable_navbar : false;
    $disable_header = isset($disable_header) ? $disable_header : false;
    $disable_titlebar = isset($disable_titlebar) ? $disable_titlebar : false;
    $disable_footer = isset($disable_footer) ? $disable_footer : false;
    $disable_mobile_bottom_menu = isset($disable_mobile_bottom_menu) ? $disable_mobile_bottom_menu : false;

    if (isset($body_class)) {
        $body_classname .= ' ' . $body_class;
    }

    if (!empty($wide_layout_px)) {
        $wide_layout_px_class = $wide_layout_px;
    }

    if (Route::currentRouteName()) {
        $route_name = str_replace(['dashboard.', '.index', '.'], ['', '', '-'], Route::currentRouteName());
        $body_classname .= ' page-' . $route_name;
    }
?>
<!DOCTYPE html>
<html
    class="scroll-smooth"
    lang="<?php echo e(\App\Helpers\Classes\Localization::getLocale()); ?>"
    dir="<?php echo e(\App\Helpers\Classes\Localization::getCurrentLocaleDirection()); ?>"
>

<?php echo $__env->make('panel.layout.partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<body
    data-theme="<?php echo e(setting('dash_theme')); ?>"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
        @twMerge(
            'group/body bg-background font-body text-xs text-foreground antialiased transition-bg',
            $body_classname),
        'has-sidebar' => $has_sidebar,
        'is-not-auth' => !$is_auth,
        'is-auth' => $is_auth,
        'is-admin-page' =>
            Auth::check() &&
            (Route::is('dashboard.admin*') ||
                Route::is('dashboard.blog*') ||
                Route::is('dashboard.page*')),
        'is-auth-page' => Route::is('login', 'register', 'forgot_password'),
        'hide-navbar' => $disable_navbar,
        'hide-footer' => $disable_footer,
        'hide-header' => $disable_header,
        'hide-titlebar' => $disable_titlebar,
        'hide-mobile-bottom-menu' => $disable_mobile_bottom_menu,
    ]); ?>"
>
    <?php if ($__env->exists('panel.layout.after-body-open-immediate')) echo $__env->make('panel.layout.after-body-open-immediate', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldPushContent('after-body-open-immediate'); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($app_is_not_demo): ?>
        <?php echo $__env->first(['onboarding-pro::banner', 'vendor.empty'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->make('panel.layout.partials.mode-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if ($__env->exists('panel.layout.after-body-open')) echo $__env->make('panel.layout.after-body-open', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldPushContent('after-body-open'); ?>

    <?php echo $__env->make('panel.layout.partials.loading', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="lqd-page relative flex min-h-full flex-col">

        <div class="lqd-page-wrapper grow-1 flex">
            <?php
                $showMenu = auth()->check();
                $isImageProLanding = MarketplaceHelper::isRegistered('ai-image-pro') && request()->is('ai-image-pro*');
                if (!$showMenu && $isImageProLanding) {
                    $showMenu = true;
                }
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showMenu): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$disable_navbar): ?>
                    <?php echo $__env->make('panel.layout.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="lqd-page-content-wrap flex grow flex-col overflow-hidden">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($good_for_now): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$disable_header): ?>
                            <?php echo $__env->make('panel.layout.header', ['layout_wide', isset($layout_wide) ? $layout_wide : ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$disable_titlebar): ?>
                            <?php echo $__env->make('panel.layout.titlebar', ['layout_wide', isset($layout_wide) ? $layout_wide : ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php echo $__env->yieldContent('before-content-container'); ?>

                    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'lqd-page-content-container',
                        'h-full',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        'container-fluid' => isset($layout_wide) && !empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ]); ?>">

                        <?php echo $__env->yieldContent('content'); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($app_is_not_demo): ?>
                            <?php echo $__env->first(['onboarding-pro::survey', 'vendor.empty'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php elseif(Auth::check() && !$good_for_now && Route::currentRouteName() != 'dashboard.admin.settings.general'): ?>
                    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'lqd-page-content-container',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        'container-fluid' => isset($layout_wide) && !empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ]); ?>">
                        <?php echo $__env->make('vendor.installer.magicai_c4st_Act', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php else: ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$disable_header): ?>
                            <?php echo $__env->make('panel.layout.header', ['layout_wide', isset($layout_wide) ? $layout_wide : ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$disable_titlebar): ?>
                            <?php echo $__env->make('panel.layout.titlebar', ['layout_wide', isset($layout_wide) ? $layout_wide : ''], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php echo $__env->yieldContent('before-content-container'); ?>

                    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'lqd-page-content-container',
                        'container' => !isset($layout_wide) || empty($layout_wide),
                        'container-fluid' => isset($layout_wide) && !empty($layout_wide),
                        $wide_layout_px_class =>
                            filled($wide_layout_px_class) &&
                            (isset($layout_wide) && !empty($layout_wide)),
                    ]); ?>">

                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$disable_footer): ?>
                        <?php echo $__env->make('panel.layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($has_sidebar && (!isset($disable_default_sidebar) || empty($disable_default_sidebar))): ?>
                        <?php if ($__env->exists('panel.layout.sidebar')) echo $__env->make('panel.layout.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($disable_floating_menu)): ?>
            <?php if (isset($component)) { $__componentOriginal1186fbc95105283416f541ca20a2df8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1186fbc95105283416f541ca20a2df8e = $attributes; } ?>
<?php $component = App\View\Components\FloatingMenu::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('floating-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\FloatingMenu::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1186fbc95105283416f541ca20a2df8e)): ?>
<?php $attributes = $__attributesOriginal1186fbc95105283416f541ca20a2df8e; ?>
<?php unset($__attributesOriginal1186fbc95105283416f541ca20a2df8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1186fbc95105283416f541ca20a2df8e)): ?>
<?php $component = $__componentOriginal1186fbc95105283416f541ca20a2df8e; ?>
<?php unset($__componentOriginal1186fbc95105283416f541ca20a2df8e); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$disable_mobile_bottom_menu): ?>
            <?php if (isset($component)) { $__componentOriginal4fd86c034ffd3ed5f13bef4e5d216cbd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fd86c034ffd3ed5f13bef4e5d216cbd = $attributes; } ?>
<?php $component = App\View\Components\BottomMenu::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('bottom-menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\BottomMenu::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fd86c034ffd3ed5f13bef4e5d216cbd)): ?>
<?php $attributes = $__attributesOriginal4fd86c034ffd3ed5f13bef4e5d216cbd; ?>
<?php unset($__attributesOriginal4fd86c034ffd3ed5f13bef4e5d216cbd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fd86c034ffd3ed5f13bef4e5d216cbd)): ?>
<?php $component = $__componentOriginal4fd86c034ffd3ed5f13bef4e5d216cbd; ?>
<?php unset($__componentOriginal4fd86c034ffd3ed5f13bef4e5d216cbd); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!isset($disableChatbot)): ?>
        <?php
            $currentUrl = url()->current();
            $shouldShowChatbot =
                in_array($settings_two->chatbot_status, ['dashboard', 'both']) &&
                !activeRoute('dashboard.user.openai.chat.chat', 'dashboard.user.openai.webchat.workbook', 'dashboard.user.advanced-image.index') &&
                route('dashboard.user.openai.generator.workbook', 'ai_vision') !== $currentUrl &&
                route('dashboard.user.openai.generator.workbook', 'ai_chat_image') !== $currentUrl &&
                route('dashboard.user.openai.generator.workbook', 'ai_pdf') !== $currentUrl;

            if (MarketplaceHelper::isRegistered('ai-chat-pro')) {
                $shouldShowChatbot = $shouldShowChatbot && route('dashboard.user.openai.chat.pro.index') !== $currentUrl && route('chat.pro') !== $currentUrl;
            }
        ?>

        <?php echo $__env->renderWhen($shouldShowChatbot, 'panel.chatbot.widget', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if ($__env->exists('live-customizer::particles.customizer')) echo $__env->make('live-customizer::particles.customizer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('panel.layout.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session()->has('message')): ?>
        <script>
            toastr.<?php echo e(session('type')); ?>('<?php echo e(session('message')); ?>');
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <script>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                toastr.error('<?php echo e($error); ?>');
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo $__env->yieldPushContent('script'); ?>

    <script src="<?php echo e(custom_theme_url('/assets/js/frontend.js')); ?>"></script>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($setting->dashboard_code_before_body != null): ?>
        <?php echo $setting->dashboard_code_before_body; ?>

    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <script src="<?php echo e(custom_theme_url('assets/js/chatbot.js')); ?>"></script>

    <?php if ($__env->exists('panel.layout.before-body-close')) echo $__env->make('panel.layout.before-body-close', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($app_is_not_demo): ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->isAdmin()): ?>
                <script src="<?php echo e(custom_theme_url('/assets/js/panel/update-check.js')); ?>"></script>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <script src="<?php echo e(custom_theme_url('/assets/libs/introjs/intro.min.js')); ?>"></script>
        <?php if ($__env->exists('seo-tool::particles.generate-seo-script')) echo $__env->make('seo-tool::particles.generate-seo-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('panel.layout.includes.lazy-intercom', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('panel.layout.includes.subscription-status', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scriptConfig(); ?>


    <template id="typing-template">
        <div class="lqd-typing relative inline-flex items-center gap-3 rounded-full bg-secondary !px-3 !py-2 text-xs font-medium leading-none text-secondary-foreground">
            <?php echo e(__('Typing')); ?>

            <div class="lqd-typing-dots flex h-5 items-center gap-1">
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-40 ![animation-delay:0.2s]"></span>
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-60 ![animation-delay:0.3s]"></span>
                <span class="lqd-typing-dot inline-block !h-1 !w-1 rounded-full !bg-current opacity-80 ![animation-delay:0.4s]"></span>
            </div>
        </div>
    </template>

    <template id="copy-btns-template">
        <div
            class="pointer-events-none invisible flex translate-y-1 flex-col gap-2 opacity-0 transition-all group-[&.active]/copy-wrap:pointer-events-auto group-[&.active]/copy-wrap:visible group-[&.active]/copy-wrap:translate-y-0 group-[&.active]/copy-wrap:opacity-100">
            <button
                class="group/btn relative inline-flex size-9 items-center justify-center rounded-full bg-white p-0 text-[12px] text-black shadow-lg transition-all hover:scale-110"
                data-copy-type="md"
                type="button"
            >
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-markdown'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5','stroke-width' => '1.5']); ?>
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
                <span
                    class="absolute end-full top-1/2 me-1 inline-block -translate-y-1/2 translate-x-1 whitespace-nowrap rounded-full bg-white px-3 py-1 font-medium leading-5 opacity-0 shadow-lg transition-all group-hover/btn:translate-x-0 group-hover/btn:opacity-100"
                >
                    <?php echo app('translator')->get('Copy Markdown'); ?>
                </span>
            </button>
            <button
                class="group/btn relative inline-flex size-9 items-center justify-center rounded-full bg-white p-0 text-[12px] text-black shadow-lg transition-all hover:scale-110"
                data-copy-type="html"
                type="button"
            >
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('tabler-file-type-html'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(BladeUI\Icons\Components\Svg::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5','stroke-width' => '1.5']); ?>
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
                <span
                    class="absolute end-full top-1/2 me-1 inline-block -translate-y-1/2 translate-x-1 whitespace-nowrap rounded-full bg-white px-3 py-1 font-medium leading-5 opacity-0 shadow-lg transition-all group-hover/btn:translate-x-0 group-hover/btn:opacity-100"
                >
                    <?php echo app('translator')->get('Copy HTML'); ?>
                </span>
            </button>
        </div>
    </template>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($app_is_demo): ?>
        <?php if (isset($component)) { $__componentOriginal9ceb171f1767f45e800191509ce86428 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ceb171f1767f45e800191509ce86428 = $attributes; } ?>
<?php $component = App\View\Components\DemoSwitcher::resolve(['themesType' => 'Dashboard'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('demo-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\DemoSwitcher::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ceb171f1767f45e800191509ce86428)): ?>
<?php $attributes = $__attributesOriginal9ceb171f1767f45e800191509ce86428; ?>
<?php unset($__attributesOriginal9ceb171f1767f45e800191509ce86428); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ceb171f1767f45e800191509ce86428)): ?>
<?php $component = $__componentOriginal9ceb171f1767f45e800191509ce86428; ?>
<?php unset($__componentOriginal9ceb171f1767f45e800191509ce86428); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if ($__env->exists('demoextension::switcher')) echo $__env->make('demoextension::switcher', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php if ($__env->exists('content-manager::media-modal')) echo $__env->make('content-manager::media-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</body>

</html>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/layout/app.blade.php ENDPATH**/ ?>