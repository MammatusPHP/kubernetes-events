<?php

declare(strict_types=1);

namespace Mammatus\Tests\Kubernetes\Events\Helm;

use Mammatus\Kubernetes\Events\Helm\Values;
use WyriHaximus\TestUtilities\TestCase;

use const PHP_INT_SIZE;

final class ValuesTest extends TestCase
{
    /** @test */
    public function registry(): void
    {
        $values = new Values(new Values\Registry());

        $values->registry->add('secion', ['PHP_INT_SIZE' => PHP_INT_SIZE]);

        self::assertSame(['secion' => ['PHP_INT_SIZE' => PHP_INT_SIZE]], $values->registry->get());
    }
}
