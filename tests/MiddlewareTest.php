<?php

namespace PragmaRX\Tracker\Tests;

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use PragmaRX\Tracker\Package\Tracker as TrackerService;

class MiddlewareTest extends TestCase
{
    public function test_can_instantiate_service()
    {
        $this->assertInstanceOf(TrackerService::class, $this->tracker);
    }

    public function test_can_route()
    {
        $router = $this->getRouter();

        $router->get('foo/bar', function () {
            return 'hello';
        });

        $this->assertEquals('hello', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());
    }

    public function test_use_middleware()
    {
        $router = $this->getRouter();

        $router->get('/home', function () {
            return 'working';
        })->middleware(\PragmaRX\Tracker\Package\Http\Middleware\Tracker::class);

        $this->assertEquals('working', $router->dispatch(Request::create('home', 'GET'))->getContent());
    }

    protected function getRouter()
    {
        $container = new Container();

        $router = new Router(new Dispatcher(), $container);

        $container->singleton(Registrar::class, function () use ($router) {
            return $router;
        });

        return $router;
    }
}
