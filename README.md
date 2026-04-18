<p align="center"><a href="https://valkyrja.io" target="_blank">
    <img src="https://raw.githubusercontent.com/valkyrjaio/art/refs/heads/master/long-banner/orange/php.png" width="100%">
</a></p>

# Valkyrja OpenSwoole

OpenSwoole persistent worker entry point for the [Valkyrja][Valkyrja url]
PHP framework.

This integration bootstraps the Valkyrja application once at worker startup,
then dispatches every incoming request to an isolated child container so
request state never bleeds between concurrent requests. The result is the
performance benefit of a persistent process without the state-contamination
risks of naive long-running PHP.

<p>
    <a href="https://packagist.org/packages/valkyrja/openswoole"><img src="https://poser.pugx.org/valkyrja/openswoole/require/php" alt="PHP Version Require"></a>
    <a href="https://packagist.org/packages/valkyrja/openswoole"><img src="https://poser.pugx.org/valkyrja/openswoole/v" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/valkyrja/openswoole"><img src="https://poser.pugx.org/valkyrja/openswoole/license" alt="License"></a>
    <a href="https://github.com/valkyrjaio/valkyrja-openswoole-php/actions/workflows/ci.yml?query=branch%3Amaster"><img src="https://github.com/valkyrjaio/valkyrja-openswoole-php/actions/workflows/ci.yml/badge.svg?branch=master" alt="CI Status"></a>
    <a href="https://scrutinizer-ci.com/g/valkyrjaio/valkyrja-openswoole-php/?branch=master"><img src="https://scrutinizer-ci.com/g/valkyrjaio/valkyrja-openswoole-php/badges/quality-score.png?b=master" alt="Scrutinizer"></a>
    <a href="https://coveralls.io/github/valkyrjaio/valkyrja-openswoole-php?branch=master"><img src="https://coveralls.io/repos/github/valkyrjaio/valkyrja-openswoole-php/badge.svg?branch=master" alt="Coverage Status" /></a>
    <a href="https://shepherd.dev/github/valkyrjaio/valkyrja-openswoole-php"><img src="https://shepherd.dev/github/valkyrjaio/valkyrja-openswoole-php/coverage.svg" alt="Psalm Shepherd" /></a>
    <a href="https://sonarcloud.io/summary/new_code?id=valkyrjaio_openswoole"><img src="https://sonarcloud.io/api/project_badges/measure?project=valkyrjaio_openswoole&metric=sqale_rating" alt="Maintainability Rating" /></a>
</p>

Requirements
------------

- PHP 8.4+
- The [OpenSwoole][openswoole docs url] PHP extension
  (`openswoole/core ^26.2.0`)
- An existing [Valkyrja][framework url] application

Installation
------------

```
composer require valkyrja/openswoole
```

Usage
-----

Wire the OpenSwoole entry point into your application's front controller:

```
// app/public/index.php
use Valkyrja\Application\Data\HttpConfig;
use Valkyrja\OpenSwoole\OpenSwooleHttp;

OpenSwooleHttp::run(new HttpConfig(
    dir: __DIR__ . '/..',
));
```

`run()` bootstraps the application once when the worker process starts, then
starts the OpenSwoole HTTP server. Each request is handled in an isolated
child container so state never bleeds between requests.

### Customizing the Server

Override `getSwooleServer()` to configure the server address, port, or
options:

```
use OpenSwoole\HTTP\Server;
use Valkyrja\OpenSwoole\OpenSwooleHttp;

class App extends OpenSwooleHttp
{
    public static function getSwooleServer(): Server
    {
        $server = new Server('0.0.0.0', 8080);
        $server->set([
            'worker_num'  => 4,
            'max_request' => 10000,
        ]);

        return $server;
    }
}
```

### Customizing Bootstrap

Override `bootstrapParentServices()` to force-resolve services that are
expensive to create and safe to share across requests:

```
use Valkyrja\Application\Kernel\Contract\ApplicationContract;
use Valkyrja\Http\Routing\Collection\Contract\CollectionContract;
use Valkyrja\OpenSwoole\OpenSwooleHttp;

class App extends OpenSwooleHttp
{
    protected static function bootstrapParentServices(ApplicationContract $app): void
    {
        $container = $app->getContainer();
        $container->getSingleton(CollectionContract::class);
        $container->getSingleton(MyExpensiveSharedService::class);
    }
}
```

Worker Lifecycle
----------------

See the [Valkyrja framework repository][framework url] for a full explanation
of the persistent worker lifecycle, the child container isolation model, and
configuration options.

Related Integrations
--------------------

Other persistent-worker runtime integrations for Valkyrja PHP:

- [**FrankenPHP**][frankenphp url] — persistent worker via the FrankenPHP
  embedded runtime
- [**RoadRunner**][roadrunner url] — persistent worker via the Go-based
  RoadRunner manager

Contributing
------------

See [`CONTRIBUTING.md`][contributing url] for the submission process and
[`VOCABULARY.md`][vocabulary url] for the terminology used across Valkyrja.

Security Issues
---------------

If you discover a security vulnerability, please follow our
[disclosure procedure][security vulnerabilities url].

License
-------

Licensed under the [MIT license][MIT license url]. See
[`LICENSE.md`](./LICENSE.md).

[Valkyrja url]: https://valkyrja.io

[framework url]: https://github.com/valkyrjaio/valkyrja-php

[openswoole docs url]: https://openswoole.com/

[frankenphp url]: https://github.com/valkyrjaio/valkyrja-frankenphp-php

[roadrunner url]: https://github.com/valkyrjaio/valkyrja-roadrunner-php

[contributing url]: https://github.com/valkyrjaio/.github/blob/master/CONTRIBUTING.md

[vocabulary url]: https://github.com/valkyrjaio/.github/blob/master/VOCABULARY.md

[security vulnerabilities url]: https://github.com/valkyrjaio/.github/blob/master/SECURITY.md

[MIT license url]: https://opensource.org/licenses/MIT
