<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values\Registry;

use JsonSerializable;

/** @api */
final readonly class Ingress implements JsonSerializable
{
    /** @phpstan-ignore ergebnis.noConstructorParameterWithDefaultValue */
    public function __construct(
        public string $name,
        public string $service,
        public string $host,
        public string $path = '/',
    ) {
    }

    /** @return array{name: string, service: string, host: string, path: string} */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'service' => $this->service,
            'host' => $this->host,
            'path' => $this->path,
        ];
    }
}
