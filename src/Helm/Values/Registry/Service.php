<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values\Registry;

use JsonSerializable;

/** @api */
final readonly class Service implements JsonSerializable
{
    public function __construct(
        public string $name,
        public string $group,
        public int $port,
    ) {
    }

    /** @return array{name: string, group: string, port: int} */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'group' => $this->group,
            'port' => $this->port,
        ];
    }
}
