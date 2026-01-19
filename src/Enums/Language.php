<?php

declare(strict_types=1);

namespace W33bvgl\GtxTranslate\Enums;

enum Language: string
{
    case Auto               = 'auto';
    case English            = 'en';
    case Russian            = 'ru';
    case Armenian           = 'hy';
    case Japanese           = 'ja';
    case ChineseSimplified  = 'zh-CN';
    case ChineseTraditional = 'zh-TW';
    case French             = 'fr';
    case German             = 'de';
    case Spanish            = 'es';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
