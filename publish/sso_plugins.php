<?php

declare(strict_types=1);

return [
    // 新版sso地址
    'newsso_host' => env('SSO_NEWSSO_API_HOST', ''),
    // 新版sso api
    'newsso_api' => [
        'LOGIN' => '/rest/login',
        'LOGOUT' => '/rest/logout',
        'USERINFO' => '/account/get-user-info',
    ],

    // 用户中心地址
    'usercenter_host' => env('SSO_USERCENTER_API_HOST', ''),
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
];
