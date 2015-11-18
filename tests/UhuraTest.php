<?php

use Uhura\Uhura;

class UhuraTest extends PHPUnit_Framework_TestCase
{
    public function test_that_uhura_can_return_the_url_of_a_resource()
    {
        $uhura = new Uhura('http://example.com');
        $uhura->users(1);

        $this->assertEquals('http://example.com/users/1', $uhura->url());
    }

    public function test_that_uhura_can_return_the_url_of_a_nested_resource()
    {
        $uhura = new Uhura('http://example.com');
        $uhura->users(1)->blogs('my-blog');

        $this->assertEquals('http://example.com/users/1/blogs/my-blog', $uhura->url());
    }
}
