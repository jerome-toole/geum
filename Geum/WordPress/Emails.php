<?php

namespace Geum\WordPress;

class Emails
{
    public static function init(): void
    {
        \add_action('wp_mail_from', [__CLASS__, 'filterWordPressFromEmailAddress']);
        \add_action('wp_mail_from_name', [__CLASS__, 'filterWordPressFromName']);
    }

    public static function filterWordPressFromEmailAddress($email): string
    {
        $default = str_replace('wordpress@', 'noreply@', $email);

        if (! function_exists('get_field')) {
            return $default;
        }

        $option = \get_field('site_email_address', 'option');

        if (empty($option)) {
            return $default;
        }

        return $option;
    }

    public static function filterWordPressFromName($name): string
    {
        $default = \get_bloginfo('name');

        if (! function_exists('get_field')) {
            return $default;
        }

        $option = \get_field('site_email_sender_name', 'option');

        if (empty($option)) {
            return $default;
        }

        return $option;
    }
}
