<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Interfaces;

interface SsoInterface
{
    /**
     * 用户登录.
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @return mixed
     */
    public function login(string $username, string $password);

    /**
     * 用户登出.
     *
     * @param string $tgc TGC
     * @return mixed
     */
    public function logout(string $tgc);

    /**
     * 通过TGC获取用户信息.
     *
     * @param string $tgc TGC
     * @return mixed
     */
    public function getUserInfoByTgc(string $tgc);

    /**
     * 创建单个用户.
     *
     * @param array $userParams ['username', 'nickname', 'email', 'mobile', 'id_number', 'wechat_unionid', 'status', 'password', 'country_code']
     * @return mixed
     */
    public function createUser(array $userParams);

    /**
     * 精确搜索用户信息.
     *
     * @param array $userParams ['username', 'email', 'mobile']
     * @return mixed
     */
    public function exactSearchUser(array $userParams = []);

    /**
     * 根据id(精确)，用户名(模糊)，邮箱(精确)，手机号(精确)，昵称(模糊)检索用户.
     *
     * @param array $userParams ['id', 'username', 'nickname', 'email', 'mobile', 'page', 'size']
     * @return mixed
     */
    public function search(array $userParams);

    /**
     * 通过用户的ssoId精确搜索单个用户信息.
     *
     * @param string $id 用户的ssoId
     * @param mixed $needEncrypt
     * @return mixed
     */
    public function exactSearchOneUserById($id, $needEncrypt = 1);

    /**
     * 批量查询用户信息.
     * @param $params
     * @param int $needEncrypt
     * @return mixed
     */
    public function multiGetUsers($params, $needEncrypt = 1);
}
