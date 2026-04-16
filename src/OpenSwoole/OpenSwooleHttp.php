<?php

declare(strict_types=1);

/*
 * This file is part of the Valkyrja Framework package.
 *
 * (c) Melech Mizrachi <melechmizrachi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Valkyrja\OpenSwoole;

use OpenSwoole\HTTP\Request;
use OpenSwoole\HTTP\Response;
use OpenSwoole\HTTP\Server;
use Valkyrja\Application\Data\HttpConfig;
use Valkyrja\Application\Entry\Abstract\WorkerHttp;
use Valkyrja\Application\Env\Env;

class OpenSwooleHttp extends WorkerHttp
{
    /**
     * Run the Swoole app.
     *
     * @see https://openswoole.com/
     */
    public static function run(HttpConfig $config, Env $env = new Env()): void
    {
        $app = static::bootstrap(
            config: $config,
            env: $env,
        );

        $container = $app->getContainer();
        $data      = $container->getData();

        $server = static::getSwooleServer();

        $server->on('start', static function (Server $server): void {
            // echo "OpenSwoole http server is started at http://127.0.0.1:9501\n";
        });

        $server->on('request', static function (Request $swooleRequest, Response $swooleResponse) use ($app, $data): void {
            static::handle($app, $data, static::getRequest());
        });

        $server->start();
    }

    /**
     * Get the Swoole server.
     */
    public static function getSwooleServer(): Server
    {
        return new Server('127.0.0.1', 9501);
    }
}
