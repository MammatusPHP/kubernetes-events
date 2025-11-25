<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values;

use Mammatus\Groups\Attributes\Group as GroupAttribute;
use Mammatus\Groups\Generated\AbstractGroups;
use Mammatus\Kubernetes\Events\Helm\Values\Groups\Group;

use function array_key_exists;

final class Groups
{
    /** @var array<string, Group> */
    private array $groups = [];

    public function __construct()
    {
        foreach (AbstractGroups::groups() as $group) {
            $this->groups[$group->name] = new Group($group);
        }
    }

    /** @param array<string|int, mixed> $addOns */
    public function add(GroupAttribute $group, array $addOns): void
    {
        if (! array_key_exists($group->name, $this->groups)) {
            $this->groups[$group->name] = new Group($group);
        }

        $this->groups[$group->name]->add($addOns);
    }

    /** @return array<string, Group> */
    public function get(): array
    {
        return $this->groups;
    }
}
