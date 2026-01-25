<li class="<?= classes('menu-item', $this->classes) ?>" <?= attributes($this->attributes) ?>>
    <span class="menu-item__wrap">
        <?= \Geum\Components\Link::make(...$this->link); ?>

        <?php if ($this->display_submenu === true) { ?>
            <?= \Geum\Components\Button::make(...$this->button); ?>
        <?php } ?>
    </span>

    <?php if ($this->display_submenu === true) { ?>
        <div <?= \Geum\Helpers::buildAttributes($this->{'sub-menu-attributes'}) ?>>
            <?= \Geum\Components\Menu\MenuList::make(
                items: $this->item->children,
                depth: ($this->depth + 1),
                max_depth: $this->max_depth,
            ); ?>
        </div>
    <?php } ?>
</li>
