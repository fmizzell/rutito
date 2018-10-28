<?php

class RutitoTest extends \PHPUnit\Framework\TestCase {

  public function testSimple() {
    $router = new \Rutito\Simple();
    $router->addRoute("1", "hello/friend");

    $this->assertEquals("1", $router->route("hello/friend"));

    $this->assertFalse($router->route("goodbye/fiend"));
  }

  public function testDynamic() {
    $route = new \Rutito\Dynamic();
    $route->addRoute("1", "hello/%/friend");

    $match = $route->route("hello/g/friend");

    $this->assertEquals("1", $match[0]);
    $this->assertEquals(["g"], $match[1]);

    $match = $route->route("hello/56/friend");

    $this->assertEquals("1", $match[0]);
    $this->assertEquals(["56"], $match[1]);

    $this->assertFalse($route->route("goodbye"));

  }

}
