<?php

namespace PragmaRX\Tracker\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\Registrar;
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

    public function test_can_use_routing_system()
    {
        $router = $this->getRouter();

        $router->get('foo/bar', function () {
            return 'hello';
        });

        $this->assertEquals('hello', $router->dispatch(Request::create('foo/bar', 'GET'))->getContent());
    }

    public function test_use_middleware()
    {
        $this->assertFalse($this->tracker->isBooted());

        $router = $this->getRouter();

        $router->get('/no-tracking', function () {
            return 'not tracked';
        });

        $this->assertEquals('not tracked', $router->dispatch(Request::create('no-tracking', 'GET'))->getContent());

        $this->assertFalse($this->tracker->isBooted());

        $router->get('/tracking', function () {
            return 'being tracked';
        })->middleware(\PragmaRX\Tracker\Package\Http\Middleware\Tracker::class);

        $this->assertEquals('being tracked', $router->dispatch(Request::create('tracking', 'GET'))->getContent());

        $this->assertTrue($this->tracker->isBooted());
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
