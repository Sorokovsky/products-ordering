<?php
namespace ProductsOrdering\Views;

use ProductsOrdering\Constants\PluginConstants;

/**
 * @implements View<string>
 */
class OrderMetaboxView implements View
{
    public function __construct()
    {
    }

    public function render($value): void
    {
        ?>
                    <p>
                        <label for="<?php echo PluginConstants::ORDER_SLUG; ?>">
                            <strong>Числове значення:</strong>
                        </label>
                        <br>
                        <input type="number"
                               id="<?php echo PluginConstants::ORDER_SLUG; ?>"
                               name="<?php echo PluginConstants::ORDER_SLUG; ?>"
                               value="<?php echo esc_attr($value); ?>"
                               style="width: 100%;"
                               step="1"
                               min="0" />
                        <br>
                        <small>Менше число = вище в списку</small>
                    </p>
                    <?php
    }
}
