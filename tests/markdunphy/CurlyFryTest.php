<?php

use markdunphy\CurlyFry;

class CurlyFryTest extends PHPUnit_Framework_TestCase {

  /**
   * @dataProvider constructorArguments
   */
  public function testConstruct( $url, $data ) {

    $fry = new CurlyFry( $url, $data );

    $this->assertEquals( $data, $fry->getData() );
    $this->assertEquals( $url, $fry->getUrl() );

  } // testConstruct

  /**
   * @dataProvider constructorArguments
   */
  public function testCreate( $url, $data ) {

    $fry = CurlyFry::create( $url, $data );

    $this->assertEquals( $data, $fry->getData() );
    $this->assertEquals( $url, $fry->getUrl() );

  } // testCreate

  public function constructorArguments() {

    return [
        [ 'http://www.google.com', [ 'hello' => 'world' ] ],
    ];

  } // constructorArguments

} // CurlyFryTest
