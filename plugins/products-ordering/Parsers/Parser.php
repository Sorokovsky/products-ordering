<?php

namespace ProductsOrdering\Parsers;

/**
 * @template T
 */
interface Parser
{
    /**
     * Summary of parse
     * @param mixed $input
     * @return T
     */
    public function parse(mixed $input);
}