<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values;

use Mammatus\Kubernetes\Events\Helm\Groups\Section;

final class Registry
{
    /** @var array<string, array<string|int, mixed>> */
    private array $values = [];

    public function __construct(private readonly ValuesFile $valuesFile)
    {
    }

    /** @param array<string|int, mixed> $values */
    public function add(Section $section, array $values): void
    {
        $this->values[$section->value] = $this->valuesFile->swapInValues($values);
    }

    /** @return array<string, array<string|int, mixed>> */
    public function get(): array
    {
        return $this->values;
    }
}
