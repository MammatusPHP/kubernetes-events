<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values;

use Mammatus\Kubernetes\Events\Helm\Values\Registry\CronJob;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Deployment;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Section;

final class Registry
{
    /** @var array<string, array<string|int, CronJob|Deployment>> */
    private array $values = [];

    public function add(CronJob|Deployment $values): void
    {
        $this->values[$values instanceof CronJob ? Section::CronJob->value : Section::Deployment->value][$values->name] = $values;
    }

    /** @return array<string, array<string|int, CronJob|Deployment>> */
    public function get(): array
    {
        return $this->values;
    }
}
