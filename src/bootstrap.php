<?php
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class);

    if (!file_exists(__DIR__ . "/../../$file.php")) return;

    require "$file.php";
});
