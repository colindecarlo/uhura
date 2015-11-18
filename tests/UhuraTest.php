<?php

use Uhura\Uhura;

class UhuraTest extends PHPUnit_Framework_TestCase
{
    public $uhura;

    public function setUp()
    {
        $this->uhura = new Uhura('http://example.com');
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
}
