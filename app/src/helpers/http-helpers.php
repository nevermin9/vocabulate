<?php

if (! function_exists('redirect')) {
    function redirect(string $url, int $statusCode = 303)
    {
        header("Location: " . $url, true, $statusCode);
        die();
    }
}
