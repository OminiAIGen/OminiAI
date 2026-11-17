<div class="grid grid-cols-2 gap-0.5 md:grid-cols-3 lg:grid-cols-4">
    <template
        x-for="(image, index) in images"
        :key="image.id"
    >
        <?php echo $__env->make('ai-image-pro::includes.image-grid-item', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </template>
</div>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/app/Extensions/AIImagePro/System/../resources/views/includes/image-grid.blade.php ENDPATH**/ ?>