<?php
spl_autoload_register(function ($class) {
    $file = str_replace('\\', '/', $class);

    if (!file_exists(dirname(__DIR__, 4) . "/$file.php")) return;

    require "$file.php";
});
