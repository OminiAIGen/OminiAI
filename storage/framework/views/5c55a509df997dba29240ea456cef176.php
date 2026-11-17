<?php $__env->startSection('error_code', '404'); ?>
<?php $__env->startSection('error_title', __('Looks like you’re lost.')); ?>
<?php $__env->startSection('error_subtitle', __('We can’t seem to find the page you’re looking for.')); ?>

<?php echo $__env->make('layout.error', ['disable_navbar' => true, 'disable_header' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/errors/404.blade.php ENDPATH**/ ?>