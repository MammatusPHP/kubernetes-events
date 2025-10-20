<?php

declare(strict_types=1);

namespace Mammatus\Tests\Kubernetes\Events\Helm;

use Mammatus\Kubernetes\Events\Helm\Values;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

use const DIRECTORY_SEPARATOR;
use const PHP_INT_SIZE;

final class ValuesTest extends TestCase
{
    #[Test]
    public function registry(): void
    {
        $values = new Values(new Values\Registry(Values\ValuesFile::createFromFile(__DIR__ . DIRECTORY_SEPARATOR . 'values.yaml', __DIR__ . DIRECTORY_SEPARATOR . 'values-secrets.yaml')));

        $values->registry->add('secion', ['PHP_INT_SIZE' => PHP_INT_SIZE]);
        $values->registry->add('fromValues', ['key' => '${VALUES:nested.value}', 'foo' => '${VALUES:env.FOO}']);

        self::assertSame(['secion' => ['PHP_INT_SIZE' => PHP_INT_SIZE], 'fromValues' => ['key' => 'bier', 'foo' => 'bar']], $values->registry->get());
    }
}
