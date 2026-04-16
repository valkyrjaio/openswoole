<p align="center"><a href="https://valkyrja.io" target="_blank">
    <img src="https://raw.githubusercontent.com/valkyrjaio/art/refs/heads/master/long-banner/orange/php.png" width="100%">
</a></p>

# Valkyrja OpenSwoole

OpenSwoole persistent worker entry point for the [Valkyrja Framework](https://www.valkyrja.io).

About
-----

> This repository provides the OpenSwoole persistent worker entry point for the Valkyrja Framework.

Bootstraps the application once at startup, then dispatches every incoming request to an
isolated child container — so request state never bleeds between concurrent requests.

## Installation

```bash
composer require valkyrja/openswoole
```

Requires the [OpenSwoole](https://openswoole.com/) extension (`openswoole/core ^26.2.0`).

## Usage

```php
// app/public/index.php
use Valkyrja\Application\Data\HttpConfig;
use Valkyrja\OpenSwoole\OpenSwooleHttp;

OpenSwooleHttp::run(new HttpConfig(
    dir: __DIR__ . '/..',
));
```

`run()` bootstraps the application once when the worker process starts, then
starts the OpenSwoole HTTP server. Each request is handled in an isolated child
container so state never bleeds between requests.

## Customising the Server

Override `getSwooleServer()` to configure the server address, port, or options:

```php
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

## Customising Bootstrap

Override `bootstrapParentServices()` to force-resolve services that are
expensive to create and safe to share across requests:

```php
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

## Worker Lifecycle

See the [Valkyrja Framework README](https://github.com/valkyrjaio/valkyrja) for
a full explanation of the persistent worker lifecycle, the child container
isolation model, and configuration options.

## License

MIT