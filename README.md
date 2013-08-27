CurlyFry
==========

A deliciously elegant PHP cURL wrapper.

The auto-json-parsing, life awesomizing, crunchy, crispy, salty, MIT-licensed library of choice for all your cURL-y needs.

###Documentation

CurlyFry provides an easy way to make RESTful calls with support for GET, POST, PUT, and DELETE methods.
```php
// Instantiate a new CurlyFry instance
$salty = new CurlyFry( 'http://www.example.com' ); // Normally
$salty = CurlyFry::create( 'http://www.example.com' ); // Statically

// Execute a GET request
$response = $salty->get();

// Execute a POST request
$response = $salty->post();

// Execute a PUT request
$response = $salty->put();

// Execute a DELETE request
$response = $salty->delete();
```

Obviously, you would need to send data with your request in many cases. Just pass an associative array in as the second parameter to the constructor.
```php
$data = array(
	'user_id' => 1234
);

$salty = new CurlyFry( 'http://www.example.com', $data );

$response = $salty->post();
```

You're also able to create and send your request in static context with one line:
```php
$response = CurlyFry::create( 'http://www.example.com', $data )->get();
$response = CurlyFry::create( 'http://www.example.com', $data )->put();
$response = CurlyFry::create( 'http://www.example.com', $data )->post();
$response = CurlyFry::create( 'http://www.example.com', $data )->delete();
```
Or you can do this which is a lot like the above code, but shorter:
```php
$response = CurlyFry::get( 'http://www.example.com', $data );
$response = CurlyFry::put( 'http://www.example.com', $data );
$response = CurlyFry::post( 'http://www.example.com', $data );
$response = CurlyFry::delete( 'http://www.example.com', $data );
```
So you're getting JSON back? That's cool, we'll parse that for you.
```php
$response = CurlyFry::get( 'http://www.example.com/myjson' );
print $response->whatever; // HOLY MOTHER OF POSEIDON IT WORKS
```
Things not going as planned?  Debug it! CurlyFry stores the details and errors of the last request you made.
```php
$salty    = new CurlyFry( 'http://www.example.com' );
$response = $salty->get(); // Oh no! The call bombed! Why?!?!?!

// Dump out the debug results
var_dump( $salty->debug() ); // Basically just curl_getinfo()
var_dump( $salty->error() ); // Basically just curl_error();
```


###Currently in the Fryer
* Parallel requests using curl_multi_exec
* Other things

###License
The MIT License (MIT)

Copyright (c) 2013 Mark Dunphy

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.


