<?php declare(strict_types=1)
require_once(__DIR__ . '/URLPath.php');

class Route {
    final URLPath $path;

    # e.g. Route("/posts/$id"
    function __construct(string path) {


        $this->path = _parsePath(path);
    }

    function _parsePath(string path) {

    }

    # e.g. get(id=1)
    function get(mixed something) {
    }
}

?>