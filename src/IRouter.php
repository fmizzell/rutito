<?php

namespace Rutito;

interface IRouter {
  public function addRoute($id, $url_template);
  public function route($url_path);
}