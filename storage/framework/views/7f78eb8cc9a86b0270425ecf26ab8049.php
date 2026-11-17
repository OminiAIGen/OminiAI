<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('landing-page.banner.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->renderWhen($fSectSettings->testimonials_active == 1, 'landing-page.clients.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->features_active == 1, 'landing-page.features.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->generators_active == 1, 'landing-page.generators.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen(setting('marketing_tools', '1') === '1', 'landing-page.marketing-tools.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->tools_active == 1, 'landing-page.tools.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen(setting('marquee_active', '1') === '1', 'landing-page.marquee.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen(setting('vertical_slider_active', '1') === '1', 'landing-page.vertical-slider.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->custom_templates_active == 1, 'landing-page.custom-templates.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->how_it_works_active == 1, 'landing-page.how-it-works.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->testimonials_active == 1, 'landing-page.testimonials.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->pricing_active == 1, 'landing-page.pricing.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->faq_active == 1, 'landing-page.faq.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->who_is_for_active == 1, 'landing-page.who-is-for.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($fSectSettings->blog_active == 1, 'landing-page.blog.section', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

    <?php echo $__env->renderWhen($setting->gdpr_status == 1, 'landing-page.gdpr', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('script'); ?>
    <script src="<?php echo e(custom_theme_url('/assets/libs/gsap/minified/gsap.min.js')); ?>"></script>
    <script src="<?php echo e(custom_theme_url('/assets/libs/gsap/minified/Observer.min.js')); ?>"></script>
    <script src="<?php echo e(custom_theme_url('/assets/libs/gsap/minified/ScrollTrigger.min.js')); ?>"></script>
    <script src="<?php echo e(custom_theme_url('/assets/libs/gsap/minified/ScrollToPlugin.min.js')); ?>"></script>
    <script src="<?php echo e(custom_theme_url('/assets/libs/gsap/minified/SplitText.min.js')); ?>"></script>
    <script src="<?php echo e(custom_theme_url('/assets/libs/flickity.pkgd.min.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\omini_customization\OminiAI\resources\views/marketing-bot/index.blade.php ENDPATH**/ ?>