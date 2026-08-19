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

use ProductsOrdering\Constants\PluginConstants;
use ProductsOrdering\Controllers\AdminController;
use ProductsOrdering\Views\AdminPageView;

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
    private readonly AdminController $admin_controller;

    private readonly AdminPageView $admin_page_view;

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

    public function add_settings_page(): void
    {
        $title = __(PluginConstants::TITLE, PluginConstants::DOMAIN);
        add_menu_page(
            $title,
            $title,
            PluginConstants::ACCESS,
            PluginConstants::SLUG,
            [$this->admin_controller, "render_admin_page"],
            'dashicons-admin-generic',
            25
        );
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
        $this->admin_page_view = new AdminPageView();
    }

    private function init_controllers(): void
    {
        $this->admin_controller = new AdminController($this->admin_page_view);
    }

    private function register_hooks(): void
    {
        add_action("admin_menu", [$this, 'add_settings_page']);
    }
}

$plugin = new ProductsOrderingPlugin();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);