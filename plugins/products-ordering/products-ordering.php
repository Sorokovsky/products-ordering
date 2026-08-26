<?php
/*
Plugin Name: Сортування товарів
Description: Сортування товарів для інтернет магазинів.
Version: 0.0.0
Requires at least: 5.8
Requires PHP: 8.1
Requires Plugins: woocommerce
Author: Sorokovskys
Text Domain: products-ordering
*/

namespace ProductsOrdering;

use ProductsOrdering\Controllers\OrderEditorController;
use ProductsOrdering\Controllers\OrderingController;
use ProductsOrdering\Models\OrderModel;
use ProductsOrdering\Views\OrderColumnContentView;
use ProductsOrdering\Views\View;

if (!defined("ABSPATH")) {
    exit;
}

spl_autoload_register(function (string $class) {
    $prefix = 'ProductsOrdering\\';
    $base_dir = __DIR__ . '/';
    $length = strlen($prefix);
    if (strncmp($prefix, $class, $length) !== 0) {
        return;
    }
    $relative_class = substr($class, $length);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    } else {
        die("ProductsOrdering: Файл не знайдено - " . $file);
    }
});

class ProductsOrderingPlugin
{
    private readonly OrderingController $ordering_controller;
    private readonly OrderEditorController $order_editor_controller;

    /**
     * @var View<OrderModel>
     */
    private readonly View $order_content_view;

    public function activate(): void
    {
        $this->ordering_controller->setup_order();
    }

    public function deactivate(): void
    {

    }

    public function __construct()
    {
        $this->init_parsers();
        $this->init_repositories();
        $this->init_services();
        $this->init_views();
        $this->init_controllers();
        $this->register_hooks();
    }

    private function init_parsers(): void
    {

    }

    private function init_repositories(): void
    {
    }

    private function init_services(): void
    {

    }

    private function init_views(): void
    {
        $this->order_content_view = new OrderColumnContentView();
    }

    private function init_controllers(): void
    {
        $this->ordering_controller = new OrderingController($this->order_content_view);
        $this->order_editor_controller = new OrderEditorController();
    }

    private function register_hooks(): void
    {
        add_filter('manage_edit-product_columns', [$this->ordering_controller, 'add_order_column'], 10, 1);
        add_action('manage_product_posts_custom_column', [$this->ordering_controller, 'display_order_content'], 10, 2);
        add_filter('manage_edit-product_sortable_columns', [$this->ordering_controller, 'make_order_column_editable']);
        add_filter('manage_edit-product_sortable_columns', [$this->ordering_controller, 'make_columns_sortable']);
        add_action('pre_get_posts', [$this->ordering_controller, 'order_products_by_meta']);
        add_action('wp_ajax_save_order_value', [$this->order_editor_controller, 'save_order_ajax']);
        add_action('admin_footer', [$this->order_editor_controller, 'admin_footer_js']);
    }
}


$plugin = new ProductsOrderingPlugin();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);
