<?php

if (! empty($this->output)) {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() output is already escaped
    echo $this->output;
}
