<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $headers;

    const URI = "api/";

    public function setUp(): void
    {
        parent::setUp();
        $this->refreshDatabase();
        $this->headers = ['Accept' => 'application/json', 'Content-type' => 'application/json'];
        $this->headers = $this->transformHeadersToServerVars($this->headers);
    }


    /**
     * A basic test example.
     */
    public function test_user_can_register()
    {
        $body = ['name'=>'amir','email' => 'admin2@pars.com', 'password' => 'password'];
        $response = $this->call('POST', self::URI .'register', [], [], [], $this->headers, json_encode($body));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertArrayHasKey('access_token',$response->json());
    }


    /**
     * A basic test example.
     */
    public function test_user_can_login()
    {
        // first register user
        $body = ['name'=>'amir','email' => 'admin2@pars.com', 'password' => 'password'];
        $this->call('POST', self::URI .'register', [], [], [], $this->headers, json_encode($body));


        //now login with registered user
        $body = ['email' => 'admin2@pars.com', 'password' => 'password'];
        $response = $this->call('POST', self::URI .'login' , [], [], [], $this->headers, json_encode($body));


        $this->assertArrayHasKey('access_token',$response->json());
        $response->assertStatus(Response::HTTP_OK);
    }


    public function test_user_can_not_login_with_wrong_password()
    {
        // first register user
        $body = ['name'=>'amir','email' => 'admin2@pars.com', 'password' => 'password'];
        $this->call('POST', self::URI .'register', [], [], [], $this->headers, json_encode($body));


        //now login with registered user
        $body = ['email' => 'admin2@pars.com', 'password' => 'wrongpassword'];
        $response = $this->call('POST', self::URI .'login' , [], [], [], $this->headers, json_encode($body));


        $this->assertEquals($response->json()['message'], 'Wrong Data');
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


}