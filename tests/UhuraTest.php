<?php

use Uhura\Uhura;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class UhuraTest extends PHPUnit_Framework_TestCase
{
    public $uhura;

    public function setUp()
    {
        $this->uhura = Uhura::test('http://example.com');
    }

    public function test_that_uhura_can_return_the_url_of_a_resource()
    {
        $this->assertEquals(
            'http://example.com/users/1',
            $this->uhura->users(1)->url()
        );
    }

    public function test_that_uhura_can_return_the_url_of_a_nested_resource()
    {
        $this->assertEquals(
            'http://example.com/users/1/blogs/my-blog',
            $this->uhura->users(1)->blogs('my-blog')->url()
        );
    }

    public function test_that_uhura_can_return_the_url_of_a_collection_of_resources()
    {
        $this->assertEquals(
            'http://example.com/users',
            $this->uhura->users->url()
        );
    }

    public function test_that_uhura_sends_a_get_request_for_the_correct_resource()
    {
        $handler = $this->uhura->getHttp()->getConfig('handler');
        $handler->append(new Response);

        $this->uhura->users(1)->get();

        $this->assertEquals(0, $handler->count());
        $this->assertEquals('GET', $handler->getLastRequest()->getMethod());
        $this->assertEquals('http://example.com/users/1', $handler->getLastRequest()->getUri());
    }

    public function test_that_uhura_sends_a_post_reequest_with_the_correct_parameters_when_creating_resources()
    {
        $handler = $this->uhura->getHttp()->getConfig('handler');
        $handler->append(new Response);

        $user = [
            'email' => 'test@example.com',
        ];

        $this->uhura->users->create($user);

        $this->assertEquals(0, $handler->count());
        $this->assertEquals('POST', $handler->getLastRequest()->getMethod());
        $this->assertEquals('http://example.com/users', $handler->getLastRequest()->getUri());

        $params = [];
        parse_str($handler->getLastRequest()->getBody()->getContents(), $params);
        $this->assertEquals($user, $params);
    }

    public function test_that_uhura_sends_a_put_request_with_the_correct_parameters_when_updating_a_resource()
    {
        $handler = $this->uhura->getHttp()->getConfig('handler');
        $handler->append(new Response);

        $user = [
            'email' => 'test@example.com',
        ];

        $this->uhura->users(1)->update($user);

        $this->assertEquals(0, $handler->count());
        $this->assertEquals('PUT', $handler->getLastRequest()->getMethod());
        $this->assertEquals('http://example.com/users/1', $handler->getLastRequest()->getUri());

        $params = [];
        parse_str($handler->getLastRequest()->getBody()->getContents(), $params);
        $this->assertEquals($user, $params);
    }

    public function test_that_uhura_sends_a_delete_request_to_the_correct_url_when_deleting_a_resource()
    {
        $handler = $this->uhura->getHttp()->getConfig('handler');
        $handler->append(new Response);

        $this->uhura->users(1)->delete();

        $this->assertEquals(0, $handler->count());
        $this->assertEquals('DELETE', $handler->getLastRequest()->getMethod());
        $this->assertEquals('http://example.com/users/1', $handler->getLastRequest()->getUri());
    }

    public function test_that_uhura_sends_an_basic_authorization_header_when_sending_authenticated_requests_using_basic_authentication()
    {
        $handler = $this->uhura->getHttp()->getConfig('handler');
        $handler->append(new Response);

        $this->uhura->useBasicAuthentication('username', 'some_token');

        $this->uhura->users->get();

        $this->assertTrue($handler->getLastRequest()->hasHeader('Authorization'));
        $this->assertEquals(
            sprintf('Basic %s', base64_encode('username:some_token')),
            $handler->getLastRequest()->getHeader('Authorization')[0]
        );
    }

    public function test_that_uhura_returns_a_json_decode_response_body_when_using_the_json_response_handler()
    {
        $this->uhura->useResponseHandler(new \Uhura\ResponseHandler\Json);

        $expectedResponse = [
            'status' => 'ok',
            'data' => [
                'foo' => 'bar'
            ]
        ];

        $handler = $this->uhura->getHttp()->getConfig('handler');
        $handler->append(new Response(200, [], json_encode($expectedResponse)));

        $this->assertEquals($this->uhura->get(), $expectedResponse);
    }
}
