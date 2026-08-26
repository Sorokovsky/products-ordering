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

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use ProductsOrdering\Controllers\OrderingController;
use ProductsOrdering\Repositories\OrderingRepository;
use ProductsOrdering\Views\OrderColumnView;
use ProductsOrdering\Views\OrderMetaboxView;

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

    private readonly OrderColumnView $order_column_view;

    private readonly OrderMetaboxView $order_metabox_view;

    private readonly OrderingRepository $ordering_repository;

    private bool $isHpos = false;

    public function activate(): void
    {

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
        $this->ordering_repository = new OrderingRepository();
    }

    private function init_services(): void
    {

    }

    private function init_views(): void
    {
        $this->order_column_view = new OrderColumnView();
        $this->order_metabox_view = new OrderMetaboxView();
    }

    private function init_controllers(): void
    {
        $this->ordering_controller = new OrderingController($this->order_column_view, $this->order_metabox_view);
    }

    private function register_hooks(): void
    {
        add_action("add_meta_boxes", [$this->ordering_controller, 'add_metabox']);
        add_filter("manage_edit-product_columns", [$this->ordering_repository, 'create_sort_field']);
        add_action('manage_product_posts_custom_column', [$this->ordering_controller, 'render_order_column_content'], 20, 2 );
        add_filter( 'manage_edit-product_sortable_columns', [$this->ordering_controller, 'make_custom_order_column_sortable']);
        add_action( 'pre_get_posts', [$this->ordering_controller, 'sort_products_by_custom_order_column']);
        add_filter('woocommerce_shop_order_list_table_columns', [$this->ordering_repository, 'create_sort_field'], 20);
        add_action('woocommerce_shop_order_list_table_custom_column', [$this->ordering_controller, 'render_order_column_content'], 10, 2);
        add_filter('woocommerce_shop_order_list_table_sortable_columns', [$this->ordering_controller, 'make_custom_order_column_sortable'], 20);
    }
}

$plugin = new ProductsOrderingPlugin();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);
// // Додайте в конструктор
// add_action('init', function() {
//     if (class_exists('Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController')) {
//         $controller = wc_get_container()->get(CustomOrdersTableController::class);
//         $is_hpos = $controller->custom_orders_table_usage_is_enabled();
//         die('=== HPOS enabled: ' . ($is_hpos ? 'YES' : 'NO') . ' ===');
//     }
// });
