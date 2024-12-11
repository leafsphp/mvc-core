<?php

namespace Leaf;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Yaml\Yaml;

/**
 * Leaf DB Schema [WIP]
 * ---
 * One file to rule them all.
 * 
 * @version 1.0
 */
class Schema
{
    /**@var \Illuminate\Database\Capsule\Manager $capsule */
    protected static Manager $connection;

    /**
     * Migrate your schema file tables
     * 
     * @param string $fileToMigrate The schema file to migrate
     * @return bool
     */
    public static function migrate(string $fileToMigrate): bool
    {
        $data = Yaml::parseFile($fileToMigrate);
        $tableName = rtrim(path($fileToMigrate)->basename(), '.yml');

        if ($data['truncate'] ?? false) {
            static::$connection::schema()->dropIfExists($tableName);
        }

        try {
            if (!static::$connection::schema()->hasTable($tableName)) {
                if (storage()->exists(StoragePath("database/$tableName"))) {
                    storage()->delete(StoragePath("database/$tableName"));
                }

                static::$connection::schema()->create($tableName, function (Blueprint $table) use ($data) {
                    $columns = $data['columns'] ?? [];
                    $relationships = $data['relationships'] ?? [];

                    $increments = $data['increments'] ?? true;
                    $timestamps = $data['timestamps'] ?? true;
                    $softDeletes = $data['softDeletes'] ?? false;
                    $rememberToken = $data['remember_token'] ?? false;

                    if ($increments) {
                        $table->increments('id');
                    }

                    foreach ($relationships as $model) {
                        $table->foreignIdFor($model);
                    }

                    foreach ($columns as $columnName => $columnValue) {
                        if (is_string($columnValue)) {
                            $table->{$columnValue}($columnName);
                        }

                        if (is_array($columnValue)) {
                            $column = $table->{$columnValue['type']}($columnName);

                            unset($columnValue['type']);

                            foreach ($columnValue as $columnOptionName => $columnOptionValue) {
                                if (is_bool($columnOptionValue)) {
                                    $column->{$columnOptionName}();
                                } else {
                                    $column->{$columnOptionName}($columnOptionValue);
                                }
                            }
                        }
                    }

                    if ($rememberToken) {
                        $table->rememberToken();
                    }

                    if ($softDeletes) {
                        $table->softDeletes();
                    }

                    if ($timestamps) {
                        $table->timestamps();
                    }
                });
            } else if (storage()->exists(StoragePath("database/$tableName"))) {
                static::$connection::schema()->table($tableName, function (Blueprint $table) use ($data, $tableName) {
                    $columns = $data['columns'] ?? [];
                    $relationships = $data['relationships'] ?? [];

                    $allPreviousMigrations = glob(StoragePath("database/$tableName/*.yml"));
                    $lastMigration = $allPreviousMigrations[count($allPreviousMigrations) - 1] ?? null;
                    $lastMigration = Yaml::parseFile($lastMigration);

                    $increments = $data['increments'] ?? true;
                    $timestamps = $data['timestamps'] ?? true;
                    $softDeletes = $data['softDeletes'] ?? false;
                    $rememberToken = $data['remember_token'] ?? false;

                    if ($increments !== ($lastMigration['increments'] ?? true)) {
                        if ($increments && !static::$connection::schema()->hasColumn($tableName, 'id')) {
                            $table->increments('id');
                        } else if (!$increments && static::$connection::schema()->hasColumn($tableName, 'id')) {
                            $table->dropColumn('id');
                        }
                    }

                    $columnsDiff = [];
                    $staticColumns = [];
                    $removedColumns = [];

                    foreach ($lastMigration['columns'] as $colKey => $colVal) {
                        if (!array_key_exists($colKey, $columns)) {
                            $removedColumns[] = $colKey;
                        } else if (static::getColumnAttributes($colVal) !== static::getColumnAttributes($columns[$colKey])) {
                            $columnsDiff[] = $colKey;
                            $staticColumns[] = $colKey;
                        } else {
                            $staticColumns[] = $colKey;
                        }
                    }

                    $newColumns = array_diff(array_keys($columns), $staticColumns);

                    if (count($newColumns) > 0) {
                        foreach ($newColumns as $newColumn) {
                            $column = static::getColumnAttributes($columns[$newColumn]);

                            if (!static::$connection::schema()->hasColumn($tableName, $newColumn)) {
                                $newCol = $table->{$column['type']}($newColumn);

                                unset($column['type']);

                                foreach ($column as $columnOptionName => $columnOptionValue) {
                                    if (is_bool($columnOptionValue)) {
                                        if ($columnOptionValue) {
                                            $newCol->{$columnOptionName}();
                                        }
                                    } else {
                                        $newCol->{$columnOptionName}($columnOptionValue);
                                    }
                                }
                            }
                        }
                    }

                    if (count($columnsDiff) > 0) {
                        foreach ($columnsDiff as $changedColumn) {
                            $column = static::getColumnAttributes($columns[$changedColumn]);
                            $prevMigrationColumn = static::getColumnAttributes($lastMigration['columns'][$changedColumn] ?? []);

                            if ($column['type'] === 'timestamp') {
                                continue;
                            }

                            $newCol = $table->{$column['type']}(
                                $changedColumn,
                                ($column['type'] === 'string') ? $column['length'] : null
                            );

                            unset($column['type']);

                            foreach ($column as $columnOptionName => $columnOptionValue) {
                                if ($columnOptionValue === $prevMigrationColumn[$columnOptionName]) {
                                    continue;
                                }

                                if ($columnOptionName === 'unique') {
                                    if ($columnOptionValue) {
                                        $newCol->unique()->change();
                                    } else {
                                        $table->dropUnique("{$tableName}_{$changedColumn}_unique");
                                    }

                                    continue;
                                }

                                if ($columnOptionName === 'index') {
                                    if ($columnOptionValue) {
                                        $newCol->index()->change();
                                    } else {
                                        $table->dropIndex("{$tableName}_{$changedColumn}_index");
                                    }

                                    continue;
                                }

                                // skipping this for now, primary + autoIncrement
                                // doesn't work well in the same run. They need to be
                                // run separately for some reason
                                // if ($columnOptionName === 'autoIncrement') {

                                if ($columnOptionName === 'primary') {
                                    if ($columnOptionValue) {
                                        $newCol->primary()->change();
                                    } else {
                                        $table->dropPrimary("{$tableName}_{$changedColumn}_primary");
                                    }

                                    continue;
                                }

                                if ($columnOptionName === 'default') {
                                    $newCol->default($columnOptionValue)->change();
                                    continue;
                                }

                                if (is_bool($columnOptionValue)) {

                                    if ($columnOptionValue) {
                                        $newCol->{$columnOptionName}()->change();
                                    } else {
                                        $newCol->{$columnOptionName}(false)->change();
                                    }
                                } else {
                                    $newCol->{$columnOptionName}($columnOptionValue)->change();
                                }
                            }

                            $newCol->change();
                        }
                    }

                    if (count($removedColumns) > 0) {
                        foreach ($removedColumns as $removedColumn) {
                            if (static::$connection::schema()->hasColumn($tableName, $removedColumn)) {
                                $table->dropColumn($removedColumn);
                            }
                        }
                    }

                    if ($rememberToken !== ($lastMigration['remember_token'] ?? false)) {
                        if ($rememberToken && !static::$connection::schema()->hasColumn($tableName, 'remember_token')) {
                            $table->rememberToken();
                        } else if (!$rememberToken && static::$connection::schema()->hasColumn($tableName, 'remember_token')) {
                            $table->dropRememberToken();
                        }
                    }

                    if ($softDeletes !== ($lastMigration['softDeletes'] ?? false)) {
                        if ($softDeletes && !static::$connection::schema()->hasColumn($tableName, 'deleted_at')) {
                            $table->softDeletes();
                        } else if (!$softDeletes && static::$connection::schema()->hasColumn($tableName, 'deleted_at')) {
                            $table->dropSoftDeletes();
                        }
                    }

                    if ($timestamps !== ($lastMigration['timestamps'] ?? true)) {
                        if ($timestamps && !static::$connection::schema()->hasColumn($tableName, 'created_at')) {
                            $table->timestamps();
                        } else if (!$timestamps && static::$connection::schema()->hasColumn($tableName, 'created_at')) {
                            $table->dropTimestamps();
                        }
                    }
                });
            }

            storage()->copy(
                $fileToMigrate,
                StoragePath('database' . '/' . $tableName . '/' . tick()->format('YYYY_MM_DD_HHmmss[.yml]')),
                ['recursive' => true]
            );
        } catch (\Throwable $th) {
            throw $th;
        }

        return true;
    }

    /**
     * Seed a database table from schema file
     * 
     * @param string $fileToSeed The name of the schema file
     * @return bool
     */
    public static function seed(string $fileToSeed): bool
    {
        $data = Yaml::parseFile($fileToSeed);
        return true;
    }

    /**
     * Get all column attributes
     */
    public static function getColumnAttributes($value)
    {
        $attributes = [
            'type' => 'string',
            'length' => null,
            'nullable' => false,
            'default' => null,
            'unsigned' => false,
            'index' => false,
            'unique' => false,
            'primary' => false,
            'foreign' => false,
            'foreignTable' => null,
            'foreignColumn' => null,
            'onDelete' => null,
            'onUpdate' => null,
            'comment' => null,
            'autoIncrement' => false,
            'useCurrent' => false,
            'useCurrentOnUpdate' => false,
        ];

        if (is_string($value)) {
            $attributes['type'] = $value;
        } else if (is_array($value)) {
            $attributes = array_merge($attributes, $value);
        }

        return $attributes;
    }

    /**
     * Set the internal db connection
     * @param mixed $connection
     * @return void
     */
    public static function setDbConnection($connection)
    {
        static::$connection = $connection;
    }
}
