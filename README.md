CurlyFry
==========

A deliciously elegant PHP cURL wrapper.

This is currently untested. Literally. Like I haven't tested any of this code formally or informally.  I just wrote it.  If you manage to get a hold of this before I get to test it later today, I implore you to let me know how it goes.

Don't let the tests folder fool you. It is empty.

###Documentation

CurlyFry provides an easy way to make RESTful calls with support for GET, POST, PUT, and DELETE methods.
```php
	// Instantiate a new CurlyFry instance
	$salty = new CurlyFry( 'http://www.google.com' );

	// Execute a GET request
	$response = $salty->get();

	// Execute a POST request
	$response = $salty->post();

	// Execute a PUT request
	$response = $salty->put();

	// Execute a DELETE request
	$response = $salty->delete();
```

Obviously, you would need to send data with your request in a lot of cases. Just pass an associative array in as the second parameter to the constructor.
```php
	$data = array(
		'user_id' => 1234
	);

	$salty = new CurlyFry( 'http://www.google.com', $data );

	$response = $salty->post();
```

You're also able to create and send your request in static context with one line.
```php
	$response = CurlyFry::create( 'http://www.google.com', $data )->get();
	$response = CurlyFry::create( 'http://www.google.com', $data )->put();
	$response = CurlyFry::create( 'http://www.google.com', $data )->post();
	$response = CurlyFry::create( 'http://www.google.com', $data )->delete();
```

