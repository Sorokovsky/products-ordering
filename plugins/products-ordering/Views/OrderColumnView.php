<?php
namespace ProductsOrdering\Views;

/**
 * @implements View<string>
 */
class OrderColumnView implements View
{
    public function __construct()
    {
    }


    public function render($value): void
    {
        if (!empty($value) && intval($value) > 0) {
            echo '<span style="font-weight: bold; color: #2271b1; display: inline-block; padding: 2px 8px; background: #f0f6fc; border-radius: 4px;">' . esc_html($value) . '</span>';
        } else {
            // Якщо значення немає, показуємо прочерк
            echo '<span style="color: #999; display: inline-block; padding: 2px 8px;">—</span>';
        }
    }
}
