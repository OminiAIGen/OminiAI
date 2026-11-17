<?php
    $isChatImageRoute = request()->is('ai-image-pro*');
    $themeNavbar = setting('dash_theme', 'default');
    $navbarView = "{$themeNavbar}.components.navbar.navbar";
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isChatImageRoute): ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($themeNavbar === 'default'): ?>
        <?php echo $__env->make('ai-image-pro::quick-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif(View::exists($navbarView)): ?>
        <?php echo $__env->make($navbarView, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('components.navbar.navbar-default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php else: ?>
    <?php echo $__env->make('components.navbar.navbar-default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/navbar/navbar.blade.php ENDPATH**/ ?>