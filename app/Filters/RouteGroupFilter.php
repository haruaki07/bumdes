<?php

namespace App\Filters;

use Illuminate\Support\Facades\Route;
use TakiElias\Tablar\Menu\Filters\FilterInterface;

class RouteGroupFilter implements FilterInterface
{
    public function transform($item)
    {
        if (Route::is('e-billing.*')) {
            if (! isset($item['group'])) {
                return false;
            }

            if ($item['group'] === 'e-billing') {
                return $item;
            }

            return false;
        }

        if (isset($item['group'])) {
            return false;
        }

        return $item;
    }
}
