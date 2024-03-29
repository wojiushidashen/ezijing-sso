<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Core;

use Ezijing\EzijingSso\Constants\ErrorCode;
use Ezijing\EzijingSso\Exceptions\PluginException;
use Ezijing\EzijingSso\Interfaces\SsoInterface;
use Ezijing\EzijingSso\Utils;
use Hyperf\Config\Annotation\Value;

/**
 * 单点登录API.
 */
class Sso implements SsoInterface
{
    /**
     * @var int 账密登录
     */
    protected const LOGIN_TYPE_PASSWORD = 1;

    /**
     * @var string POST 请求方式
     */
    protected const POST = 'post';

    /**
     * @var string GET请求方式
     */
    protected const GET = 'get';

    /**
     * @var string 请求数据格式 application/x-www-form-urlencoded
     */
    protected const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';

    /**
     * @var string 请求数据格式 text/html
     */
    protected const CONTENT_TYPE_TEXT = 'text/html';

    /**
     * 版本.
     *
     * @Value("sso_plugins.default_version")
     */
    protected $version;

    /**
     * 新版sso的host.
     */
    protected $ssoHost;

    /**
     * 用户中心的host.
     */
    protected $userCenterHost;

    /**
     * 盐值.
     * @Value("sso_plugins.salt")
     */
    protected $salt;

    public function __construct()
    {
        $this->init($this->version);
    }

    /**
     * 设置版本.
     *
     * @param string $version 版本
     * @return $this
     */
    public function withVersion(string $version)
    {
        $this->init($version);

        return $this;
    }

    /**
     * 通过用户名密码登录.
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @return mixed
     */
    public function login(string $username, string $password)
    {
        $data = requestClient(
            self::POST,
            $this->ssoHost . getConfigByFormatName('sso_plugins.%s.newsso_api.LOGIN', $this->version),
            $this->getRequestData([
                'service' => $this->ssoHost,
                'account' => $username,
                'password' => $password,
                'type' => self::LOGIN_TYPE_PASSWORD,
            ]),
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );

        return $this->formatResponse($data);
    }

    /**
     * 退出登录.
     *
     * @param string $tgc TGC
     * @return mixed
     */
    public function logout(string $tgc)
    {
        $data = requestClient(
            self::GET,
            $this->ssoHost . getConfigByFormatName('sso_plugins.%s.newsso_api.LOGOUT', $this->version),
            $this->getRequestData([]),
            [
                'Content-Type' => self::CONTENT_TYPE_TEXT,
                'Cookie' => "TGC={$tgc}",
            ]
        );

        return $this->formatResponse($data);
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
            $this->ssoHost . getConfigByFormatName('sso_plugins.%s.newsso_api.USERINFO', $this->version),
            $this->getRequestData([]),
            [
                'Content-Type' => self::CONTENT_TYPE_TEXT,
                'Cookie' => "TGC={$tgc}",
            ]
        );

        if (isset($data['code']) && $data['code'] == 0) {
            return $data['data'];
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
        if (formatVersion($this->version) > 1) {
            array_push($allowUserParams, 'project_id', 'register_type');
        }
        setDataAndCheck($userParams, $allowUserParams, [], $sendData);

        $res = requestClient(
            self::POST,
            $this->userCenterHost . getConfigByFormatName('sso_plugins.%s.usercenter_api.CREATE_USER_SINGLE', $this->version),
            $this->getRequestData($sendData),
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );

        return $this->formatResponse($res);
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
        $allowUserParams = ['id', 'username', 'nickname', 'email', 'mobile', 'page', 'size', 'need_encrypt'];
        setDataAndCheck($userParams, $allowUserParams, [], $sendData);
        $data = requestClient(
            self::POST,
            $this->userCenterHost . getConfigByFormatName('sso_plugins.%s.usercenter_api.EXACT_SEARCH_USER', $this->version),
            $this->getRequestData($sendData),
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );

        if (formatVersion($this->version) > 1) {
            $data = $this->formatResponse($data);
            return $data['items'] ?? [];
        }

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
        $allowUserParams = ['id', 'username', 'nickname', 'email', 'mobile', 'page', 'size', 'need_encrypt'];
        setDataAndCheck($userParams, $allowUserParams, [], $sendData);

        if (empty($sendData)) {
            return [];
        }

        $data = requestClient(
            self::GET,
            $this->userCenterHost . getConfigByFormatName('sso_plugins.%s.usercenter_api.SEARCH_SERVER_USER', $this->version),
            $this->getRequestData($sendData),
            ['Content-Type' => self::CONTENT_TYPE_FORM]
        );

        return $this->formatResponse($data);
    }

