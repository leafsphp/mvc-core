<?php

namespace Leaf;

/**
 * Middleware
 *
 * @package Leaf
 * @author  Michael Darko
 * @since   1.5.0
 */
abstract class Middleware
{
    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     */
    abstract public function call();
}
