<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckUserType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class CheckUserTypeMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function middleware_allows_access_for_correct_user_type()
    {
        $user = User::factory()->create(['user_type' => 'legal_professional']);
        $this->actingAs($user);

        $middleware = new CheckUserType();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Success');
        }, 'legal_professional');

        $this->assertEquals('Success', $response->getContent());
    }

    /** @test */
    public function middleware_denies_access_for_incorrect_user_type()
    {
        $user = User::factory()->create(['user_type' => 'pro_se']);
        $this->actingAs($user);

        $middleware = new CheckUserType();
        $request = Request::create('/test', 'GET');
        
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        
        $middleware->handle($request, function ($request) {
            return new Response('Success');
        }, 'legal_professional');
    }

    /** @test */
    public function middleware_redirects_unauthenticated_users_to_login()
    {
        $middleware = new CheckUserType();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Success');
        }, 'legal_professional');

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContainsString('login', $response->headers->get('Location'));
    }

    /** @test */
    public function middleware_works_with_pro_se_user_type()
    {
        $user = User::factory()->create(['user_type' => 'pro_se']);
        $this->actingAs($user);

        $middleware = new CheckUserType();
        $request = Request::create('/test', 'GET');
        
        $response = $middleware->handle($request, function ($request) {
            return new Response('Pro-Se Success');
        }, 'pro_se');

        $this->assertEquals('Pro-Se Success', $response->getContent());
    }
}
