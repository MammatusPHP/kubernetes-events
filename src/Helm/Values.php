<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm;

use Mammatus\Groups\Attributes\Group;
use Mammatus\Groups\Type;
use Mammatus\Kubernetes\Events\Helm\Values\Groups;
use Mammatus\Kubernetes\Events\Helm\Values\Registry;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\CronJob;
use Mammatus\Kubernetes\Events\Helm\Values\Registry\Deployment;
use Mammatus\Kubernetes\Events\Helm\Values\ValuesFile;

use function count;

final readonly class Values
{
    public function __construct(
        private Groups $groups,
        private Registry $registry,
        private ValuesFile $valuesFile,
    ) {
    }

    public function add(CronJob|Deployment $values): void
    {
        $this->registry->add($values);
    }

    /** @param array<string|int, mixed> $addOns */
    public function addToGroup(Group $group, array $addOns): void
    {
        $this->groups->add($group, $addOns);
    }

    /** @return array<string|int, mixed> */
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

        return $this->valuesFile->swapInValues(
            $this->convertToArray(
                $values,
            ),
        );
    }

    /**
     * @param array<string, array<string|int, CronJob|Deployment>> $fromValues
     *
     * @return array<string, array<string|int, array<string, mixed>>>
     */
    private function convertToArray(array $fromValues): array
    {
        $toValues = [];
        foreach ($fromValues as $type => $items) {
            foreach ($items as $name => $item) {
                $toValues[$type][$name] = $item->jsonSerialize();
            }
        }

        return $toValues;
    }
}
