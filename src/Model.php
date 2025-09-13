<?php

namespace Leaf;

Database::connect();

class Model extends \Illuminate\Database\Eloquent\Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Define the default seeder for the model
     * @return array
     */
    public static function __seeder()
    {
        return [];
    }
}
