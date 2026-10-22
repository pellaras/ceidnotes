<?php

function prepair_path(string $path, bool $is_file = false): string
{
    if ($is_file) {
        return $path;
    }

    $replace = [
        ' ' => '+',
    ];

    return strtr($path, $replace);
}
