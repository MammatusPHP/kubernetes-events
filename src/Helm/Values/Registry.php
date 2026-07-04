<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values;

use InvalidArgumentException;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\CronJob;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Deployment;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Section;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Service;

final class Registry
{
    /** @var array<string, array<string|int, CronJob|Deployment|Service>> */
    private array $values = [];

    public function add(CronJob|Deployment|Service $values): void
    {
        $this->values[$this->getSection($values::class)->value][$values->name] = $values;
    }

    /** @return array<string, array<string|int, CronJob|Deployment|Service>> */
    public function get(): array
    {
        return $this->values;
    }

    private function getSection(string $name): Section
    {
        return match ($name) {
            CronJob::class => Section::CronJob,
            Deployment::class => Section::Deployment,
            Service::class => Section::Service,
            default => throw new InvalidArgumentException('Invalid section name: ' . $name),
        };
    }
}
