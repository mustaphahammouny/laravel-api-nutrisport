<?php

use App\Models\Product;
use App\Models\ProductSitePrice;
use App\Models\Site;
use Symfony\Component\HttpFoundation\Response;

use function Pest\Laravel\get;
use function Pest\Laravel\getJson;

beforeEach(function () {
    $this->site = Site::create([
        'code' => 'test',
        'name' => 'Test',
        'domain' => 'localhost',
    ]);

    $this->product = Product::create([
        'name' => 'Whey Protein',
        'stock' => 40,
    ]);

    ProductSitePrice::create([
        'product_id' => $this->product->id,
        'site_id' => $this->site->id,
        'price' => 75.5,
    ]);
});

it('returns products as json by default', function () {
    getJson('/api/produits')
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonPath('data.0.id', $this->product->id)
        ->assertJsonPath('data.0.name', $this->product->name)
        ->assertJsonPath('data.0.price', '75.50');
});

it('returns a product as xml when format is xml', function () {
    $response = get("/api/produits/{$this->product->id}?format=xml");

    $response->assertStatus(Response::HTTP_OK);
    expect($response->headers->get('Content-Type'))->toContain('application/xml');

    $xml = simplexml_load_string($response->getContent());

    expect($xml)->not->toBeFalse();
    expect((string) $xml->data->id)->toBe((string) $this->product->id);
    expect((string) $xml->data->name)->toBe($this->product->name);
    expect((string) $xml->data->price)->toBe('75.50');
});

it('validates unsupported formats', function () {
    getJson('/api/produits?format=yaml')
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['format']);
});
