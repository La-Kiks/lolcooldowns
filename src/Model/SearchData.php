<?php

namespace App\Model;

class SearchData
{
    /** @var string  */
    public string $championName = '';

    /** @var int  */
    public int $haste = 0;

    /** @var float|int */
    public float|int $multiplier = 1;

    /** @var int  */
    public int $page = 1;
}