<?php

declare(strict_types=1);

namespace Mammatus\Tests\Kubernetes\Events\Helm;

use Mammatus\Groups\Attributes\Group;
use Mammatus\Groups\Type;
use Mammatus\Kubernetes\Events\Helm\Values;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\CronJob;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Deployment;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;

use const DIRECTORY_SEPARATOR;
use const PHP_INT_MAX;
use const PHP_INT_MIN;
use const PHP_INT_SIZE;

final class ValuesTest extends TestCase
{
    /** @return iterable<string, array{0: array<string>, 1: array<CronJob|Deployment>, 2: array<array{0: Group, 1: array<string, mixed>}>}> */
    public static function provideRegistryCalls(): iterable
    {
        yield 'basic-registry' => [
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'values.yaml',
                __DIR__ . DIRECTORY_SEPARATOR . 'values-secrets.yaml',
            ],
            [
                new Values\Registry\CronJob(
                    'basic',
                    self::class,
                    '* * * * *',
                    ['PHP_INT_SIZE' => PHP_INT_SIZE],
                ),
                new Values\Registry\Deployment(
                    'basic',
                    'mammatus-basic',
                    [
                        'key' => '${VALUES:nested.value}',
                        'foo' => '${VALUES:env.FOO}',
                    ],
                    [
                        'key' => '${VALUES:nested.value}',
                        'foo' => '${VALUES:env.FOO}',
                    ],
                ),
            ],
            [],
            [
                Values\Registry\Section::CronJob->value => [
                    'basic' => [
                        'name' => 'basic',
                        'class' => self::class,
                        'schedule' => '* * * * *',
                        'addOns' => ['PHP_INT_SIZE' => PHP_INT_SIZE],
                    ],
                ],
                Values\Registry\Section::Deployment->value => [
                    'basic' => [
                        'name' => 'basic',
                        'command' => 'mammatus-basic',
                        'arguments' => [
                            'key' => 'bier',
                            'foo' => 'bar',
                        ],
                        'addOns' => [
                            'key' => 'bier',
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ];

        yield 'basic-groups' => [
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'values.yaml',
                __DIR__ . DIRECTORY_SEPARATOR . 'values-secrets.yaml',
            ],
            [
                new Values\Registry\CronJob(
                    'basic',
                    self::class,
                    '* * * * *',
                    ['PHP_INT_SIZE' => PHP_INT_SIZE],
                ),
                new Values\Registry\Deployment(
                    'bier',
                    'mammatus-bier',
                    [
                        'key' => '${VALUES:nested.value}',
                        'foo' => '${VALUES:env.FOO}',
                    ],
                    [
                        'key' => '${VALUES:nested.value}',
                        'foo' => '${VALUES:env.FOO}',
                    ],
                ),
            ],
            [
                [
                    new Group(
                        Type::Normal,
                        'basic',
                    ),
                    ['PHP_INT_MAX' => PHP_INT_MAX],
                ],
                [
                    new Group(
                        Type::Daemon,
                        'spirit',
                    ),
                    ['PHP_INT_MIN' => PHP_INT_MIN],
                ],
            ],
            [
                Values\Registry\Section::CronJob->value => [
                    'basic' => [
                        'name' => 'basic',
                        'class' => self::class,
                        'schedule' => '* * * * *',
                        'addOns' => [
                            'PHP_INT_SIZE' => PHP_INT_SIZE,
                            'PHP_INT_MIN' => PHP_INT_MIN,
                        ],
                    ],
                ],
                Values\Registry\Section::Deployment->value => [
                    'bier' => [
                        'name' => 'bier',
                        'command' => 'mammatus-bier',
                        'arguments' => [
                            'key' => 'bier',
                            'foo' => 'bar',
                        ],
                        'addOns' => [
                            'key' => 'bier',
                            'foo' => 'bar',
                            'PHP_INT_MIN' => PHP_INT_MIN,
                        ],
                    ],
                    'basic' => [
                        'name' => 'basic',
                        'command' => 'mammatus',
                        'arguments' => ['basic'],
                        'addOns' => [
                            'PHP_INT_MAX' => PHP_INT_MAX,
                            'PHP_INT_MIN' => PHP_INT_MIN,
                        ],
                    ],
                ],
            ],
        ];

        yield 'mixed' => [
            [
                __DIR__ . DIRECTORY_SEPARATOR . 'values.yaml',
                __DIR__ . DIRECTORY_SEPARATOR . 'values-secrets.yaml',
            ],
            [],
            [
                [
                    new Group(
                        Type::Normal,
                        'basic',
                    ),
                    ['PHP_INT_SIZE' => PHP_INT_SIZE],
                ],
                [
                    new Group(
                        Type::Daemon,
                        'spirit',
                    ),
                    [
                        'key' => '${VALUES:nested.value}',
                        'foo' => '${VALUES:env.FOO}',
                    ],
                ],
            ],
            [
                Values\Registry\Section::Deployment->value => [
                    'basic' => [
                        'name' => 'basic',
                        'command' => 'mammatus',
                        'arguments' => ['basic'],
                        'addOns' => [
                            'PHP_INT_SIZE' => PHP_INT_SIZE,
                            'key' => 'bier',
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string>                                   $valuesFiles
     * @param array<CronJob|Deployment>                       $registryCalls
     * @param array<array{0: Group, 1: array<string, mixed>}> $groupsCalls
     * @param array<mixed>                                    $expectedValues
     */
    #[DataProvider('provideRegistryCalls')]
    #[Test]
    public function get(array $valuesFiles, array $registryCalls, array $groupsCalls, array $expectedValues): void
    {
        $values = new Values(
            new Values\Groups(),
            new Values\Registry(),
            Values\ValuesFile::createFromFile(...$valuesFiles),
        );

        foreach ($registryCalls as $registryCall) {
            $values->add($registryCall);
        }

        foreach ($groupsCalls as $groupsCall) {
            $values->addToGroup(...$groupsCall);
        }

        self::assertSame($expectedValues, $values->get());
    }
}
