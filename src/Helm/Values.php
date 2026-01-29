<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm;

use Mammatus\Groups\Attributes\Group;
use Mammatus\Groups\Type;
use Mammatus\Kubernetes\Events\Helm\Values\Groups;
use Mammatus\Kubernetes\Events\Helm\Values\Registry;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\CronJob;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Deployment;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Section;
use Mammatus\Kubernetes\Events\Helm\Values\ValuesFile;

use function array_key_exists;
use function count;

final readonly class Values
{
    private function __construct(
        private Groups $groups,
        private Registry $registry,
        private ValuesFile $valuesFile,
    ) {
    }

    public static function createFromFile(string ...$valuesFiles): self
    {
        return new self(
            new Groups(),
            new Registry(),
            ValuesFile::createFromFile(...$valuesFiles),
        );
    }

    public function add(CronJob|Deployment $values): void
    {
        $this->registry->add($values);
    }

    /** @param array<array{helper: string, type: string, arguments: array<string, mixed>}> $addOns */
    public function addToGroup(Group $group, array $addOns): void
    {
        $this->groups->add($group, $addOns);
    }

    /** @return array<string, array<string, array{name: string, command: string, arguments: array<int, mixed>, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}|array{name: string, class: string, schedule: string, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>> */
    public function get(): array
    {
        $registry = clone $this->registry;
        foreach ($this->groups->get() as $group) {
            if ($group->group->type->name !== Type::Normal->name) {
                continue;
            }

            $registry->add(
                new Deployment(
                    $group->group->name,
                    'mammatus',
                    [
                        $group->group->name,
                    ],
                    $group->addOns,
                ),
            );
        }

        $values = $registry->get();
        foreach ($this->groups->get() as $group) {
            if ($group->group->type->name !== Type::Daemon->name || count($group->addOns) <= 0) {
                continue;
            }

            foreach ($values as $type => $items) {
                foreach ($items as $name => $item) {
                    $values[$type][$name]->add($group->addOns);
                }
            }
        }

        return $this->removeProbeAddOnFromCronJobs(
            /** @phpstan-ignore argument.type */
            $this->valuesFile->swapInValues(
                $this->convertToArray(
                    $values,
                ),
            ),
        );
    }

    /**
     * @param array<string, array<string|int, CronJob|Deployment>> $fromValues
     *
     * @return array<string, array<string, array{name: string, command: string, arguments: array<int, mixed>, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}|array{name: string, class: string, schedule: string, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>>
     */
    private function convertToArray(array $fromValues): array
    {
        $toValues = [];
        foreach ($fromValues as $type => $items) {
            foreach ($items as $name => $item) {
                $toValues[$type][$name] = $item->jsonSerialize();
            }
        }

        /** @phpstan-ignore return.type */
        return $toValues;
    }

    /**
     * @param array<string, array<string, array{name: string, command: string, arguments: array<int, mixed>, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}|array{name: string, class: string, schedule: string, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>> $values
     *
     * @return array<string, array<string, array{name: string, command: string, arguments: array<int, mixed>, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}|array{name: string, class: string, schedule: string, addOns: array<array{helper: string, type: string, arguments: array<string, mixed>}>}>>
     */
    private function removeProbeAddOnFromCronJobs(array $values): array
    {
        if (array_key_exists(Section::CronJob->value, $values)) {
            foreach ($values[Section::CronJob->value] as $name => $cronJob) {
                $addOns = [];
                foreach ($cronJob['addOns'] as $addOn) {
                    if ($addOn['helper'] === 'mammatus.container.probe') {
                        continue;
                    }

                    $addOns[] = $addOn;
                }

                $values[Section::CronJob->value][$name]['addOns'] = $addOns;
            }
        }

        return $values;
    }
}
