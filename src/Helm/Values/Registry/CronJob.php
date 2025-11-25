<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values\Registry;

use JsonSerializable;

final class CronJob implements JsonSerializable
{
    /**
     * @param array<string|int, mixed> $addOns
     *
     * @phpstan-ignore ergebnis.noConstructorParameterWithDefaultValue
     */
    public function __construct(
        public readonly string $name,
        public readonly string $class,
        public readonly string $schedule,
        /** @phpstan-ignore shipmonk.publicPropertyNotReadonly */
        public private(set) array $addOns = [],
    ) {
    }

    /** @param array<string|int, mixed> $addOns */
    public function add(array $addOns): void
    {
        $this->addOns = [...$this->addOns, ...$addOns];
    }

    /** @return array{name: string, class: string, schedule: string, addOns: array<string|int, mixed>} */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'class' => $this->class,
            'schedule' => $this->schedule,
            'addOns' => $this->addOns,
        ];
    }
}
