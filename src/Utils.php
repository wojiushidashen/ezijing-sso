<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso;

use Ezijing\EzijingSso\Constants\ErrorCode;
use Ezijing\EzijingSso\Exceptions\PluginException;

class Utils
{
    /**
     * 验证版本.
     */
    public static function checkVersion(string $version)
    {
        $versionArr = ['V1', 'V2'];
        if (! in_array(strtoupper($version), $versionArr)) {
            throw new PluginException(ErrorCode::SSO_VERSION_ERROR, [implode(',', $versionArr)]);
        }
    }
}
