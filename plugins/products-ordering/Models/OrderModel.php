<?php
namespace ProductsOrdering\Models;

class OrderModel
{
    private readonly int $order;
    private readonly int $product_id;

    public function __construct(int $order, int $product_id)
    {
        $this->order = $order;
        $this->product_id = $product_id;
    }

    public function get_order(): int
    {
        return $this->order;
    }

    public function get_product_id(): int
    {
        return $this->product_id;
    }
}