<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Values\Registry;

enum Section: string
{
    case CronJob    = 'cronjobs';
    case Deployment = 'deployments';
}
