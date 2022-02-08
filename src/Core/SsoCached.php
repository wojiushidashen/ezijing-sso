<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Core;

use Ezijing\EzijingSso\Constants\ErrorCode;
use Ezijing\EzijingSso\Constants\RedisKeys;
use Ezijing\EzijingSso\Exceptions\PluginException;
use Ezijing\EzijingSso\Interfaces\SsoCachedInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Codec\Json;

/**
 * 单点登录用户信息缓存.
 */
class SsoCached implements SsoCachedInterface
{
    /**
     * TGC.
     */
    protected const CACHED_TAG_TGC = 0;

    /**
     * SSOID.
     */
    protected const CACHED_TAG_SSOID = 1;

    /**
     * @var string 缓存key
     */
    protected $cacheKey;

    /**
     * @var int 缓存类型
     */
    protected $cachedTag;

    /**
     * @var string TGC 或 ssoId
     */
    protected $cachedIndex;

    /**
     * @var Sso
     * @Inject
     */
    protected $ssoApi;

    /**
     * 以TGC为KEY的缓存.
     *
     * @param string $tac TGC
     * @return $this
     */
    public function withTgc($tac)
    {
        $this->setCachedKey(self::CACHED_TAG_TGC, $tac);

        return $this;
    }

    /**
     * 以ssoId为Key的缓存.
     *
     * @param string $ssoId
     * @return $this
     */
    public function withSsoId($ssoId)
    {
        $this->setCachedKey(self::CACHED_TAG_SSOID, $ssoId);

        return $this;
    }

    /**
     * 设置用户缓存.
     *
     * @param array $user 要缓存的用户信息
     * @return mixed|void
     */
    public function setUser(array $user)
    {
        $user = Json::encode($user);
        $key = $this->getCacheKey();
        $redis = redis();
        if ($redis->set($key, $user)) {
            if (empty($user)) {
                // 防止缓存击穿
                $redis->expire($key, 60 * 5 + mt_rand(1, 60));
            } else {
                $redis->expire($key, 86400 * 7 + mt_rand(1, 600));
            }
        }
    }

    /**
     * 获取缓存的用户信息.
     *
     * @return array|false|mixed
     */
    public function getUser()
    {
        $user = $this->getUserByCached();
        if ($user === false) {
            switch ($this->cachedTag) {
                case self::CACHED_TAG_SSOID:
                    if (empty($user = $this->ssoApi->exactSearchOneUserById($this->cachedIndex))) {
                        return [];
                    }
                    break;
                case self::CACHED_TAG_TGC:
                    if (empty($this->cachedIndex)) {
                        return [];
                    }
                    if (empty($user = $this->ssoApi->getUserInfoByTgc($this->cachedIndex))) {
                        return [];
                    }
                    break;
                default:
                    return [];
            }

            $this->setUser($user);
        }

        return $user;
    }

    /**
     * 删除用户缓存信息.
     *
     * @return bool
     */
    public function clearUserCached()
    {
        $key = $this->getCacheKey();
        if (! redis()->exists($key)) {
            return true;
        }
        redis()->del($key);

        return true;
    }

    /**
     * 缓存用户信息hash key.
     *
     * @param string $cachedTag 缓存类型
     * @param string $cachedIndex 缓存标识
     */
    protected function setCachedKey($cachedTag, $cachedIndex)
    {
        if (empty($cachedIndex)) {
            throw new PluginException(ErrorCode::CACHED_SSO_INFO_ERROR);
        }

        $this->cachedIndex = (string) $cachedIndex;
        switch ($cachedTag) {
            case self::CACHED_TAG_SSOID:
                $this->cacheKey = RedisKeys::getMessage(RedisKeys::CACHE_USER_INFO_SSOID, [$this->cachedIndex]);
                break;
            case self::CACHED_TAG_TGC:
                $this->cacheKey = RedisKeys::getMessage(RedisKeys::CACHE_USER_INFO_TGC, [md5($this->cachedIndex)]);
                break;
            default:
                throw new PluginException(ErrorCode::CACHED_SSO_INFO_ERROR, 'cached tag error');
        }
    }

    /**
     * 获取缓存的key.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        if (empty($this->cacheKey)) {
            throw new PluginException(ErrorCode::CACHED_SSO_INFO_ERROR, '获取缓存key失败，请通过withTgc或withSsoId函数设置');
        }

        return $this->cacheKey;
    }

    /**
     * 直接通过缓存获取用户信息.
     *
     * @return false|mixed
     */
    private function getUserByCached()
    {
        $key = $this->getCacheKey();
        if (! redis()->exists($key)) {
            return false;
        }
        $user = redis()->get($key);

        return Json::decode($user, true);
    }
}
