<?php

function getBool(int $tinyint): bool
{
    if ($tinyint == 1) {
        return true;
    }

    else if ($tinyint == 0) {
        return false;
    }
}

function getTinyInt(bool $bool)
{
    if ($bool == true) {
        return 1;
    } else {
        return 0;
    }
}

function getTinyIntFromCheck(string|null $check)
{
    if ($check == 'on') {
        return 1;
    } else if ($check == null) {
        return null;
    } else {
        return 0;
    }
}