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

    }

    private function init_services(): void
    {

    }

    private function init_views(): void
    {

    }

    private function init_controllers(): void
    {

    }

    private function register_hooks(): void
    {

    }
}

$plugin = new ProductsOrderingPlugin();
register_activation_hook(__FILE__, [$plugin, 'activate']);
register_deactivation_hook(__FILE__, [$plugin, 'deactivate']);