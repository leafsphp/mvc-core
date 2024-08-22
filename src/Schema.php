<?php

namespace Leaf;

use Illuminate\Database\Schema\Blueprint;

class Schema
{
    public static $capsule;

    /**
     * @param string $table The name of table to manipulate
     * @param string|null $schema The JSON schema for database
     */
    public static function build(string $table, ?string $schema = null)
    {
        list($table, $schema) = static::getSchema($table, $schema);

        if (is_array($schema)) {
            $schema = $schema[0];
        }

        if (!static::$capsule::schema()->hasTable($table)) {
            static::$capsule::schema()->create($table, function (Blueprint $table) use ($schema) {
                foreach ($schema as $key => $value) {
                    list($key, $type) = static::getColumns($key, $value);

                    echo $key . " => " . $type . "\n";

                    if (strpos($key, '*') === 0) {
                        $table->foreignId(substr($key, 1));
                        continue;
                    }

                    if ($key === 'timestamps') {
                        $table->timestamps();
                        continue;
                    }

                    if ($key === 'softDeletes') {
                        $table->softDeletes();
                        continue;
                    }

                    if ($key === 'rememberToken') {
                        $table->rememberToken();
                        continue;
                    }

                    if ($type === 'enum') {
                        if (substr($key, -1) === '?') {
                            $table->enum(substr($key, 0, -1), $value)->nullable();
                            continue;
                        }

                        $table->enum($key, $value);
                        continue;
                    }

                    if (method_exists($table, $type)) {
                        if (substr($key, -1) === '?') {
                            call_user_func_array([$table, $type], [substr($key, 0, -1)])->nullable();
                            continue;
                        }

                        call_user_func_array([$table, $type], [$key]);
                        continue;
                    }
                }
            });
        }
    }

    /**
     * Get the table and table structure
     * @param string $table The name of table to manipulate (can be the name of the file)
     * @param string|null $schema The JSON schema for database (if $table is not the name of the file)
     *
     * @return array
     */
    protected static function getSchema(string $table, ?string $schema = null): array
    {
        try {
            if ($schema === null) {
                if (file_exists($table)) {
                    $schema = file_get_contents($table);
                    $table = str_replace('.json', '', basename($table));
                } else {
                    $table = str_replace('.json', '', $table);
                    $schema = json_decode(file_get_contents(AppPaths('schema') . "/$table.json"));
                }
            } else {
                $schema = json_decode($schema);
            }

            return [$table, $schema];
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Get the columns of a table and their types
     *
     * @param string $key The column as provided in the schema
     * @param mixed $value The value of the column as provided in the schema
     *
     * @return array
     */
    protected static function getColumns(string $key, $value): array
    {
        $type = '';
        $column = '';

        $keyData = explode(':', $key);

        if (count($keyData) > 1) {
            $type = trim($keyData[1]);
            $column = trim($keyData[0]);

            if ($type === 'id') {
                $column .= '*';
                $type = 'bigIncrements';
            } else if ($type === 'number') {
                $type = 'integer';
            } else if ($type === 'bool') {
                $type = 'boolean';
            }

            if (gettype($value) === 'NULL' && rtrim($column, '*') !== $column) {
                $column .= '?';
            }

            return [$column, $type];
        }

        echo $key . " => " . $value . "\n";

        if (
            (strtolower($key) === 'id' && gettype($value) === 'integer') ||
            (strpos(strtolower($key), '_id') !== false && gettype($value) === 'integer')
        ) {
            return [$key, 'bigIncrements'];
        }

        if (
            strpos(ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $key)), '_'), '_at') !== false ||
            strpos(ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $key)), '_'), '_date') !== false ||
            strpos(ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $key)), '_'), '_time') !== false ||
            strpos($key, 'timestamp') === 0 ||
            strpos($key, 'time') === 0 ||
            strpos($key, 'date') === 0
        ) {
            return [$key, 'timestamp'];
        }

        if (gettype($value) === 'integer') {
            return [$key, 'integer'];
        }

        if (gettype($value) === 'double') {
            return [$key, 'float'];
        }

        if (gettype($value) === 'string') {
            if (strpos($value, '{') === 0 || strpos($value, '[') === 0) {
                return [$key, 'json'];
            }

            if ($key === 'description' || $key === 'text' || strlen($value) > 150) {
                return [$key, 'text'];
            }

            return [$key, 'string'];
        }

        if (gettype($value) === 'array') {
            return [$key, 'enum'];
        }

        if (gettype($value) === 'boolean') {
            return [$key, 'boolean'];
        }

        return [$column, $type];
    }
}
