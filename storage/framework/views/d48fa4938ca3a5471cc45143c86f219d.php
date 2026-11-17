<div
    <?php echo e($attributes->merge(['class' => 'masonry-grid-item'])); ?>

    x-data="{ init: () => $dispatch('masonry:appended', { item: $el }), destroy: () => $dispatch('masonry:remove', { item: $el }) }"
>
    <?php echo e($slot); ?>

</div>
<?php /**PATH /home/u504467222/domains/omini.ai/public_html/resources/views/default/components/masonry-grid-item.blade.php ENDPATH**/ ?>