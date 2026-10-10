<?php
/*
Plugin Name: Сортування товарів
Description: Drag-and-drop сортування товарів WooCommerce прямо у списку товарів.
Version: 1.0.0
Requires at least: 5.8
Requires PHP: 8.1
Requires Plugins: woocommerce
Author: Sorokovskys
Text Domain: products-ordering
*/

namespace ProductsOrdering;

if (!defined("ABSPATH")) {
    exit;
}

class ProductsOrderingPlugin
{
    public function activate(): void {}
    public function deactivate(): void {}

    public function __construct()
    {
        $this->register_hooks();
    }

    private function register_hooks(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_notices', [$this, 'maybe_show_notice']);
        add_action('wp_ajax_save_product_order', [$this, 'ajax_save_product_order']);
        add_action('pre_get_posts', [$this, 'force_menu_order_sorting']);
    }

    public function force_menu_order_sorting(\WP_Query $query): void
{
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }

    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->id !== 'edit-product') {
        return;
    }

    // Якщо користувач явно обрав сортування — не втручаємось
    if (!empty($_GET['orderby'])) {
        return;
    }

    // Дефолтний стан списку товарів — сортуємо за menu_order
    $query->set('orderby', 'menu_order');
    $query->set('order', 'ASC');
}

    public function enqueue_admin_scripts(string $hook): void
    {
        // Нас цікавить лише сторінка списку записів
        if ($hook !== 'edit.php') {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-product') {
            return;
        }

        if (!current_user_can('edit_products')) {
            return;
        }

        wp_enqueue_script('jquery-ui-sortable');

        wp_register_style('products-ordering-admin', false, [], '1.0.0');
        wp_enqueue_style('products-ordering-admin');
        wp_add_inline_style('products-ordering-admin', '
            #the-list tr { cursor: move; }
            #the-list tr a,
            #the-list tr button,
            #the-list tr input,
            #the-list tr select,
            #the-list tr .check-column,
            #the-list tr .row-actions,
            #the-list tr .toggle-row { cursor: default; }
            .ui-sortable-helper {
                background: #fff !important;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            }
            .ui-sortable-placeholder {
                background: #f0f0f1 !important;
                visibility: visible !important;
            }
        ');

        wp_localize_script('jquery-ui-sortable', 'productSort', [
            'nonce'   => wp_create_nonce('product_sort_nonce'),
            'ajaxurl' => admin_url('admin-ajax.php'),
        ]);

        wp_add_inline_script('jquery-ui-sortable', $this->get_sortable_js());
    }

    private function get_sortable_js(): string
    {
        return <<<JS
        jQuery(function($) {
            var tbody = $('#the-list');
            if (!tbody.length) return;

            tbody.sortable({
                items: '> tr:not(.no-items)',
                cursor: 'move',
                axis: 'y',
                cancel: 'a, button, input, select, textarea, .check-column, .row-actions, .toggle-row',
                helper: function(e, tr) {
                    var originals = tr.children();
                    var helper = tr.clone();
                    helper.children().each(function(i) {
                        $(this).width(originals.eq(i).width());
                    });
                    return helper;
                },
                update: function() {
                    var order = [];
                    tbody.find('> tr').each(function() {
                        var id = $(this).attr('id') || '';
                        var m = id.match(/^post-(\\d+)$/);
                        if (m) order.push(m[1]);
                    });
                    if (!order.length) return;

                    $.post(productSort.ajaxurl, {
                        action: 'save_product_order',
                        nonce: productSort.nonce,
                        order: order
                    }, function(response) {
                        if (!response.success) {
                            alert('Помилка збереження порядку: ' + (response.data || ''));
                        }
                    });
                }
            });
        });
JS;
    }

    public function maybe_show_notice(): void
    {
        $screen = get_current_screen();
        if (!$screen || $screen->id !== 'edit-product') {
            return;
        }
        if (!current_user_can('edit_products')) {
            return;
        }

        $counts = wp_count_posts('product');
        $total  = isset($counts->publish) ? (int) $counts->publish : 0;
        $per_page = (int) apply_filters('edit_posts_per_page', 20, 'product');

        if ($total > $per_page) {
            $url = add_query_arg('posts_per_page', $total);
            echo '<div class="notice notice-warning is-dismissible"><p>';
            echo 'Сортування: показано ' . esc_html($per_page) . ' з ' . esc_html($total) . ' товарів. ';
            echo 'Перетягування змінює порядок лише в межах поточної сторінки. ';
            echo '<a href="' . esc_url($url) . '">Показати всі</a>';
            echo '</p></div>';
        }
    }

    public function ajax_save_product_order(): void
    {
        check_ajax_referer('product_sort_nonce', 'nonce');

        if (!current_user_can('edit_products')) {
            wp_send_json_error('Недостатньо прав', 403);
        }

        $order = isset($_POST['order']) ? (array) $_POST['order'] : [];
        if (empty($order)) {
            wp_send_json_error('Немає даних для сортування');
        }

        foreach ($order as $index => $product_id) {
            $product_id = absint($product_id);
            if (!$product_id) {
                continue;
            }
            if (get_post_type($product_id) !== 'product') {
                continue;
            }
            wp_update_post([
                'ID'         => $product_id,
                'menu_order' => $index,
            ]);
        }

        wp_send_json_success('Порядок збережено');
    }
}

$plugin = new ProductsOrderingPlugin();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);