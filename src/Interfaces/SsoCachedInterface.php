<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Interfaces;

interface SsoCachedInterface
{
    /**
     * 设置用户信息.
     *
     * @param array $user 用户信息
     * @return mixed
     */
    public function setUser(array $user);

    /**
     * 获取用户id.
     *
     * @return mixed
     */
    public function getUser();

    /**
     * 清除用户缓存.
     * @return bool
     */
    public function clearUserCached();
}
