<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * RedisKey.
 *
 * @Constants
 */
class RedisKeys extends AbstractConstants
{
    /**
     * 用户登录缓存key.
     *
     * @Message("CACHE_USER_INFO_TGC:%s:%s")
     */
    public const CACHE_USER_INFO_TGC = 0;

    /**
     * 用户缓存key.
     *
     * @Message("CACHE_USER_INFO_SSOID:%s:%s")
     */
    public const CACHE_USER_INFO_SSOID = 1;
}
