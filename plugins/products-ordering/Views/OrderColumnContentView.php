<?php

namespace ProductsOrdering\Views;

/**
 * @implements View<string>
 */
class OrderColumnContentView implements View
{
    public function __construct()
    {
    }

    public function render($value): void
    {
        ob_start();
        if (!empty($value) || $value === '0') {
            echo esc_html($value);
        } else {
            echo '-';
        }
        ?>
        <style>
            th {
                width: 100px;
            }
        </style>
        <?php
        echo ob_get_clean();
    }
}