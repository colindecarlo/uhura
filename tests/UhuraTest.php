<?php

use Uhura\Uhura;

class UhuraTest extends PHPUnit_Framework_TestCase
{
    public $uhura;

    public function setUp()
    {
        parent::setUp();
        $this->uhura = new Uhura('http://localhost:' . getenv('TEST_SERVER_PORT'));
    }

    public function test_that_uhura_can_return_the_url_of_a_resource()
    {
        $this->assertEquals(
            'http://localhost:' . getenv('TEST_SERVER_PORT') . '/users/1',
            $this->uhura->users(1)->url()
        );
    }

    public function test_that_uhura_can_return_the_url_of_a_nested_resource()
    {
        $this->assertEquals(
            'http://localhost:' . getenv('TEST_SERVER_PORT') . '/users/1/blogs/my-blog',
            $this->uhura->users(1)->blogs('my-blog')->url()
        );
    }

    public function test_that_uhura_can_return_the_url_of_a_collection_of_resources()
    {
        $this->assertEquals(
            'http://localhost:' . getenv('TEST_SERVER_PORT') . '/users',
            $this->uhura->users->url()
        );
    }

    public function test_that_uhura_sends_a_get_request_for_the_correct_resource()
    {
        $response = $this->uhura->users(1)->get()->json();

        $this->assertEquals('GET', $response['method']);
        $this->assertEquals('users/1', $response['path']);
    }

    public function test_that_uhura_sends_a_post_reequest_with_the_correct_parameters_when_creating_resources()
    {
        $user = [
            'email' => 'test@example.com',
        ];

        $response = $this->uhura->users->create($user)->json();

        $this->assertEquals('POST', $response['method']);
        $this->assertEquals('users', $response['path']);
        $this->assertEquals($user, $response['form_params']);
    }

    public function test_that_uhura_sends_a_patch_request_with_the_correct_parameters_when_updating_a_resource()
    {
        $user = [
            'email' => 'test@example.com',
        ];

        $response = $this->uhura->users(1)->update($user)->json();

        $this->assertEquals('PATCH', $response['method']);
        $this->assertEquals('users/1', $response['path']);
        $this->assertEquals($user, $response['form_params']);
    }

    public function test_that_uhura_sends_a_put_request_with_the_correct_parameters_when_replacing_a_resource()
    {
        $user = [
            'email' => 'test@example.com',
        ];

        $response = $this->uhura->users(1)->replace($user)->json();

        $this->assertEquals('PUT', $response['method']);
        $this->assertEquals('users/1', $response['path']);
        $this->assertEquals($user, $response['form_params']);
    }

    public function test_that_uhura_sends_a_delete_request_to_the_correct_url_when_deleting_a_resource()
    {
        $response = $this->uhura->users(1)->delete()->json();

        $this->assertEquals('DELETE', $response['method']);
        $this->assertEquals('users/1', $response['path']);
    }

    public function test_that_uhura_sends_an_basic_authorization_header_when_sending_authenticated_requests_using_basic_authentication()
    {
        $this->uhura->useBasicAuthentication('username', 'some_token');

        $response = $this->uhura->users->get()->json();

        $this->assertArrayHasKey('authorization', $response['headers']);
        $this->assertEquals(
            sprintf('Basic %s', base64_encode('username:some_token')),
            $response['headers']['authorization'][0]
        );
    }

    public function test_that_uhura_returns_a_json_decode_response_body_when_using_the_json_response_handler()
    {
        $this->uhura->useResponseHandler(new \Uhura\ResponseHandler\Json);

        $expectedResponse = [
            'method' => 'GET',
            'path' => '/',
            'form_params' => [
                'foo' => 'bar'
            ]
        ];

        $this->assertArraySubset($expectedResponse, $this->uhura->get(['foo' => 'bar']));
    }

    public function test_that_uhura_can_attach_a_query_string_to_get_requests()
    {
        $response = $this->uhura->users->get(['foo' => 'bar'])->json();

        $this->assertEquals(['foo' => 'bar'], $response['form_params']);
    }

    public function test_that_uhura_respects_the_version_specifier_of_apis()
    {
        $this->uhura = new Uhura('http://localhost:' . getenv('TEST_SERVER_PORT') . '/v2');

        $response = $this->uhura->users(1)->blog->get()->json();

        $this->assertEquals('v2/users/1/blog', $response['path']);
    }
}
