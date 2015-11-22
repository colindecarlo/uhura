#Uhura

A communications officer for RESTful APIs

Uhura is a dead simple RESTful API client for just about anything. No need to set up schemas or
configure API endpoints, just tell Uhura what you want and go get it.

```php
$github = new Uhura('https://api.github.com');
$response = $github->users->colindecarlo->repos->get();
```

##Installation

Install Uhura using composer.

```bash
$ composer require uhura/uhura
```

##Making Requests

Uhura maps what you ask for in your Demeter chain over to the URL that is used to access the
resource you want.

####Examples

**Send a GET request to `http://someapi.com/users`**

```php
$uhura = new Uhura('http://someapi.com');
$response = $uhura->users->get();
```

**Send a GET request to `http://someapi.com/users/1`**

```php
$uhura = new Uhura('http://someapi.com');
$response = $uhura->users(1)->get();
```

**Send a GET request to `http://someapi.com/users/1/blogs/some-blog/comments`**

```php
$uhura = new Uhura('http://someapi.com');
$response = $uhura->users(1)-blogs('some-blog')->comments->get();
```
###CRUD

CRUD operations are super simple with Uhura and are mapped to the `create`, `get`, `update` and
`delete` methods respectively.

Operation | Method Signature
----------|-----------------
Create | `create($payload)`
Read | `get()`
Update | `update($payload)`
Delete | `delete()`

**`create(array $payload)`**

Use Uhura's `create` method to create resources. The `create` method accepts an associative array
of attributes which are sent to the API in the request body as a `x-www-form-urlencoded` string. 

```php
$uhura = new Uhura('http://someapi.com');
$uhura->users->create(['email' => 'example@example.com']);
```

**`get()`**

Use Uhura's `get` method to get API resources.

```php
$uhura = new Uhura('http://someapi.com');
$response = $uhura->users->get();
```

**`update($payload)`**

Use Uhura's `update` method to update a resource. The `update` method accepts an associative array
of attributes which are sent to the API in the request body as a `x-www-form-urlencoded` string. 

```php
$uhura = new Uhura('http://someapi.com');
$uhura->users(1)->update(['name' => 'John Doe']);
```

**`delete()`**

Use Uhura's `delete` method to delete a resource.

```php
$uhura = new Uhura('http://someapi.com');
$uhura->users(1)->delete();
```

###Authentication

Uhura makes authenticated requests by adding the `Authorization` header to each request that is
made.

**Using HTTP Basic Auth**

Tell Uhura to use HTTP Basic Auth with the `useBasicAuthentication($username, $password)` method.

```php
$uhura = new Uhura('https://someapi.com');
$uhura->useBasicAuthentication('someuser', 'somepassword');

$uhura->user->update(['email' => 'example@example.com']);
```

**Explicitly Setting the Authorization Header**

You can explicitly set the value of the `Authorization` header by using Uhura's
`authenticate($token)` method.

```php
$uhura = new Uhura('https://someapi.com');
$uhura->authenticate('Bearer somebearertoken');

$uhura->user->update(['email' => 'example@example.com']);
```

##Working With Responses

By default, Uhura returns [PSR7 compliant](http://www.php-fig.org/psr/psr-7) response objects.
Working with them would be as simple as, oh I don't know, a `GuzzleHttp\Psr7\Response` object.

###Response Handlers

You can tell Uhura to pass API responses through a Response Handler to augment the return value of
the various request methods. For instance, Uhura ships with a `Json` Response Handler which consumes
the response and returns the decoded JSON response body.

```php
$uhura = new Uhura('https://someapi.com');
$uhura->useResponseHandler(new Uhura\ResponseHandler\Json);

$uhura->users(1)->get();
/*
    [
        'email' => 'example@example.com',
        'name' => 'John Doe'
    ]
*/
```

**Writing Custom Response Handlers**

Writing your own custom response handler is super simple. Response Handlers are just simple classes
which define a `handle($response)` method. Whatever is returned from the `handle` method is what
Uhura will return to you.

```php
// XML Response Handler
class XmlHandler
{
    public function handle($response)
    {
        return new SimpleXMLElement($response->getBody()->getContents());
    }
}


$uhura = new Uhura('https://someapi.com');
$uhura->useResponseHandler(new XmlHandler);

echo (string)($uhura->users(1)->get());

/*
    <?xml version='1.0' standalone='yes'?>
    <user>
        <email>example@example.com</email>
        <name>John Doe</name>
    </user>
*/
```
