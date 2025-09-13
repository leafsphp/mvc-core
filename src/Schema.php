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
     * Get the database connection used in a schema file
     * @param string $fileName The schema file name
     * @return string|null
     */
    public static function getConnection(string $fileName): ?string
    {
        return Yaml::parseFile($fileName)['connection'] ?? null;
    }

    /**
     * Migrate your schema file tables
     * @param string $fileToMigrate The schema file to migrate
     * @return bool
     */
    public static function migrate(string $fileToMigrate): bool
    {
        $data = Yaml::parseFile($fileToMigrate);
        $tableName = str_replace('.yml', '', path($fileToMigrate)->basename());

        $currentConnection = $data['connection'] ?? null;

        try {
            if (!static::$connection::schema($currentConnection)->hasTable($tableName)) {
                if (storage()->exists(StoragePath("database/$tableName"))) {
                    storage()->delete(StoragePath("database/$tableName"));
                }

                static::$connection::schema($currentConnection)->create($tableName, function (Blueprint $table) use ($data) {
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
                        if (strpos($model, 'App\Models') === false) {
                            $model = "App\Models\\$model";
                        }

                        $table->foreignIdFor($model);
                    }

                    foreach ($columns as $columnName => $columnValue) {
                        static::createColumn($table, $columnName, $columnValue);
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
                static::$connection::schema($currentConnection)->table($tableName, function (Blueprint $table) use ($currentConnection, $data, $tableName) {
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
                        if ($increments && !static::$connection::schema($currentConnection)->hasColumn($tableName, 'id')) {
                            $table->increments('id');
                        } else if (!$increments && static::$connection::schema($currentConnection)->hasColumn($tableName, 'id')) {
                            $table->dropColumn('id');
                        }
                    }

                    if ($relationships !== ($lastMigration['relationships'] ?? [])) {
                        $newRelationships = array_diff($relationships, $lastMigration['relationships'] ?? []);
                        $removedRelationships = array_diff($lastMigration['relationships'] ?? [], $relationships);

                        foreach ($newRelationships as $model) {
                            if (strpos($model, 'App\Models') === false) {
                                $model = "App\Models\\$model";
                            }

                            $table->foreignIdFor($model);
                        }

                        foreach ($removedRelationships as $model) {
                            if (strpos($model, 'App\Models') === false) {
                                $model = "App\Models\\$model";
                            }

                            $foreignKey = static::$connection::getForeignKeyName($tableName, $model::getForeignKey());

                            if (static::$connection::schema($currentConnection)->hasColumn($tableName, $foreignKey)) {
                                $table->dropForeign($foreignKey);
                                $table->dropColumn($model::getForeignKey());
                            }
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

                    if ($rememberToken !== ($lastMigration['remember_token'] ?? false)) {
                        if ($rememberToken && !static::$connection::schema($currentConnection)->hasColumn($tableName, 'remember_token')) {
                            $table->rememberToken();
                        } else if (!$rememberToken && static::$connection::schema($currentConnection)->hasColumn($tableName, 'remember_token')) {
                            $table->dropRememberToken();
                        }
                    }

                    if ($softDeletes !== ($lastMigration['softDeletes'] ?? false)) {
                        if ($softDeletes && !static::$connection::schema($currentConnection)->hasColumn($tableName, 'deleted_at')) {
                            $table->softDeletes();
                        } else if (!$softDeletes && static::$connection::schema($currentConnection)->hasColumn($tableName, 'deleted_at')) {
                            $table->dropSoftDeletes();
                        }
                    }

                    if ($timestamps !== ($lastMigration['timestamps'] ?? true)) {
                        if ($timestamps && !static::$connection::schema($currentConnection)->hasColumn($tableName, 'created_at')) {
                            $table->timestamps();
                        } else if (!$timestamps && static::$connection::schema($currentConnection)->hasColumn($tableName, 'created_at')) {
                            $table->dropTimestamps();
                        }
                    }

                    if (count($removedColumns) > 0) {
                        foreach ($removedColumns as $removedColumn) {
                            if (static::$connection::schema($currentConnection)->hasColumn($tableName, $removedColumn)) {
                                $table->dropColumn($removedColumn);
                            }
                        }
                    }

                    $newColumns = array_diff(array_keys($columns), $staticColumns);

                    if (count($newColumns) > 0) {
                        foreach ($newColumns as $newColumn) {
                            $column = static::getColumnAttributes($columns[$newColumn]);

                            if (!static::$connection::schema($currentConnection)->hasColumn($tableName, $newColumn)) {
                                static::createColumn($table, $newColumn, $column);
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

                            $newCol = static::createColumn(
                                $table,
                                $changedColumn,
                                $column,
                                false
                            );

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
     * @param string $fileToSeed The name of the schema file
     * @return bool
     */
    public static function seed(string $fileToSeed): bool
    {
        $data = Yaml::parseFile($fileToSeed);
        $tableName = str_replace('.yml', '', path($fileToSeed)->basename());

        $seeds = $data['seeds'] ?? [];
        $currentConnection = $data['connection'] ?? null;

        $count = $seeds['count'] ?? 1;
        $seedsData = $seeds['data'] ?? [];
        $seedsModel = $seeds['model'] ?? null;

        $timestamps = $data['timestamps'] ?? true;
        $softDeletes = $data['softDeletes'] ?? false;
        $rememberToken = $data['remember_token'] ?? false;

        $finalDataToSeed = [];

        if ($seeds['truncate'] ?? false) {
            static::$connection::table($tableName, null, $currentConnection)->truncate();
        }

        if (empty($seedsData) && !$seedsModel) {
            $seedsModel = \Illuminate\Support\Str::studly(
                \Illuminate\Support\Str::singular($tableName)
            );
        }

        if ($seedsModel) {
            if (strpos($seedsModel, 'App\Models') === false) {
                $seedsModel = "App\Models\\$seedsModel";
            }

            if (!class_exists($seedsModel)) {
                throw new \Exception("The model $seedsModel does not exist");
            }

            for ($i = 0; $i < $count; $i++) {
                $finalDataToSeed[] = $seedsModel::__seeder();
            }
        } else if (is_array($seedsData[0] ?? null)) {
            $finalDataToSeed = $seedsData;
        } else {
            for ($i = 0; $i < $count; $i++) {
                $parsedData = [];

                foreach ($seedsData as $key => $value) {
                    $valueArray = explode('.', $value);

                    if ($valueArray[0] === '@faker') {
                        $localFakerInstance = \Faker\Factory::create();

                        foreach ($valueArray as $index => $fakerMethod) {
                            if ($index === 0) {
                                continue;
                            }

                            if (strpos($fakerMethod, ':') !== false) {
                                $fakerMethod = explode(':', $fakerMethod);
                                if (strpos($fakerMethod[1], ',') !== false) {
                                    $array_params = explode(",", $fakerMethod[1]);
                                    $localFakerInstance = $localFakerInstance->{$fakerMethod[0]}($array_params);
                                } else {
                                    $localFakerInstance = $localFakerInstance->{$fakerMethod[0]}($fakerMethod[1]);
                                }
                            } else {
                                $localFakerInstance = $localFakerInstance->{$fakerMethod}();
                            }
                        }

                        $parsedData[$key] = is_array($localFakerInstance) ? implode('-', $localFakerInstance) : $localFakerInstance;

                        continue;
                    }

                    if ($valueArray[0] === '@tick') {
                        $localTickInstance = tick();

                        foreach ($valueArray as $index => $tickMethod) {
                            if ($index === 0) {
                                continue;
                            }

                            if (strpos($tickMethod, ':') !== false) {
                                $tickMethod = explode(':', $tickMethod);
                                $localTickInstance = $localTickInstance->{$tickMethod[0]}($tickMethod[1]);
                            } else {
                                $localTickInstance = $localTickInstance->{$tickMethod}();
                            }
                        }

                        $parsedData[$key] = $localTickInstance;

                        continue;
                    }

                    if (strpos($value, '@randomString') === 0) {
                        $value = explode(':', $value);
                        $parsedData[$key] = \Illuminate\Support\Str::random($value[1] ?? 10);

                        continue;
                    }

                    if (strpos($value, '@hash') === 0) {
                        $value = explode(':', $value);
                        $parsedData[$key] = \Leaf\Helpers\Password::hash($value[1] ?? 'password');

                        continue;
                    }

                    $parsedData[$key] = $value;
                }

                $finalDataToSeed[] = $parsedData;
            }
        }

        foreach ($finalDataToSeed as $itemToSeed) {
            if ($rememberToken) {
                $itemToSeed['remember_token'] = \Illuminate\Support\Str::random(10);
            }

            if ($softDeletes) {
                $itemToSeed['deleted_at'] = null;
            }

            if ($timestamps) {
                $itemToSeed['created_at'] = tick()->format('YYYY-MM-DD HH:mm:ss');
                $itemToSeed['updated_at'] = tick()->format('YYYY-MM-DD HH:mm:ss');
            }

            static::$connection::table($tableName, null, $currentConnection)->insert($itemToSeed);
        }

        return true;
    }

    /**
     * Reset a database table
     * @param string $fileToReset The schema file to reset
     * @return bool
     */
    public static function reset(string $fileToReset): bool
    {
        static::drop($fileToReset);
        return static::migrate($fileToReset);
    }

    /**
     * Drop a database table
     * @param string $fileToDrop The schema file to drop
     * @return bool
     */
    public static function drop(string $fileToDrop): bool
    {
        $data = Yaml::parseFile($fileToDrop);
        $tableName = str_replace('.yml', '', path($fileToDrop)->basename());

        $currentConnection = $data['connection'] ?? null;

        try {
            if (static::$connection::schema($currentConnection)->hasTable($tableName)) {
                static::$connection::schema($currentConnection)->dropIfExists($tableName);

                if (storage()->exists(StoragePath("database/$tableName"))) {
                    storage()->delete(StoragePath("database/$tableName"));
                }
            }

            return true;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Rollback db to a previous state
     * @param string $fileToRollback The schema file to rollback
     * @param int $step The number of steps to rollback
     * @return bool
     */
    public static function rollback(string $fileToRollback, int $step = 1): bool
    {
        $tableName = rtrim(path($fileToRollback)->basename(), '.yml');

        if (!storage()->exists(StoragePath("database/$tableName"))) {
            return false;
        }

        $files = glob(StoragePath("database/$tableName/*.yml"));

        if (count($files) === 0) {
            return false;
        }

        $migrationStep = count($files) - $step;
        $currentFileToRollback = $files[$migrationStep] ?? null;

        if (!$currentFileToRollback) {
            return false;
        }

        $files = array_reverse($files);

        for ($i = 0; $i < ($step - 1); $i++) {
            storage()->delete($files[$i]);
        }

        storage()->rename($fileToRollback, StoragePath('database' . '/' . $tableName . '/' . tick()->format('YYYY_MM_DD_HHmmss[.yml]')));
        storage()->rename($currentFileToRollback, $fileToRollback);

        return static::migrate($fileToRollback);
    }

    /**
     * Get all column attributes
     * @param mixed $value
     * @return array
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
            'values' => null,
            'onDelete' => null,
            'onUpdate' => null,
            'comment' => null,
            'autoIncrement' => false,
            'useCurrent' => false,
            'useCurrentOnUpdate' => false,
            'charset' => null,
            'collation' => null,
        ];

        if (is_string($value)) {
            $attributes['type'] = $value;
        } else if (is_array($value)) {
            $attributes = array_merge($attributes, $value);
        }

        return $attributes;
    }

    protected static function createColumn($table, $columnName, $columnValue, $createOnly = true)
    {
        if (is_string($columnValue)) {
            return $table->{$columnValue}($columnName);
        }

        if (is_array($columnValue)) {
            if ($columnValue['type'] === 'string' || $columnValue['type'] === 'char' || $columnValue['type'] === 'text') {
                $returnedColumn = $table->{$columnValue['type']}(
                    $columnName,
                    $columnValue['length'] ?? null
                );

                unset($columnValue['length']);
            } else if ($columnValue['type'] === 'enum' || $columnValue['type'] === 'set') {
                $returnedColumn = $table->{$columnValue['type']}(
                    $columnName,
                    $columnValue['values'] ?? []
                );

                unset($columnValue['values']);
            } else {
                $returnedColumn = $table->{$columnValue['type']}($columnName);
            }

            unset($columnValue['type']);

            if ($createOnly === true) {
                foreach ($columnValue as $columnOptionName => $columnOptionValue) {
                    if (!is_bool($columnOptionValue) || $columnOptionName === 'default') {
                        $returnedColumn->{$columnOptionName}($columnOptionValue);
                    } else {
                        if ($columnOptionValue) {
                            $returnedColumn->{$columnOptionName}();
                        }
                    }
                }
            }

            return $returnedColumn;
        }
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
