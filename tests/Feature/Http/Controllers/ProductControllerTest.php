<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * @test
    */
    public function unauthenticated_users_cannot_access_following_endpoints_for_products() {
        $index = $this->json('GET', '/api/products');
        $index->assertStatus(401);

        $store = $this->json('POST', '/api/products');
        $store->assertStatus(401);

        $update = $this->json('PUT', '/api/products/-1');
        $update->assertStatus(401);

        $delete = $this->json('DELETE', '/api/products/-1');
        $delete->assertStatus(401);
    }

    /** 
     * @test 
     * */
    public function can_create_a_product()
    {
        //$product = factory('App\Product')->make(); //test

        $product = [
            'name' => "New Product",
            'description' => "This is a product",
            'units' => 20,
            'price' => 10,
            'image' => "https://images.pexels.com/photos/1000084/pexels-photo-1000084.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940"
        ];
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user, 'api')->json('POST', '/api/products', $product);
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);
        $response->assertJson(['message' => "Product Created!"]);
        $response->assertJson(['data' => $product]);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_products()
    {
        $products = factory(\App\Product::class, 20)->create();
        
        $user = factory(\App\User::class)->create();

        $response = $this->actingAs($user, 'api')->json('GET', '/api/products');

        $response->assertStatus(200);

        //$response->dump();

        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'units',
                        'price',
                        'image',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]
        );
    }

    /**
     * @test
     */
    public function can_update_a_product() {
        $product = factory(\App\Product::class)->create();

        $user = factory(\App\User::class)->create();
        $update = $this->actingAs($user, 'api')->json('PUT', '/api/products/' . $product->id, ['name' => "Changed for test"]);
        $update->assertStatus(200);
        $update->assertExactJson([
            'status' => true,
            'message' => "Product Updated!"
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Changed for test',
        ]);
    }

    /**
     * @test
     */
    public function can_delete_a_product() {
        $product = factory(\App\Product::class)->create();

        $user = factory(\App\User::class)->create();
        $update = $this->actingAs($user, 'api')->json('DELETE', '/api/products/' . $product->id);
        $update->assertStatus(200);
        $update->assertExactJson([
            'status' => true,
            'message' => "Product Deleted!"
            ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }    
}
