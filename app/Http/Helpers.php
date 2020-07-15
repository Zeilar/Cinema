<?php

function abbreviateName(string $name) {
    $abbreviated = '';
    preg_match_all('([A-Z]+)', $name, $matches);
    foreach ($matches[0] as $letter) {
        $abbreviated .= $letter;
    }
    return $abbreviated;
}