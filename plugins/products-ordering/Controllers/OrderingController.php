<?php

namespace ProductsOrdering\Controllers;

use ProductsOrdering\Constants\PluginConstants;
use ProductsOrdering\Views\View;
use WP_Query;

class OrderingController
{
    /**
     * @var View<string>
     */
    private readonly View $order_content_view;

    /**
     * @param View<string> $order_content_view
     */
    public function __construct(View $order_content_view)
    {
        $this->order_content_view = $order_content_view;
    }

    public function add_order_column(array $columns): array
    {
        $result = array();
        foreach ($columns as $key => $value) {
            $result[$key] = $value;
        }
        $result[PluginConstants::ORDER_SLUG] = __(PluginConstants::ORDER_TITLE, PluginConstants::DOMAIN);
        $result[PluginConstants::RATING_SLUG] = __(PluginConstants::RATING_TITLE, PluginConstants::DOMAIN);
        return $result;
    }

    public function display_order_content(string $column, int $product_id): void
    {
        if ($column === PluginConstants::ORDER_SLUG) {
            $value = get_post_meta($product_id, PluginConstants::ORDER_METABOX_SLUG, true);
            $this->order_content_view->render($value);
        }
        if ($column === PluginConstants::RATING_SLUG) {
            $product = wc_get_product($product_id);
            $average = $product->get_average_rating();
            $rating_count = $product->get_rating_count();
        
        if ($average > 0) {
            echo \wc_get_rating_html($average);
            echo ' <span style="color:#999;font-size:11px;">(' . esc_html($rating_count) . ')</span>';
        } else {
            echo '<span style="color:#ccc;">0</span>';
        }
        }
    }

    public function setup_order(): void
    {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'post_status' => 'publish'
        ];

        $products = get_posts($args);
        foreach ($products as $product) {
            $order = get_post_meta($product, PluginConstants::ORDER_SLUG, true);
            if (empty($order)) {
                update_post_meta($product, PluginConstants::ORDER_SLUG, 0);
            }
        }
    }

    public function make_order_column_editable(array $columns): array
    {
        $columns[PluginConstants::ORDER_SLUG] = PluginConstants::ORDER_METABOX_SLUG;
        return $columns;
    }

    public function make_order_column_sortable($columns)
    {
        $columns[PluginConstants::ORDER_SLUG] = PluginConstants::ORDER_METABOX_SLUG;
        return $columns;
    }

    public function order_products_by_meta(WP_Query $query)
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $orderby = $query->get('orderby');
        if ($orderby === PluginConstants::ORDER_METABOX_SLUG) {
            $query->set('meta_key', PluginConstants::ORDER_METABOX_SLUG);
            $query->set('orderby', 'meta_value_num');
        }
    }
}