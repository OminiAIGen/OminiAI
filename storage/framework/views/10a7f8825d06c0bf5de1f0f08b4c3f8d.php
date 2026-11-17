<?php
    use App\Helpers\Classes\MarketplaceHelper;

    $theme = setting('dash_theme', 'default');
    $fromDashboard = str(request()->route()?->getName() ?? '')->startsWith('dashboard.user.ai-image-pro.');
    $isCreativeSuiteInstalled = MarketplaceHelper::isRegistered('creative-suite');
    $isAdvancedImageInstalled = MarketplaceHelper::isRegistered('advanced-image');
?>


<?php $__env->startSection('title', __('AI Image Pro')); ?>
<?php $__env->startSection('titlebar_actions', ''); ?>

<?php $__env->startPush('after-body-open'); ?>
    <script>
        (() => {
            document.body.classList.remove("focus-mode");
            document.body.classList.add('navbar-shrinked');
            localStorage.setItem('lqdNavbarShrinked', true);
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('css'); ?>
    <style>
        @media (min-width: 992px) {
            .lqd-header {
                display: none !important;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    
    <div
        class="pointer-events-none absolute end-[var(--body-padding,0px)] start-[var(--body-padding,0px)] top-[var(--body-padding,0px)] -z-1 overflow-hidden mix-blend-darken blur-[1px] dark:opacity-70 dark:mix-blend-color dark:[mask-image:linear-gradient(to_bottom,black,transparent)] lg:start-[--navbar-width]"
        aria-hidden="true"
    >
        <img
            class="w-full"
            src="<?php echo e(custom_theme_url('/vendor/ai-image-pro/images/bg.jpg')); ?>"
            alt="Background image"
        >
    </div>

    
    <div
        class="lqd-adv-img-editor [--header-h:60px] [--sidebar-w:370px] md:pt-[--header-h]"
        x-data="imageProManager"
    >
        <div class="lqd-adv-img-editor-home transition-all">
            <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                'container',
                'max-sm:px-0' => $theme === 'social-media-dashboard',
            ]); ?>">
                <?php echo $__env->make('ai-image-pro::home.top-navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <?php echo $__env->make('ai-image-pro::home.generator-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fromDashboard): ?>
            <div class="pb-20">
                <div class="container">
                    <?php echo $__env->renderWhen(setting('ai-image-pro:show-tools-section', 1), 'ai-image-pro::home.sections.tools', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
                </div>

                <?php echo $__env->make('ai-image-pro::home.sections.user-dashboard', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>

            
            <?php echo $__env->make('ai-image-pro::home.sections.community-gallery.full', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php else: ?>
            <div class="container">
                <?php echo $__env->renderWhen(setting('ai-image-pro:show-tools-section', 1), 'ai-image-pro::home.sections.tools', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            </div>

            <?php echo $__env->renderWhen(setting('ai-image-pro:show-community-section', 1), 'ai-image-pro::home.sections.community-gallery.wrapper', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

            <?php echo $__env->renderWhen($fSectSettings->generators_active == 1, 'ai-image-pro::home.sections.generators', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            <?php echo $__env->renderWhen($fSectSettings->faq_active == 1, 'ai-image-pro::home.sections.faq', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            <?php echo $__env->renderWhen($setting->gdpr_status == 1, 'landing-page.gdpr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
            <?php echo $__env->make('ai-image-pro::home.sections.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php echo $__env->make('ai-image-pro::home.shared-components.image-modal', ['community_enabled' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('ai-image-pro::includes.scripts.image-pro-manager-script', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->make('panel.layout.app', [
    'disable_tblr' => true,
    'disable_default_sidebar' => true,
    'disable_titlebar' => true,
    'disable_footer' => true,
    'layout_wide' => true,
], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/index.blade.php ENDPATH**/ ?>