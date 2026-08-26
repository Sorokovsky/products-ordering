<?php
namespace ProductsOrdering\Repositories;

use ProductsOrdering\Constants\PluginConstants;

class OrderingRepository
{
    public function __construct()
    {
    }

    public function create_sort_field(array $columns): array
    {
        $new_columns = array();
        foreach($columns as $key => $value)
        {
            $new_columns[$key] = $value;
        }
        if (!isset($new_columns[PluginConstants::ORDER_SLUG])) {
            $new_columns[PluginConstants::ORDER_SLUG] = PluginConstants::ORDER_TITLE;
        }
        return $new_columns;
    }
}
