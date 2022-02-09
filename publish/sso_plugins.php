<?php

declare(strict_types=1);

return [
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
