<ul class="<?= classes('menu-list', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <?php foreach ($this->items as $key => $item) { ?>
        <?= \Geum\Components\Menu\MenuItem::make(
            item: $item,
            depth: $this->depth,
            max_depth: $this->max_depth,
        ); ?>
    <?php } ?>
</ul>
