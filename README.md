# Kubernetes related Events

![Continuous Integration](https://github.com/mammatusphp/kubernetes-events/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/mammatus/kubernetes-events/v/stable.png)](https://packagist.org/packages/mammatus/kubernetes-events)
[![Total Downloads](https://poser.pugx.org/mammatus/kubernetes-events/downloads.png)](https://packagist.org/packages/mammatus/kubernetes-events/stats)
[![Type Coverage](https://shepherd.dev/github/mammatusphp/kubernetes-events/coverage.svg)](https://shepherd.dev/github/mammatusphp/kubernetes-events)
[![License](https://poser.pugx.org/mammatus/kubernetes-events/license.png)](https://packagist.org/packages/mammatus/kubernetes-events)

# Install

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require mammatus/kubernetes-events
```

# Events

This package provides the following events:

## Helm/Values

This event is emitted when the Helm chart is being templated/installed/etc and is used to pass data to Helm to
render the Chart and any additional subcharts. It has one property, the registry, and anything you add into it using
the `add` method will be returned through the `get` method and passed directly to Helm.

```php
$values = new Values(new Values\Registry(Values\ValuesFile::createFromFile('path/to/helm/chart/values.yaml'));

$values->registry->add(Values\Registry\Section::Deployment, ['PHP_INT_SIZE' => PHP_INT_SIZE]);

$values->registry->get()); // Returns: ['deployments' => ['PHP_INT_SIZE' => PHP_INT_SIZE]]
```

# License

The MIT License (MIT)

Copyright (c) 2025 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
