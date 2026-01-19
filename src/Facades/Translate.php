<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\Facades;

use Illuminate\Support\Facades\Facade;

class Translate extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'gtx-translate';
    }
}
