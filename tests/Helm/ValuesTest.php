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

final class ValuesTest extends TestCase
{
    /** @return iterable<string, array{0: array<string>, 1: array<CronJob|Deployment>, 2: array<array{0: Group, 1: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>, 3: array<string, array<string, array{name: string, command: string, arguments: array<int, mixed>, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}|array{name: string, class: string, schedule: string, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>>}> */
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
                    [
                        [
                            'helper' => 'mammatus.container.resources',
                            'type' => 'container',
                            'arguments' => [
                                'cpu' => '1000m',
                                'memory' => '263Mi',
                            ],
                        ],
                    ],
                ),
                new Values\Registry\Deployment(
                    'basic',
                    'mammatus-basic',
                    [
                        '${VALUES:nested.value}',
                        '${VALUES:env.FOO}',
                    ],
                    [
                        [
                            'helper' => 'nested.value',
                            'type' => 'container',
                            'arguments' => ['key' => '${VALUES:nested.value}'],
                        ],
                        [
                            'helper' => 'env.FOO',
                            'type' => 'container',
                            'arguments' => ['foo' => '${VALUES:env.FOO}'],
                        ],
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
                        'addOns' => [
                            [
                                'helper' => 'mammatus.container.resources',
                                'type' => 'container',
                                'arguments' => [
                                    'cpu' => '1000m',
                                    'memory' => '263Mi',
                                ],
                            ],
                        ],
                    ],
                ],
                Values\Registry\Section::Deployment->value => [
                    'basic' => [
                        'name' => 'basic',
                        'command' => 'mammatus-basic',
                        'arguments' => [
                            'bier',
                            'bar',
                        ],
                        'addOns' => [
                            [
                                'helper' => 'nested.value',
                                'type' => 'container',
                                'arguments' => ['key' => 'bier'],
                            ],
                            [
                                'helper' => 'env.FOO',
                                'type' => 'container',
                                'arguments' => ['foo' => 'bar'],
                            ],
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
                    [
                        [
                            'helper' => 'mammatus.container.resources',
                            'type' => 'container',
                            'arguments' => [
                                'cpu' => '1000m',
                                'memory' => '263Mi',
                            ],
                        ],
                    ],
                ),
                new Values\Registry\Deployment(
                    'bier',
                    'mammatus-bier',
                    [
                        '${VALUES:nested.value}',
                        '${VALUES:env.FOO}',
                    ],
                    [
                        [
                            'helper' => 'nested.value',
                            'type' => 'container',
                            'arguments' => ['key' => '${VALUES:nested.value}'],
                        ],
                        [
                            'helper' => 'env.FOO',
                            'type' => 'container',
                            'arguments' => ['foo' => '${VALUES:env.FOO}'],
                        ],
                    ],
                ),
            ],
            [
                [
                    new Group(
                        Type::Normal,
                        'basic',
                    ),
                    [
                        [
                            'helper' => 'mammatus.container.resources',
                            'type' => 'container',
                            'arguments' => [
                                'cpu' => '1000m',
                                'memory' => '263Mi',
                            ],
                        ],
                    ],
                ],
                [
                    new Group(
                        Type::Daemon,
                        'healthz',
                    ),
                    [
                        [
                            'helper' => 'mammatus.container.port',
                            'type' => 'container',
                            'arguments' => [
                                'name' => 'healthz',
                                'containerPort' => 9666,
                            ],
                        ],
                        [
                            'helper' => 'mammatus.container.probe',
                            'type' => 'container',
                            'arguments' => [
                                'liveness' => [
                                    'path' => '/probe/liveness',
                                    'vhost' => 'healthz',
                                ],
                                'readiness' => [
                                    'path' => '/probe/readiness',
                                    'vhost' => 'healthz',
                                ],
                                'startUp' => [
                                    'path' => '/probe/startup',
                                    'vhost' => 'healthz',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            [
                Values\Registry\Section::CronJob->value => [
                    'basic' => [
                        'name' => 'basic',
                        'class' => self::class,
                        'schedule' => '* * * * *',
                        'addOns' => [
                            [
                                'helper' => 'mammatus.container.resources',
                                'type' => 'container',
                                'arguments' => [
                                    'cpu' => '1000m',
                                    'memory' => '263Mi',
                                ],
                            ],
                            [
                                'helper' => 'mammatus.container.port',
                                'type' => 'container',
                                'arguments' => [
                                    'name' => 'healthz',
                                    'containerPort' => 9666,
                                ],
                            ],
                        ],
                    ],
                ],
                Values\Registry\Section::Deployment->value => [
                    'bier' => [
                        'name' => 'bier',
                        'command' => 'mammatus-bier',
                        'arguments' => [
                            'bier',
                            'bar',
                        ],
                        'addOns' => [
                            [
                                'helper' => 'nested.value',
                                'type' => 'container',
                                'arguments' => ['key' => 'bier'],
                            ],
                            [
                                'helper' => 'env.FOO',
                                'type' => 'container',
                                'arguments' => ['foo' => 'bar'],
                            ],
                            [
                                'helper' => 'mammatus.container.port',
                                'type' => 'container',
                                'arguments' => [
                                    'name' => 'healthz',
                                    'containerPort' => 9666,
                                ],
                            ],
                            [
                                'helper' => 'mammatus.container.probe',
                                'type' => 'container',
                                'arguments' => [
                                    'liveness' => [
                                        'path' => '/probe/liveness',
                                        'vhost' => 'healthz',
                                    ],
                                    'readiness' => [
                                        'path' => '/probe/readiness',
                                        'vhost' => 'healthz',
                                    ],
                                    'startUp' => [
                                        'path' => '/probe/startup',
                                        'vhost' => 'healthz',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'basic' => [
                        'name' => 'basic',
                        'command' => 'mammatus',
                        'arguments' => ['basic'],
                        'addOns' => [
                            [
                                'helper' => 'mammatus.container.resources',
                                'type' => 'container',
                                'arguments' => [
                                    'cpu' => '1000m',
                                    'memory' => '263Mi',
                                ],
                            ],
                            [
                                'helper' => 'mammatus.container.port',
                                'type' => 'container',
                                'arguments' => [
                                    'name' => 'healthz',
                                    'containerPort' => 9666,
                                ],
                            ],
                            [
                                'helper' => 'mammatus.container.probe',
                                'type' => 'container',
                                'arguments' => [
                                    'liveness' => [
                                        'path' => '/probe/liveness',
                                        'vhost' => 'healthz',
                                    ],
                                    'readiness' => [
                                        'path' => '/probe/readiness',
                                        'vhost' => 'healthz',
                                    ],
                                    'startUp' => [
                                        'path' => '/probe/startup',
                                        'vhost' => 'healthz',
                                    ],
                                ],
                            ],
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
                    [
                        [
                            'helper' => 'mammatus.container.resources',
                            'type' => 'container',
                            'arguments' => [
                                'cpu' => '1000m',
                                'memory' => '263Mi',
                            ],
                        ],
                    ],
                ],
                [
                    new Group(
                        Type::Daemon,
                        'spirit',
                    ),
                    [
                        [
                            'helper' => 'nested.value',
                            'type' => 'container',
                            'arguments' => ['key' => '${VALUES:nested.value}'],
                        ],
                        [
                            'helper' => 'env.FOO',
                            'type' => 'container',
                            'arguments' => ['foo' => '${VALUES:env.FOO}'],
                        ],
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
                            [
                                'helper' => 'mammatus.container.resources',
                                'type' => 'container',
                                'arguments' => [
                                    'cpu' => '1000m',
                                    'memory' => '263Mi',
                                ],
                            ],
                            [
                                'helper' => 'nested.value',
                                'type' => 'container',
                                'arguments' => ['key' => 'bier'],
                            ],
                            [
                                'helper' => 'env.FOO',
                                'type' => 'container',
                                'arguments' => ['foo' => 'bar'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array<string>                                                                                                                                                                                                                                                                                                                   $valuesFiles
     * @param array<CronJob|Deployment>                                                                                                                                                                                                                                                                                                       $registryCalls
     * @param array<array{0: Group, 1: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>                                                                                                                                                                                                                          $groupsCalls
     * @param array<string, array<string, array{name: string, command: string, arguments: array<int, mixed>, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}|array{name: string, class: string, schedule: string, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>> $expectedValues
     */
    #[DataProvider('provideRegistryCalls')]
    #[Test]
    public function get(array $valuesFiles, array $registryCalls, array $groupsCalls, array $expectedValues): void
    {
        $values = Values::createFromFile(...$valuesFiles);

        foreach ($registryCalls as $registryCall) {
            $values->add($registryCall);
        }

        foreach ($groupsCalls as $groupsCall) {
            $values->addToGroup(...$groupsCall);
        }

        self::assertSame($expectedValues, $values->get());
    }
}
