<?php

namespace ProductsOrdering\Views;

use ProductsOrdering\Models\OrderModel;

/**
 * @implements View<OrderModel>
 */
class OrderColumnContentView implements View
{
    public function __construct()
    {
    }

    public function render($model): void
    {
        $value = $model->get_order();
        $product_id = $model->get_product_id();
        ob_start();
        ?>
        <span>
            <input type="number" 
            class="order-input" 
            data-product-id="<?php echo esc_attr($product_id); ?>" 
            value="<?php echo esc_attr($value); ?>" 
            style="width:60px;text-align:center;" 
            min="0" 
            step="1">
            </span>
        <style>
            th {
                width: 100px;
            }
        </style>
        <?php
        echo ob_get_clean();
    }
}