<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values;

final class Registry
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $values = [];

    public function add(string $section, array $values): void
    {
        $this->values[$section] = $values;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function get(): array
    {
        return $this->values;
    }
}
