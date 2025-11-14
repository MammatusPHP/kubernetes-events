<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values\Groups;

use Mammatus\Groups\Attributes\Group as GroupAttribute;

final class Group
{
    /**
     * @param array<string|int, mixed> $addOns
     *
     * @phpstan-ignore ergebnis.noConstructorParameterWithDefaultValue
     */
    public function __construct(
        public readonly GroupAttribute $group,
        /** @phpstan-ignore shipmonk.publicPropertyNotReadonly */
        public private(set) array $addOns = [],
    ) {
    }

    /** @param array<string|int, mixed> $addOns */
    public function add(array $addOns): void
    {
        $this->addOns = [...$this->addOns, ...$addOns];
    }
}
