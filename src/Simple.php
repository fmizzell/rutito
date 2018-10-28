<?php

namespace Rutito;

class Simple implements IRouter {
  private $map = [];

  public function addRoute($id, $url_template) {
    $this->map[$url_template] = $id;
  }

  public function route($url_path) {
    $paths = array_keys($this->map);
    if (in_array($url_path, $paths)) {
      $id = $this->map[$url_path];
      return $id;
    }

    return FALSE;
  }

}