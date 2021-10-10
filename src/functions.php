<?php

if (!function_exists("render")) {
    function render(string $view, array $data = [])
    {
        return ViewConfig("render")($view, $data);
    }
}
