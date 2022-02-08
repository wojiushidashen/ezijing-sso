<?php

declare(strict_types=1);

if (! function_exists('container')) {
    /**
     * 获取容器实例.
     * @return \Psr\Container\ContainerInterface
     */
    function container(): Psr\Container\ContainerInterface
    {
        return \Hyperf\Utils\ApplicationContext::getContainer();
    }
}

if (! function_exists('redis')) {
    /**
     * 获取Redis实例.
     * @param mixed $redisPool
     * @return \Hyperf\Redis\Redis
     */
    function redis($redisPool = 'default')
    {
        return container()->get(\Hyperf\Redis\RedisFactory::class)->get($redisPool);
    }
}

if (! function_exists('requestClient')) {
    // 发送http请求
    function requestClient($method, $url, array $data = [], array $header = [], array $creatOptions = [])
    {
        $clientFactory = container()->get(\Hyperf\Guzzle\ClientFactory::class);
        $client = $clientFactory->create(array_merge($creatOptions, ['timeout' => 20]));
        $options = [];
        if ($tgc = \Hyperf\Utils\Context::get('tgc')) {
            $header['Cookie'] = "TGC={$tgc}";
        }
        if (! empty($header)) {
            $options['headers'] = $header;
        }
        switch (strtolower($method)) {
            case 'get':
                if (! empty($data)) {
                    $options['query'] = $data;
                }
                $result = $client->get($url, $options)->getBody()->getContents();
                break;
            case 'post':
                if (! empty($data)) {
                    if (isset($header['Content-Type']) && strpos($header['Content-Type'], 'application/x-www-form-urlencoded') !== false) {
                        $options['form_params'] = $data;
                    } else {
                        $options['json'] = $data;
                    }
                }
                $result = $client->post($url, $options)->getBody()->getContents();
                break;
            case 'delete':
                if (! empty($data)) {
                    $options['query'] = $data;
                }
                $result = $client->delete($url, $options)->getBody()->getContents();
                break;
            case 'put':
                if (! empty($data)) {
                    if (isset($header['Content-Type']) && strpos($header['Content-Type'], 'application/x-www-form-urlencoded') !== false) {
                        $options['form_params'] = $data;
                    } else {
                        $options['json'] = $data;
                    }
                }
                $result = $client->put($url, $options)->getBody()->getContents();
                break;
            case 'patch':
                if (! empty($data)) {
                    if (isset($header['Content-Type']) && strpos($header['Content-Type'], 'application/x-www-form-urlencoded') !== false) {
                        $options['form_params'] = $data;
                    } else {
                        $options['json'] = $data;
                    }
                }
                $result = $client->patch($url, $options)->getBody()->getContents();
                break;
            default:
                throw new \Ezijing\EzijingSso\Exceptions\PluginException(
                    \Ezijing\EzijingSso\Constants\ErrorCode::REQUEST_ERROR
                );
        }

        return \Hyperf\Utils\Codec\Json::decode($result ?? '[]', true);
    }
}

if (! function_exists('setDataAndCheck')) {
    /**
     * 设置和校验要保存的参数.
     *
     * @param array $attributes 传入的属性
     * @param array $allAttributeNames 所有想要设置的属性
     * @param array $allowAttributeNames 必传的属性
     * @param array $saveData 要保存的数组
     */
    function setDataAndCheck(array $attributes, array $allAttributeNames, array $allowAttributeNames = [], &$saveData = [])
    {
        foreach ($allAttributeNames as $allAttributeName) {
            if (isset($attributes[$allAttributeName])) {
                if (is_string($attributes[$allAttributeName])) {
                    $attributes[$allAttributeName] = trim($attributes[$allAttributeName]);
                }
                $saveData[$allAttributeName] = $attributes[$allAttributeName];
            }
        }

        foreach ($allowAttributeNames as $allowAttributeName) {
            if (! isset($saveData[$allowAttributeName])) {
                throw new \Ezijing\EzijingSso\Exceptions\PluginException(
                    \Ezijing\EzijingSso\Constants\ErrorCode::PARAMS_ERROR,
                    $allowAttributeName
                );
            }
        }
    }
}