    /**
     * 通过用户的ssoId精确检索用户信息.
     *
     * @param string $id 用户的ssoId
     * @param mixed $needEncrypt
     * @return array|mixed
     */
    public function exactSearchOneUserById($id, $needEncrypt = 1)
    {
        $users = requestClient(
            self::GET,
            $this->userCenterHost . getConfigByFormatName('sso_plugins.%s.usercenter_api.SEARCH_SERVER_USER_MULTI', $this->version),
            $this->getRequestData([
                'id' => $id,
                'need_encrypt' => $needEncrypt,
            ]),
            ['Content-Type' => self::CONTENT_TYPE_TEXT]
        );

        if (formatVersion($this->version) > 1) {
            $data = $this->formatResponse($users);
            $users = $data['data'];
        }

        return $users[$id] ?? [];
    }

    /**
     * 批量获取用户信息.
     *
     * @param $params
     * @param int $needEncrypt
     * @return mixed|void
     */
    public function multiGetUsers($params, $needEncrypt = 1)
    {
        $sendData = [];
        $allowUserParams = ['id', 'username', 'need_cache', 'need_encrypt'];
        setDataAndCheck($params, $allowUserParams, [], $sendData);

        if (empty($sendData)) {
            return [];
        }

        $users = requestClient(
            self::GET,
            $this->userCenterHost . getConfigByFormatName('sso_plugins.%s.usercenter_api.SEARCH_USER', $this->version),
            $this->getRequestData(array_merge([
                'need_encrypt' => $needEncrypt,
            ], $sendData)),
            ['Content-Type' => self::CONTENT_TYPE_TEXT]
        );

        if (formatVersion($this->version) > 1) {
            $data = $this->formatResponse($users);
            $users = $data['data'];
        }


        return $users;
    }

    /**
     * 格式化输出结果.
     *
     * @param $res
     * @return mixed
     */
    private function formatResponse($res)
    {
        if (isset($res['code']) && $res['code'] != 0) {
            throw new PluginException(ErrorCode::REQUEST_ERROR, $res['msg'] ?? '');
        }

        return $res;
    }

    private function init($version)
    {
        // 验证版本
        Utils::checkVersion($version);
        $this->version = $version;

        // 设置host
        $this->setHost();
    }

    /**
     * 设置host.
     */
    private function setHost()
    {
        switch (strtoupper($this->version)) {
            case 'V1':
                $this->ssoHost = config('sso_plugins.newsso_host');
                $this->userCenterHost = config('sso_plugins.usercenter_api_host');
                break;
            case 'V2':
                $this->ssoHost = config('sso_plugins.usercenter_host');
                $this->userCenterHost = config('sso_plugins.usercenter_api_host');
                break;
            default:
                throw new PluginException(ErrorCode::SSO_VERSION_ERROR, 'V1,V2');
        }
    }

    /**
     * 签名.
     *
     * @param $data
     * @return mixed
     */
    private function sign($data)
    {
        $data['timestamp'] = time();
        $data['nonce'] = uniqid('', true);  // 保证唯一
        $data['salt'] = $this->salt;
        ksort($data);

        $query = '';
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $query .= $k . '=' . json_encode($v) . '&';
            } else {
                $query .= $k . '=' . $v . '&';
            }
        }
        $query = substr($query, 0, strlen($query) - 1);
        $signature = strtoupper(md5($query));
        $data['signature'] = $signature;
        unset($data['salt']);

        return $data;
    }

    /**
     * 获取请求参数.
     *
     * @return array|mixed
     */
    private function getRequestData(array $data)
    {
        if (formatVersion(strtoupper($this->version)) > 1) {
            return $this->sign($data);
        }

        return $data;
    }
}
