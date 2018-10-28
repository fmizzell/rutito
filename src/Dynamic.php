<?php

namespace Rutito;

use Timber\Node;

class Dynamic implements IRouter {

  const WILDCARD = "%";

  private $tree;

  public function __construct() {
    $this->tree = new Node("root");
  }

  public function addRoute($id, $url_template) {
    $pieces = explode("/", $url_template);

    $current = $this->tree;
    foreach ($pieces as $piece) {

      if (substr_count($piece, self::WILDCARD) > 0) {
        $piece = self::WILDCARD;
      }

      if (!empty($piece)) {
        $node = $current->searchChildren($piece);
        if (!$node) {
          $node = new Node($piece);
          $current->addChild($node);
        }
        $current = $node;
      }
    }
    $current->addChild(new Node(['id' => $id, 'url_template' => $url_template]));
  }

  public function route($url_path) {
    $pieces = explode("/", $url_path);

    $nodes = [$this->tree];
    foreach ($pieces as $piece) {
      if (!empty($piece)) {
        $matches = $this->matches($nodes, $piece);
        $matches = array_merge($matches, $this->matches($nodes, self::WILDCARD));
        $nodes = $matches;
      }
    }

    /* @var $node \Timber\Node */
    foreach ($nodes as $node) {
       /* @var $child \Timber\Node */
       foreach ($node->getChildren() as $child) {
         if ($child->isLeaf()) {
           $value = $child->getValue();
           $id = $value['id'];
           $arguments = $this->getArguments($value['url_template'], $url_path);

           return [$id, $arguments];
         }
      }
    }

    return FALSE;
  }

  private function getArguments($path, $current) {
    $arguments = [];

    $pieces1 = explode("/", $path);
    $pieces2 = explode("/", $current);

    foreach ($pieces1 as $key => $piece) {
      if (substr_count($piece, self::WILDCARD) > 0) {
        $arg_key = str_replace(self::WILDCARD, "", $piece);
        if (!empty($arg_key)) {
          $arguments[$arg_key] = $pieces2[$key];
        }
        else {
          $arguments[] = $pieces2[$key];
        }
      }
    }

    return !empty($arguments) ? $arguments : FALSE;
  }

  private function matches($nodes, $value) {
    $matches = [];
    /* @var $node \Timber\Node */
    foreach ($nodes as $node) {
      $child = $node->searchChildren($value);
      if ($child) {
        $matches[] = $child;
      }
    }
    return $matches;
  }

}
