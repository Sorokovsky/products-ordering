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
    private readonly View $order_column_view;

    /**
     * @var View<string>
     */
    private readonly View $order_metabox_view;

    /**
     * @param View<string> $order_column_view
     * @param View<string> $order_metabox_view
     */
    public function __construct(View $order_column_view, View $order_metabox_view)
    {
        $this->order_column_view = $order_column_view;
        $this->order_metabox_view = $order_metabox_view;

    }

    public function render_order_column_content(string $column, int $post_id): void
    {
        if ( $column === 'custom_order' ) {
                $order_value = get_post_meta( $post_id, PluginConstants::ORDER_SLUG, true);

                $this->order_column_view->render($order_value);
            }
    }

    public function add_metabox(): void
    {
        add_meta_box(
        PluginConstants::ORDER_METABOX_SLUG,
        PluginConstants::ORDER_TITLE,
        [$this, 'render_metabox'],
        'product',
        'side',
        'default'
        );
    }

    public function render_metabox(mixed $post): void
    {
        $value = get_post_meta($post->ID, PluginConstants::ORDER_SLUG, true);
        if (empty($value) || $value === '')
        {
            $value = '0';
        }
        wp_nonce_field('custom_order_nonce', 'custom_order_nonce');
        $this->order_metabox_view->render($value);
    }

    public function make_custom_order_column_sortable(array $columns): array
    {
        $columns[PluginConstants::ORDER_SLUG] = PluginConstants::ORDER_SLUG;
        return $columns;
    }

    public function sort_products_by_custom_order_column(WP_Query $query) {
        // Перевіряємо, чи це адмінка і чи це основний запит
        if ( ! is_admin() || ! $query->is_main_query() ) {
            return;
        }

        // Перевіряємо, чи це сторінка списку товарів
        if ( $query->get( 'post_type' ) !== 'product' ) {
            return;
        }

        // Отримуємо параметр сортування з URL
        $orderby = $query->get( 'orderby' );

        // Якщо сортують за нашим стовпцем
        if ( $orderby === 'custom_order' ) {
            // Додаємо сортування за мета-полем _custom_order
            $query->set( 'meta_key', '_custom_order' );
            $query->set( 'orderby', 'meta_value_num' );

            // За замовчуванням сортуємо за зростанням (ASC)
            if ( empty( $query->get( 'order' ) ) ) {
                $query->set( 'order', 'ASC' );
            }
        }
    }
}
