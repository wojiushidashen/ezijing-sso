<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * 错误码.
 *
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("请求接口报错")
     */
    public const REQUEST_ERROR = 40001;

    /**
     * @Message("参数错误")
     */
    public const PARAMS_ERROR = 40002;

    /**
     * @Message("请重新登录")
     */
    public const LOGIN_AGAIN = 40003;

    /**
     * @Message("缓存用户信息失败")
     */
    public const CACHED_SSO_INFO_ERROR = 40004;

    /**
     * @Message("版本设置错误，当前只支持：%s版本")
     */
    public const SSO_VERSION_ERROR = 40005;
}
