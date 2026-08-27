<?php

namespace ProductsOrdering\Controllers;

use ProductsOrdering\Constants\PluginConstants;
use ProductsOrdering\Models\OrderModel;
use ProductsOrdering\Views\View;
use WP_Query;

class OrderingController
{
    /**
     * @var View<OrderModel>
     */
    private readonly View $order_content_view;

    /**
     * @param View<OrderModel> $order_content_view
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
            $this->order_content_view->render(new OrderModel($value, $product_id));
        }
        if ($column === PluginConstants::RATING_SLUG) {
            $product = wc_get_product($product_id);
            $average = $product->get_average_rating();
        
        if ($average > 0) {
            echo ' <span>'. esc_html($average) .'</span>';
        } else {
            echo '<span>0</span>';
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

    public function make_columns_sortable(array $columns)
    {
        $columns[PluginConstants::ORDER_SLUG] = PluginConstants::ORDER_METABOX_SLUG;
        $columns[PluginConstants::RATING_SLUG] = PluginConstants::RATING_SLUG;
        return $columns;
    }

    public function order_products_by_meta(WP_Query $query)
    {
        if (!\is_admin() && $query->get('post_type') === 'product') {
    $order_meta_key = PluginConstants::ORDER_METABOX_SLUG;
    
    add_filter('posts_orderby', function($orderby, $wp_query) use ($query, $order_meta_key) {
        if ($wp_query !== $query) {
            return $orderby;
        }
        
        global $wpdb;
        $orderby = $wpdb->prepare(
            "
            COALESCE(
                (SELECT meta_value 
                 FROM {$wpdb->postmeta} 
                 WHERE post_id = {$wpdb->posts}.ID 
                 AND meta_key = '_wc_average_rating' 
                 LIMIT 1), 
                '0'
            ) DESC,
            COALESCE(
                (SELECT meta_value 
                 FROM {$wpdb->postmeta} 
                 WHERE post_id = {$wpdb->posts}.ID 
                 AND meta_key = %s 
                 LIMIT 1), 
                '0'
            ) ASC,
            {$wpdb->posts}.post_title ASC
            ",
            $order_meta_key
        );
        
        return $orderby;
    }, 10, 2);
    return;
}

        $orderby = $query->get('orderby');
        if ($orderby === PluginConstants::ORDER_METABOX_SLUG) {
            $query->set('meta_key', PluginConstants::ORDER_METABOX_SLUG);
            $query->set('orderby', 'meta_value_num');
        }
        if ($orderby === PluginConstants::RATING_SLUG) {
            $query->set('meta_key', '_wc_average_rating');
            $query->set('orderby', 'meta_value_num');
        }
    }
}