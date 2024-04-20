<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm;

use Mammatus\Kubernetes\Events\Helm\Values\Registry;

final readonly class Values
{
    public function __construct(
        public Registry $registry,
    )
    {
    }
}
