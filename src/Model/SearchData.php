<?php

namespace App\Model;

use App\Form\SearchType;

class SearchData
{
    public array $champions = [];

    /** @var int  */
    public int $page = 1;
}