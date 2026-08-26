<?php

namespace ProductsOrdering\Controllers;

use ProductsOrdering\Constants\PluginConstants;

class OrderEditorController
{
    public function __construct()
    {
    }

    public function save_order_ajax(): void
    {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        $value = isset($_POST['value']) ? intval($_POST['value']) : 0;
        if ($product_id > 0) {
            update_post_meta($product_id, PluginConstants::ORDER_METABOX_SLUG, $value);
            wp_send_json_success(['message' => 'Updated']);
        }
        wp_die();
    }

    public function admin_footer_js(): void
    {
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.order-input').on('change', function() {
                var input = $(this);
                var productId = input.data('product-id');
                var value = input.val();
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                    action: 'save_order_value',
                    product_id: productId,
                    value: value,
                    nonce: '<?php echo \wp_create_nonce('products_ordering_nonce'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        input.css('border-color', 'green');
                        setTimeout(function() {
                            input.css('border-color', '');
                        }, 2000);
                    }
                },
                error: function() {
                    input.css('border-color', 'red');
                }
            });
        });
    });
    </script>
    <?php
    }
}