<?php

declare(strict_types=1);

namespace HyperfTest\Cases;

/**
 * @internal
 * @coversNothing
 */
class SsoTest extends AbstractTestCase
{
    public function testLogin()
    {
        var_dump(env('SSO_NEWSSO_API_HOST'));
    }
}
