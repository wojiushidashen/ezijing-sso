<?php

declare(strict_types=1);

namespace Ezijing\EzijingSso\Exceptions;

use Ezijing\EzijingSso\Constants\ErrorCode;
use Throwable;

class PluginException extends \RuntimeException
{
    public function __construct($code = 0, $message = '', Throwable $previous = null)
    {
        if (!$message) {
            $message = ErrorCode::getMessage($code);
        }else{
            if (is_array($message)) {
                $message = ErrorCode::getMessage($code, $message);
            }
        }

        parent::__construct($message, $code, $previous);
    }
}
