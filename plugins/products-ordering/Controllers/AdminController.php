<?php

namespace ProductsOrdering\Controllers;

use ProductsOrdering\Views\View;

class AdminController
{
    /**
     * @var View<null>
     */
    private readonly View $page_view;

    /**
     * @param View<null> $page_view
     */
    public function __construct(View $page_view)
    {
        $this->page_view = $page_view;
    }

    public function render_admin_page(): void
    {
        $this->page_view->render(null);
    }
}