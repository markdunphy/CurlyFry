<?php namespace markdunphy\CurlyFry;

class CurlyFry {

	/**
	 * The URL to access
	 *
	 * @access private
	 * @var string
	 */
	private $url = NULL;

	/**
	 * The data to send with the request
	 *
	 * @access private
	 * @var array Associative array
	 */
	private $data = array();

	/**
	 * The info for the last request that was sent.
	 *
	 * @access private
	 * @var object Contains properties 'response' and 'details'
	 */
	private $request;

	/**
	 * A set of cURL options
	 *
	 * @access private
	 * @var array
	 */
	private $options = array();

	/**
	 * An array of queue'd curl handlers to be
	 * executed in parallel
	 *
	 * @access private
	 * @var array
	 */
	private $queue = array();

	private $defaults = array(

		'GET' => array(
			CURLOPT_HTTPGET    	   => 1,
			CURLOPT_URL 	       => NULL,
			CURLOPT_RETURNTRANSFER => 1
		),

		'POST' => array(
			CURLOPT_URL  	   	   => NULL,
			CURLOPT_POST 	   	   => NULL,
			CURLOPT_POSTFIELDS 	   => NULL,
			CURLOPT_RETURNTRANSFER => 1
		),

		'PUT' => array(
			CURLOPT_URL  	   	   => NULL,
			CURLOPT_POSTFIELDS 	   => NULL,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_CUSTOMREQUEST  => NULL
		),

		'DELETE' => array(
			CURLOPT_URL  	   	   => NULL,
			CURLOPT_POSTFIELDS 	   => NULL,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_CUSTOMREQUEST  => NULL
		)
	);

	/**
	 * Constructor method
	 *
	 * @access public
	 * @param string $url The URL to access
	 * @param array $data Associative array of data to send with the request
	 */
	public function __construct( $url = NULL, $data = array() )
	{
		// Use the provided settings to update options and such
		$this->setURL( $url );
		$this->setData( $data );

		// Set up request object
		$this->request->response = NULL;
		$this->request->details  = NULL;
		$this->request->error    = NULL;
	}

	/**
	 * Provide an option to call the class statically
	 *
	 * @access public
	 * @param string $url The URL to access
	 * @param array $data Associative array of data to send with the request
	 * @return markdunphy\CurlyFries
	 */
	public static function create( $url, $data = array() )
	{
		return new static( $url, $data );
	}

	/**
	 * Queue a new request to be executed in parallel
	 *
	 * @param string $url
	 * @param string $name the property name the response should be set to
	 * @param string $type the type of request.
	 */
	public function queue( $url, $name, $type = 'GET' )
	{
		// Build a request object with some information
		$request 	   = (object) array();
		$request->url  = $url;
		$request->type = strtolower( $type );

		// Add it to the queue
		$this->queue[ $name ] = $request;
	}

	/**
	 * Execute a GET request
	 *
	 * @access public
	 * @return string
	 */
	public function get()
	{
		$options = $this->getOptions( 'GET' );

		$options[ CURLOPT_URL ]  = $this->url;
		$options[ CURLOPT_URL ] .= $this->data ? $this->queryString( 'GET' ) : '';

		$this->setOptions( $options );

		return $this->execute();
	}

	/**
	 * Execute a POST request
	 *
	 * @access public
	 * @return string
	 */
	public function post()
	{
		// Set up POST options
		$options = $this->getOptions( 'POST' );

		$options[ CURLOPT_URL ]  	   = $this->url;
		$options[ CURLOPT_POST ] 	   = count( $this->data );
		$options[ CURLOPT_POSTFIELDS ] = $this->queryString( 'POST' );

		$this->setOptions( $options );

		return $this->execute();
	}

	/**
	 * Execute a PUT request
	 *
	 * @access public
	 * @return string
	 */
	public function put()
	{
		// Set up PUT options
		$options = $this->getOptions( 'PUT' );

		$options[ CURLOPT_URL ]  	   = $this->url;
		$options[ CURLOPT_POSTFIELDS ] = $this->queryString( 'PUT' );

		$this->setOptions( $options );

		return $this->execute();
	}

	/**
	 * Execute a DELETE request
	 *
	 * @access public
	 * @return string
	 */
	public function delete()
	{
		// Set up PUT options
		$options = $this->getOptions( 'DELETE' );

		$options[ CURLOPT_URL ] 	   = $this->url;
		$options[ CURLOPT_POSTFIELDS ] = $this->queryString( 'DELETE' );

		$this->setOptions( $options );

		return $this->execute();
	}

	/**
	 * Execute a cURL request and set up response, details, and error
	 * information.
	 *
	 * @access private
	 * @return string response
	 */
	private function execute()
	{
		$ch = $this->handler();  // Retrieve a curl handler with options set.

		$this->request->response = curl_exec( $ch );
		$this->request->details  = curl_getinfo( $ch );
		$this->request->error    = curl_error( $ch );

		curl_close( $ch );

		return $this->request->response;
	}

	/**
	 * Set custom options
	 *
	 * @access public
	 * @param array $options
	 * @return markdunphy\CurlyFries
	 */
	private function setOptions( $options = array() )
	{
		$this->options = $options;

		return $this;
	}

	private function getOptions( $type )
	{
		return $this->defaults[ $type ];
	}

	/**
	 * Set URL class property and update relevant curl options
	 *
	 * @access public
	 * @param url $string
	 * @return markdunphy\CurlyFries
	 */
	public function setURL( $url = NULL )
	{
		$this->url = $url;

		return $this;
	}

	/**
	 * Set data class property and update relevant curl options
	 *
	 * @access public
	 * @param array $data
	 * @return markdunphy\CurlyFries
	 */
	public function setData( $data = array() )
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Debug method.
	 *
	 * @access public
	 * @return array Response from curl_getinfo
	 */
	public function debug()
	{
		return $this->request->details;
	}

	/**
	 * Getter method for the last error generated by cURL
	 *
	 * @access public
	 * @return string
	 */
	public function error()
	{
		return $this->request->error;
	}

	/**
	 * Return a query string
	 *
	 * @param string $type type of request (GET, POST, PUT, DELETE)
	 * @return string
	 */
	private function queryString( $type )
	{
		$query = http_build_query( $this->data );

		return ( $type == 'GET' ) ? '?' . $query : $query;
	}

	/**
	 * Get a cURL handler resource
	 *
	 * @access private
	 * @return resource
	 */
	private function handler()
	{
		$ch = curl_init();
		curl_setopt_array( $ch, $this->options );
		// d( $this->options );
		return $ch;
	}
}