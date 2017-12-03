<?php

function prepair_path($path, $is_file = false) {
    if ($is_file) {
        return $path;
    }

    $replace = [
        " " => "+",
    ];

    return strtr($path, $replace);
}
