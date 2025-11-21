<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values\Registry;

use JsonSerializable;

final class Deployment implements JsonSerializable
{
    /**
     * @param array<string>            $arguments
     * @param array<string|int, mixed> $addOns
     *
     * @phpstan-ignore ergebnis.noConstructorParameterWithDefaultValue,ergebnis.noConstructorParameterWithDefaultValue
     */
    public function __construct(
        public readonly string $name,
        public readonly string $command,
        /** @phpstan-ignore shipmonk.publicPropertyNotReadonly */
        public private(set) array $arguments = [],
        /** @phpstan-ignore shipmonk.publicPropertyNotReadonly */
        public private(set) array $addOns = [],
    ) {
    }

    /** @param array<string|int, mixed> $addOns */
    public function add(array $addOns): void
    {
        $this->addOns = [...$this->addOns, ...$addOns];
    }

    /** @return array{name: string, command: string, arguments: array<string>, addOns: array<string|int, mixed>} */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'command' => $this->command,
            'arguments' => $this->arguments,
            'addOns' => $this->addOns,
        ];
    }
}
