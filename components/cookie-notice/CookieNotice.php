<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * CookieNotice Component
 *
 * Usage:
 *   use Geum\Components\CookieNotice;
 *
 *   echo CookieNotice::make();
 */
class CookieNotice extends ComponentBase
{
    protected static string $name = 'cookie-notice';

    /**
     * Create a new CookieNotice component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        array $attributes = [],
        string $content = '',
        ?string $accept_button_text = null,
        ?string $accept_button_text_additional_context = null,
        ?string $reject_button_text = null,
        ?string $reject_button_text_additional_context = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        // Default translated values
        $args['accept_button_text'] ??= __('Accept', 'geum');
        $args['accept_button_text_additional_context'] ??= __('site cookies', 'geum');
        $args['reject_button_text'] ??= __('Reject', 'geum');
        $args['reject_button_text_additional_context'] ??= __('site cookies', 'geum');

        // ---------------------------------------
        // Default attributes.
        // ---------------------------------------
        $args['attributes'] = array_merge([
            'id' => 'site-cookie-notice',
            'aria-hidden' => 'true',
        ], $args['attributes']);

        if ($accept_button_text = get_field('cookie_notice_accept_button_text', 'option')) {
            $args['accept_button_text'] = $accept_button_text;
        }

        if (
            $accept_button_text_additional_context = get_field(
                'cookie_notice_accept_button_text_additional_context',
                'option'
            )
        ) {
            $args['accept_button_text_additional_context'] = $accept_button_text_additional_context;
        }

        if ($reject_button_text = get_field('cookie_notice_reject_button_text', 'option')) {
            $args['reject_button_text'] = $reject_button_text;
        }

        if (
            $reject_button_text_additional_context = get_field(
                'cookie_notice_reject_button_text_additional_context',
                'option'
            )
        ) {
            $args['reject_button_text_additional_context'] = $reject_button_text_additional_context;
        }

        $content = get_field('cookie_notice_text', 'option');
        if (! empty($content)) {
            $args['content'] = $content;
        } elseif (! empty(get_privacy_policy_url())) {
            $args['content'] = sprintf(
                __('We use cookies. Read more about them in our %s', 'geum'),
                Link::make(
                    content: _x('Privacy Policy', 'Cookie notice link text', 'geum'),
                    url: get_privacy_policy_url(),
                )
            );
        }

        return $args;
    }
}
