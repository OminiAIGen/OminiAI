<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($app_is_not_demo): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('<?php echo e(route('dashboard.user.check.payment')); ?>')
            .then(response => response.json());
    });
</script>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\omini_customization\OminiAI\resources\views/default/panel/layout/includes/subscription-status.blade.php ENDPATH**/ ?>