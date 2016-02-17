## OAuth2 Resource Authorization

This package helps to provide a method of integrating a resource server to an authentication server to validate access attempts, and is meant as a resource-side complement to [lucadegasperi/oauth2-server-laravel](https://github.com/lucadegasperi/oauth2-server-laravel).

The benefit of this package is that it can be used to (<sub>almost</sub>) completely decouple the resource server from the authentication server.  The resource server will still require an `App\User` model and will need to be aware of how to connect to the authentication server's database.

This package is released under the [MIT License](LICENSE)

### Installation

Add to `config/app.php':

```php
'providers' => [
    // ...
    LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class,
    Jeffsrepoaccount\ResourceAuthorization\ServiceProvider::class,
]
```

Add to `app/Http/Kernal.php`:

```php
'oauth_access' => \Jeffsrepoaccount\ResourceAuthorization\Middleware::class,
```

### Usage

```php
// app/Http/routes.php

Route::group(['middleware   ' => 'oauth_access'], function() {
   Route::get('/api/v1/resources', 'MyApiController@indexRoute'); 
});
```

```php
use Illuminate\Http\Request;

class MyApiController
{
    public function indexRoute(Request $request)
    {
        $user = $request->user();

        $this->respondWithUserResource($user->resource);
    }
}
```

If you want to keep everything about your user accounts in a different database so that the only tables your resource database contains are resources, change the `$connection` property on the `App\User` model and add the connection details to `config/database.php': 


```php
<?php namespace App;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    protected $connection = 'accounts';    
}
```

``php
// config/database.php
return [
    'connections' => [
        'accounts' => [
            'driver'    => 'mysql',
            'host'      => env('ACCOUNTS_DB_HOST',      ''),
            'database'  => env('ACCOUNTS_DB_DATABASE',  ''),
            'username'  => env('ACCOUNTS_DB_USERNAME',  ''),
            'password'  => env('ACCOUNTS_DB_PASSWORD',  ''),
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
            'strict'    => false,
        ],
    ],
];
```

Lastly, create a `config/oauth2.php` file (because you don't want to publish this package on your resource server since it will also publish all of the migrations) and add the following contents:

```php
<?php

return [
    'database' => 'accounts',
];
```

### Authorization vs Access Control

This package _authorizes_ requests by access token handed to the resource server and supplies each request with the user instance that currently owns a valid access token.  This package does not verify scope level access to resources since that is application specific.