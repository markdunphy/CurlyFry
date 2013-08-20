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

	/**
	 * Allowed types
	 *
	 * @access private
	 * @var array
	 */
	private $types = array( 'GET', 'PUT', 'POST', 'DELETE' );

	/**
	 * Default arrays of cURL options
	 *
	 * @access private
	 * @var array
	 */
	private $defaults = array();

	/**
	 * Constructor method
	 *
	 * @access public
	 * @param string $url The URL to access
	 * @param array $data Associative array of data to send with the request
	 */
	public function __construct( $url = NULL, $data = array() )
	{
		// Set the default cURL options
		$this->setDefaultOptions();

		// Use the provided settings to update options and such
		$this->setURL( $url );
		$this->setData( $data );

		// Set up request object
		$this->request->response = NULL;
		$this->request->details  = NULL;
		$this->request->error    = NULL;
	}

	/**
	 * Dynamically execute an HTTP request
	 * based on the method name (GET/POST/PUT/DELETE).
	 * 
	 * @param string $name method name
	 * @param array $arguments method arguments
	 * @return mixed
	 */
	public function __call( $name, $arguments )
	{
		$name = strtoupper( $name );

		if ( count( $arguments ) > 0 )
		{
			$this->setURL( $arguments[0] );
			@$this->setData( $arguments[1] );
		}


		if ( in_array( $name, $this->types ) )
		{
			$this->prepare( $name );

			return $this->execute();
		}

		return FALSE;
	}

	/**
	 * Call stuff statically because why the hell not.
	 *
	 * @return mixed
	 */
	public static function __callStatic( $name, $arguments )
	{
		$arguments[1] = isset( $arguments[1] ) ? $arguments[1] : array();

		$salty = new static( $arguments[0], $arguments[1] );

		return $salty->$name();
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
	 * Prepare the cURL opts for execution
	 *
	 * @param string $type type of the request.
	 */
	private function prepare( $type )
	{
		$options = $this->getOptions( strtoupper( $type ) );

		$options[ CURLOPT_URL ] = $this->url;

		$query = $this->queryString( $type );

		switch ( $type)
		{
			case 'GET':
				$options[ CURLOPT_URL ] .= $this->data ? $query : '';
				break;

			case 'POST':
				$options[ CURLOPT_POST ] 	   = 1;
				$options[ CURLOPT_POSTFIELDS ] = $query;
				break;

			case 'PUT' || 'DELETE':
				$options[ CURLOPT_POSTFIELDS ]   = $query;
				$options[ CURLOPT_CUSTOMREQUEST ] = $type;
				break;
		}

		$this->setOptions( $options );
	}

	/**
	 * Execute a cURL request and set up response, details, and error
	 * information.
	 *
	 * @access private
	 * @return mixed object/array if response is json, string otherwise
	 */
	private function execute()
	{
		$ch = $this->handler();  // Retrieve a curl handler with options set.

		$this->request->response = curl_exec( $ch );
		$this->request->details  = curl_getinfo( $ch );
		$this->request->error    = curl_error( $ch );

		curl_close( $ch );

		return ( $parsed = json_decode( $this->request->response ) ) ? $parsed : $this->request->response;
	}

	/**
	 * ** NOT YET IMPLEMENTED **
	 * Queue a new request to be executed in parallel.
	 *
	 * @param string $url
	 * @param string $name the property name the response should be set to
	 * @param string $type the type of request.
	 */
	public function queue( $url, $name, $type = 'GET' )
	{
		// Build a request object with some information
		$request = (object) array(
			'url'  => $url,
			'type' => strtolower( $type )
		);

		// Add it to the queue
		$this->queue[ $name ] = $request;
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

	/**
	 * Return a template of default options based on type
	 *
	 * @return array
	 */
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
		$query = $this->data ? http_build_query( $this->data ) : '';

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

		return $ch;
	}

	/**
	 * Set the default cURL options
	 */
	private function setDefaultOptions()
	{
		$this->defaults = array(

			'GET' => array(
				CURLOPT_HTTPGET    	   => 1,
				CURLOPT_URL 	       => NULL,
				CURLOPT_RETURNTRANSFER => TRUE
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
	}
}