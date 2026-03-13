<?php

use App\Models\Product;
use App\Models\ProductSitePrice;
use App\Models\Site;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    $this->cartToken = (string) Str::uuid();

    $this->site = Site::create([
        'code' => 'test',
        'name' => 'Test',
        'domain' => 'localhost',
    ]);

    $this->product = Product::create([
        'name' => 'Product A',
        'stock' => 100,
    ]);

    $this->productSitePrice = ProductSitePrice::create([
        'product_id' => $this->product->id,
        'site_id' => $this->site->id,
        'price' => 50,
    ]);
});

it('add product to the cart', function () {
    $quantity = 2;

    $response = postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $quantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonPath("items.{$this->product->id}.unit_price", $this->productSitePrice->price)
        ->assertJsonPath("items.{$this->product->id}.quantity", $quantity);
});

it('merges quanitites when product exists', function () {
    $existedQuantity = 2;
    $addedQuantity = 2;

    postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $existedQuantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response = postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $addedQuantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonPath("items.{$this->product->id}.unit_price", $this->productSitePrice->price)
        ->assertJsonPath("items.{$this->product->id}.quantity", $existedQuantity + $addedQuantity);
});

it('validates quantity against product stock', function () {
    $invalidQuantity = 1000;

    $response = postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $invalidQuantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonValidationErrors(['quantity']);

    getJson(
        uri: '/api/panier',
        headers: ['X-Cart-Token' => $this->cartToken],
    )
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertExactJson(['items' => []]);
});

it('shows cart contents', function () {
    $quantity = 2;

    postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $quantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response = getJson(
        uri: "/api/panier",
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonPath("items.{$this->product->id}.unit_price", $this->productSitePrice->price)
        ->assertJsonPath("items.{$this->product->id}.quantity", $quantity);
});

it('updates products quanities', function () {
    $existedQuantity = 2;
    $updatedQuantity = 5;

    $secondProduct = Product::create([
        'name' => 'Product B',
        'stock' => 30,
    ]);

    $secondProductSitePrice = ProductSitePrice::create([
        'product_id' => $secondProduct->id,
        'site_id' => $this->site->id,
        'price' => 35,
    ]);

    postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $existedQuantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    postJson(
        uri: "/api/panier/items/{$secondProduct->id}",
        data: ['quantity' => $existedQuantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response = putJson(
        uri: '/api/panier',
        data: [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $updatedQuantity,
                ],
                [
                    'product_id' => $secondProduct->id,
                    'quantity' => $updatedQuantity,
                ],
            ],
        ],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonPath("items.{$this->product->id}.unit_price", $this->productSitePrice->price)
        ->assertJsonPath("items.{$this->product->id}.quantity", $updatedQuantity)
        ->assertJsonPath("items.{$secondProduct->id}.unit_price", $secondProductSitePrice->price)
        ->assertJsonPath("items.{$secondProduct->id}.quantity", $updatedQuantity);
});

it('validates quanities before update', function () {
    $existedQuantity = 2;
    $invalidQuantity = 1000;

    postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $existedQuantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response = putJson(
        uri: '/api/panier',
        data: [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => $invalidQuantity,
                ],
            ],
        ],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonValidationErrors(['items.0.quantity']);

    getJson(
        uri: '/api/panier',
        headers: ['X-Cart-Token' => $this->cartToken],
    )
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonPath("items.{$this->product->id}.quantity", $existedQuantity);
});

it('removes product from cart', function () {
    $quantity = 2;

    postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $quantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response = deleteJson(
        uri: "/api/panier/items/{$this->product->id}",
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertExactJson([]);

    getJson(
        uri: '/api/panier',
        headers: ['X-Cart-Token' => $this->cartToken],
    )
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertJsonMissingPath("items.{$this->product->id}");
});

it('deletes cart after expiration', function () {
    config(['cart.cache_ttl' => 1]);

    $quantity = 2;

    postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $quantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    )
        ->assertStatus(Response::HTTP_CREATED)
        ->assertHeader('X-Cart-Token', $this->cartToken);

    $this->travel(2)->seconds();

    getJson(
        uri: '/api/panier',
        headers: ['X-Cart-Token' => $this->cartToken],
    )
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertExactJson(['items' => []]);
});

it('clears the cart', function () {
    $quantity = 2;

    postJson(
        uri: "/api/panier/items/{$this->product->id}",
        data: ['quantity' => $quantity],
        headers: ['X-Cart-Token' => $this->cartToken],
    );

    deleteJson(
        uri: '/api/panier',
        headers: ['X-Cart-Token' => $this->cartToken],
    )
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken);

    getJson(
        uri: '/api/panier',
        headers: ['X-Cart-Token' => $this->cartToken],
    )
        ->assertStatus(Response::HTTP_OK)
        ->assertHeader('X-Cart-Token', $this->cartToken)
        ->assertExactJson(['items' => []]);
});
