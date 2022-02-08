<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => '发布配置文件.',
                    'source' => __DIR__ . '/../publish/sso_plugins.php',
                    'destination' => BASE_PATH . '/config/autoload/sso_plugins.php',
                ],
            ],
        ];
    }
}
