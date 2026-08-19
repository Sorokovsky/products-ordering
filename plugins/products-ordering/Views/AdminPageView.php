<?php
namespace ProductsOrdering\Views;

/**
 * @implements View<null>
 */
class AdminPageView implements View
{
    public function __construct()
    {
    }


    public function render($_): void
    {
        ?>
        <h1>Сортування товарів</h1>
        <?php
    }
}