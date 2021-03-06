<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
**Table of Contents**  *generated with [DocToc](https://github.com/thlorenz/doctoc)*

- [清控紫荆sso api插件](#%E6%B8%85%E6%8E%A7%E7%B4%AB%E8%8D%86sso-api%E6%8F%92%E4%BB%B6)
  - [安装](#%E5%AE%89%E8%A3%85)
    - [1、下载插件包](#1%E4%B8%8B%E8%BD%BD%E6%8F%92%E4%BB%B6%E5%8C%85)
    - [2、发布配置](#2%E5%8F%91%E5%B8%83%E9%85%8D%E7%BD%AE)
  - [使用ssoApi](#%E4%BD%BF%E7%94%A8ssoapi)
    - [1、用户登录](#1%E7%94%A8%E6%88%B7%E7%99%BB%E5%BD%95)
    - [2、登出](#2%E7%99%BB%E5%87%BA)
    - [3、通过TGC获取用户信息](#3%E9%80%9A%E8%BF%87tgc%E8%8E%B7%E5%8F%96%E7%94%A8%E6%88%B7%E4%BF%A1%E6%81%AF)
    - [4、创建单个用户信息](#4%E5%88%9B%E5%BB%BA%E5%8D%95%E4%B8%AA%E7%94%A8%E6%88%B7%E4%BF%A1%E6%81%AF)
    - [5、精确检索用户](#5%E7%B2%BE%E7%A1%AE%E6%A3%80%E7%B4%A2%E7%94%A8%E6%88%B7)
    - [6、根据id(精确)，用户名(模糊)，邮箱(精确)，手机号(精确)，昵称(模糊)检索用户](#6%E6%A0%B9%E6%8D%AEid%E7%B2%BE%E7%A1%AE%E7%94%A8%E6%88%B7%E5%90%8D%E6%A8%A1%E7%B3%8A%E9%82%AE%E7%AE%B1%E7%B2%BE%E7%A1%AE%E6%89%8B%E6%9C%BA%E5%8F%B7%E7%B2%BE%E7%A1%AE%E6%98%B5%E7%A7%B0%E6%A8%A1%E7%B3%8A%E6%A3%80%E7%B4%A2%E7%94%A8%E6%88%B7)
    - [7、通过用户的ssoId精确检索用户信息](#7%E9%80%9A%E8%BF%87%E7%94%A8%E6%88%B7%E7%9A%84ssoid%E7%B2%BE%E7%A1%AE%E6%A3%80%E7%B4%A2%E7%94%A8%E6%88%B7%E4%BF%A1%E6%81%AF)
  - [缓存](#%E7%BC%93%E5%AD%98)
    - [1、通过TGC缓存用户信息](#1%E9%80%9A%E8%BF%87tgc%E7%BC%93%E5%AD%98%E7%94%A8%E6%88%B7%E4%BF%A1%E6%81%AF)
    - [2、通过ssoId缓存用户信息](#2%E9%80%9A%E8%BF%87ssoid%E7%BC%93%E5%AD%98%E7%94%A8%E6%88%B7%E4%BF%A1%E6%81%AF)
    - [3、通过TGC获取缓存的用户信息](#3%E9%80%9A%E8%BF%87tgc%E8%8E%B7%E5%8F%96%E7%BC%93%E5%AD%98%E7%9A%84%E7%94%A8%E6%88%B7%E4%BF%A1%E6%81%AF)
    - [4、 通过ssoId获取缓存的用户信息](#4-%E9%80%9A%E8%BF%87ssoid%E8%8E%B7%E5%8F%96%E7%BC%93%E5%AD%98%E7%9A%84%E7%94%A8%E6%88%B7%E4%BF%A1%E6%81%AF)
    - [5、通过TGC删除用户缓存](#5%E9%80%9A%E8%BF%87tgc%E5%88%A0%E9%99%A4%E7%94%A8%E6%88%B7%E7%BC%93%E5%AD%98)
    - [6、通过ssoId删除用户缓存](#6%E9%80%9A%E8%BF%87ssoid%E5%88%A0%E9%99%A4%E7%94%A8%E6%88%B7%E7%BC%93%E5%AD%98)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

清控紫荆sso api插件
===============================

安装
-------------------------------

### 1、下载插件包
```shell
> composer require ezijing/ezijing-sso -vvvv
```
### 2、发布配置
```shell
>  php bin/hyperf.php vendor:publish ezijing/ezijing-sso -vvv
```
```php
<?php

declare(strict_types=1);

return [
    // 版本 V1, V2
    'default_version' => env('SSO_VERSION', 'V1'),
    // 新版sso地址
    'newsso_host' => env('SSO_NEWSSO_API_HOST', ''),
    // 新版sso V2地址
    'usercenter_host' => env('SSO_USERCENTER_HOST', ''),
    // 用户中心地址
    'usercenter_api_host' => env('SSO_USERCENTER_API_HOST', ''),
    // 接口签名使用的盐值
    'salt' => env('SSO_SALT', ''),
    
    'V1' => [
        // 新版sso api
        'newsso_api' => [
            'LOGIN' => '/rest/login',
            'LOGOUT' => '/rest/logout',
            'USERINFO' => '/account/get-user-info',
        ],
        // 用户中心api
        'usercenter_api' => [
            'SEARCH_USER' => '/user/multi-get-user-info',
            'CREATE_USER' => '/user/multi-create-user',
            'CREATE_USER_SINGLE' => '/user/create-user',
            'UPDATE_USER' => '/user/change-info',
            'CHANGE_PWD_BY_COOKIE' => '/user/change-pwd-by-cookie',
            'EXACT_SEARCH_USER' => '/user/exact-search-user',
            'SEARCH_SERVER_USER' => '/user/search-user',
            'SEARCH_SERVER_USER_MULTI' => '/user/multi-get-user-info',
        ],
    ],
    'V2' => [
        // 新版sso api
        'newsso_api' => [
            'LOGIN' => '/v2/frontend/user/login',
            'LOGOUT' => '/v2/frontend/user/logout',
            'USERINFO' => '/v2/frontend/user/get-user-info',
        ],
        // 用户中心api
        'usercenter_api' => [
            'SEARCH_USER' => '/v2/server/user/multi-get-user-info',
            'CREATE_USER' => '/v2/server/user/multi-create-user',
            'CREATE_USER_SINGLE' => '/v2/server/user/create-user',
            'UPDATE_USER' => '/v2/server/user/change-info',
            'CHANGE_PWD_BY_COOKIE' => '/v2/frontend/user/change-pwd-by-cookie',
            'EXACT_SEARCH_USER' => '/v2/server/user/search-user',
            'SEARCH_SERVER_USER' => '/v2/server/user/search-user',
            'SEARCH_SERVER_USER_MULTI' => '/v2/server/user/multi-get-user-info',
        ],
    ],
];
?>
```

使用ssoApi 
-------------------------------

### 1、用户登录
```php
<?php
$ssoApi = make(\Ezijing\EzijingSso\Core\Sso::class);
$ssoApi->withVersion('V2')->login('username', 'password');
?>
```

### 2、登出
```php
<?php
$ssoApi = make(\Ezijing\EzijingSso\Core\Sso::class);
$ssoApi->withVersion('V2')->logout('TGC')
?>
```

### 3、通过TGC获取用户信息
```php
<?php
$ssoApi = make(\Ezijing\EzijingSso\Core\Sso::class);
$user = $ssoApi->withVersion('V2')->getUserInfoByTgc('TGC');
?>
```

### 4、创建单个用户信息
```php
<?php
$ssoApi = make(\Ezijing\EzijingSso\Core\Sso::class);
$data = [
    'username' => '小明',
    'nickname' => '小明',
    'email' => 'xiaoming@ezijing.com',
    'mobile' => '323232323', 
    'password' => '123456', 
    'country_code' => '86'
];
$user = $ssoApi->withVersion('V2')->createUser($data);
?>
```

### 5、精确检索用户
```php
<?php
$ssoApi = make(\Ezijing\EzijingSso\Core\Sso::class);
$data = [
    'username' => '小明',
];
$ssoApi->withVersion('V2')->exactSearchUser($data);
?>
```

### 6、根据id(精确)，用户名(模糊)，邮箱(精确)，手机号(精确)，昵称(模糊)检索用户
```php
<?php
$ssoApi = make(\Ezijing\EzijingSso\Core\Sso::class);
$data = [
    'username' => '小明',
];
$ssoApi->withVersion('V2')->search($data);
?>
```

### 7、通过用户的ssoId精确检索用户信息
```php
<?php
$ssoApi = make(\Ezijing\EzijingSso\Core\Sso::class);
$ssoApi->withVersion('V2')->exactSearchOneUserById('1');
?>
```

缓存
-------------------------------------

### 1、通过TGC缓存用户信息
```php
<?php
$ssoCached = make(\Ezijing\EzijingSso\Core\SsoCached::class);
$ssoCached
    ->withVersion('V2')
    ->withTgc('TGC')
    ->setUser([
        'id' => 1,
        'username' => '小明'
    ]);
?>
```

### 2、通过ssoId缓存用户信息
```php
<?php
$ssoCached = make(\Ezijing\EzijingSso\Core\SsoCached::class);
$ssoCached
    ->withVersion('V2')
    ->withSsoId('1')
    ->setUser([
        'id' => 1,
        'username' => '小明'
    ]);
?>
?>
```

### 3、通过TGC获取缓存的用户信息
```php
<?php
$ssoCached = make(\Ezijing\EzijingSso\Core\SsoCached::class);
$user = $ssoCached
    ->withVersion('V2')
    ->withTgc('TGC')
    ->getUser();
?>
```

### 4、 通过ssoId获取缓存的用户信息
```php
<?php
$ssoCached = make(\Ezijing\EzijingSso\Core\SsoCached::class);
$user = $ssoCached
    ->withVersion('V2')
    ->withSsoId('1')
    ->getUser();
?>
```

### 5、通过TGC删除用户缓存
```php
<?php
$ssoCached = make(\Ezijing\EzijingSso\Core\SsoCached::class);
$ssoCached
    ->withVersion('V2')
    ->withTgc('TGC')
    ->clearUserCached();
?>
```


### 6、通过ssoId删除用户缓存
```php
$ssoCached = make(\Ezijing\EzijingSso\Core\SsoCached::class);
$ssoCached
    ->withVersion('V2')
    ->withSsoId('1')
    ->clearUserCached();
```
