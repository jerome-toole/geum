<?php if (! empty($this->content)) { ?>
    <<?= esc_html($this->el); ?> class="<?= classes('element', $this->classes) ?>" <?= attributes($this->attributes) ?>><?php
        $filter = $this->content_filter;
    echo is_callable($filter) ? $filter($this->content) : $this->content;
    ?></<?= esc_html($this->el); ?>>
<?php }
