<?php

declare(strict_types=1);

namespace Mammatus\Tests\Kubernetes\Events\Helm\Values;

use InvalidArgumentException;
use Mammatus\Kubernetes\Events\Helm\Values\Registry;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use WyriHaximus\TestUtilities\TestCase;

final class RegistryTest extends TestCase
{
    #[Test]
    public function getSectionThrowsForUnknownClass(): void
    {
        $registry   = new Registry();
        $getSection = new ReflectionMethod(Registry::class, 'getSection');

        try {
            $getSection->invoke($registry, 'stdClass');
        } catch (InvalidArgumentException $exception) {
            self::assertSame('Invalid section name: stdClass', $exception->getMessage());

            return;
        }

        self::fail('Expected InvalidArgumentException to be thrown.');
    }
}
