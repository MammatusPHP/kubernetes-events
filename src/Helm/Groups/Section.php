<?php

declare(strict_types=1);

namespace Mammatus\Kubernetes\Events\Helm\Groups;

enum Section: string
{
    case CronJob    = 'cronjobs';
    case Deployment = 'deployments';
}
