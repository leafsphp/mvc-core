<?php

// mvc-core's global helpers, tested directly

test('route() resolves names with associative params', function () {
    \Leaf\Router::reset();

    app()->group('/admin', ['name' => 'admin', function () {
        app()->resource('/users', 'UsersController');
    }]);

    expect(route('admin.users.show', ['id' => 5]))->toBe('/admin/users/5');
});

test('route() resolves names with positional params', function () {
    \Leaf\Router::reset();

    app()->group('/shop', ['name' => 'shop', function () {
        app()->get('/orders/{year}/{id}', ['name' => 'orders.show', function () {}]);
    }]);

    expect(route('shop.orders.show', 2026, 42))->toBe('/shop/orders/2026/42');
});

test('route() without params returns the pattern', function () {
    \Leaf\Router::reset();

    app()->get('/plain', ['name' => 'plain', function () {}]);

    expect(route('plain'))->toBe('/plain');
});
