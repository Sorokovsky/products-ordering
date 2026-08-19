<?php

namespace ProductsOrdering\Views;

/**
 * @template T
 */
interface View
{
    /**
     * @param T $parameters
     * @return void
     */
    public function render($parameters): void;
}