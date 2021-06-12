<?php

/**
 * Leaf MVC Core
 * ----------
 * Base class for configuring core methods
 */
class Core
{
    protected static $paths;

    public function paths($paths = null)
    {
        if (!$paths) {
            return static::$paths;
        }

        static::$paths = $paths;
    }
}
