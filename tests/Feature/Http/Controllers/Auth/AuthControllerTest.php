<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void{
        parent::setUp();
        
        $this->artisan('passport:install');
        // \Artisan::call('passport:install');
    }

    /** @test */
    public function can_authenticate() {

        // $clients = \DB::table('oauth_clients')->get();
        // dd($clients);

        $user = factory(User::class)->create();

        $response = $this->json('POST', '/auth/token', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
        ->assertJsonStructure(['token']);
    }

}