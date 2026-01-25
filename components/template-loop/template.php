<?php if (! empty($this->items)) { ?>
    <?= \Geum\Components\TaxonomyFilters::make(
        object: $this->object,
        show: $this->show_taxonomy_filters,
    ); ?>

    <?= new \Geum\Component($this->items_render_component, $this->items_render_component_args); ?>
    <?= \Geum\Components\Pagination::make(); ?>
<?php } else { ?>
    <?= \Geum\Components\NoContent::make(
        object: $this->object,
    ); ?>
<?php }
