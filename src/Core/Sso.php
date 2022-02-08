<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Core;

use Ezijing\EzijingSso\Constants\ErrorCode;
use Ezijing\EzijingSso\Exceptions\PluginException;
use Ezijing\EzijingSso\Interfaces\SsoInterface;
use Hyperf\Config\Annotation\Value;

/**
 * 单点登录API.
 */
class Sso implements SsoInterface
{
    //账密登录
    protected const LOGIN_TYPE_PASSWORD = 1;

    // 请求方法
    protected const POST = 'post';

    protected const GET = 'get';

    // 请求数据格式
    protected const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';

    protected const CONTENT_TYPE_TEXT = 'text/html';

    /**
     * 新版sso的host.
     *
     * @Value("sso_plugins.newsso_host")
     */
    protected $ssoHost;

    /**
     * 用户中心的host.
     *
     * @Value("sso_plugins.usercenter_host")
     */
    protected $userCenterHost;

    /**
     * 通过用户名密码登录.
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @return mixed
     */
    public function login(string $username, string $password)
    {
        return requestClient(
            self::POST,
            $this->ssoHost . config('sso_plugins.newsso_api.LOGIN'),
            [
                'service' => '0.0.0.0',
                'account' => $username,
                'password' => $password,
                'type' => self::LOGIN_TYPE_PASSWORD,
            ],
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );
    }

    /**
     * 退出登录.
     *
     * @param string $tgc TGC
     * @return mixed
     */
    public function logout(string $tgc)
    {
        return requestClient(
            self::GET,
            $this->ssoHost . config('sso_plugins.newsso_api.LOGOUT'),
            [],
            [
                'Content-Type' => self::CONTENT_TYPE_TEXT,
                'Cookie' => "TGC={$tgc}",
            ]
        );
    }

    /**
     * 通过TGC获取用户信息.
     *
     * @param string $tgc TGC
     * @return mixed
     */
    public function getUserInfoByTgc(string $tgc)
    {
        $data = requestClient(
            self::GET,
            $this->ssoHost . config('sso_plugins.newsso_api.USERINFO'),
            [],
            [
                'Content-Type' => self::CONTENT_TYPE_TEXT,
                'Cookie' => "TGC={$tgc}",
            ]
        );

        if (isset($data['code']) && $data['code'] == 0) {
            return $data['code'];
        }

        throw new PluginException(ErrorCode::LOGIN_AGAIN, $data['msg'] ?? '');
    }

    /**
     * 创建单个用户.
     *
     * @return mixed
     */
    public function createUser(array $userParams)
    {
        $sendData = [];
        $allowUserParams = ['username', 'nickname', 'email', 'mobile', 'id_number', 'wechat_unionid', 'status', 'password', 'country_code'];
        setDataAndCheck($userParams, $allowUserParams, [], $sendData);

        return requestClient(
            self::POST,
            $this->userCenterHost . config('sso_plugins.usercenter_api.CREATE_USER_SINGLE'),
            $sendData,
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );
    }

    /**
     * 精确检索用户.
     *
     * @param array $userParams 请求参数
     * @return array|mixed
     */
    public function exactSearchUser(array $userParams = [])
    {
        $sendData = [];
        $allowUserParams = ['username', 'email', 'mobile'];
        setDataAndCheck($userParams, $allowUserParams, [], $sendData);
        $data = requestClient(
            self::POST,
            $this->userCenterHost . config('sso_plugins.usercenter_api.EXACT_SEARCH_USER'),
            $sendData,
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );

        if (! $data) {
            return [];
        }

        return array_values($data)[0] ?? [];
    }

    /**
     * 根据id(精确)，用户名(模糊)，邮箱(精确)，手机号(精确)，昵称(模糊)检索用户.
     *
     * @param array $userParams 请求参数
     * @return array|mixed
     */
    public function search(array $userParams)
    {
        $sendData = [];
        $allowUserParams = ['id', 'username', 'nickname', 'email', 'mobile', 'page', 'size'];
        setDataAndCheck($userParams, $allowUserParams, [], $sendData);

        if (empty($sendData)) {
            return [];
        }

        return requestClient(
            self::GET,
            $this->userCenterHost . config('sso_plugins.usercenter_api.SEARCH_SERVER_USER'),
            $sendData,
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );
    }

    /**
     * 通过用户的ssoId精确检索用户信息.
     *
     * @param string $id 用户的ssoId
     * @return array|mixed
     */
    public function exactSearchOneUserById($id)
    {
        $users = requestClient(
            self::GET,
            $this->userCenterHost . config('sso_plugins.usercenter_api.SEARCH_SERVER_USER_MULTI'),
            ['id' => $id],
            ['Content-Type' => self::CONTENT_TYPE_TEXT]
        );

        return $users[$id] ?? [];
    }
}
