<?php

function getMySqlDate(string $date)
{
    return date("Y-m-d H:i:s", strtotime($date));
}
