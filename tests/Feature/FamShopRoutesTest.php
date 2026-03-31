<?php

namespace Tests\Feature;

use Tests\TestCase;

class FamShopRoutesTest extends TestCase
{
    public function test_guest_can_view_landing_and_login_pages()
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
    }

    public function test_explore_route_is_registered()
    {
        $routes = collect(app('router')->getRoutes()->getRoutesByMethod()['GET'] ?? []);

        $this->assertTrue(
            $routes->contains(function ($route) {
                return $route->uri() === 'explore';
            })
        );
    }
}
