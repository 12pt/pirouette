<?php declare(strict_types=1);

require_once(__DIR__ . "/Path.php");

class Controller {
    private $paths;

    public function __construct() {
        $this->paths = [];
    }

    public function commence() {
        $this->route($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
    }

    /**
     * Given a supported HTTP verb, delegate a method to handle it, return the response from that method
     * to be passed back up.
     */
    private function route(string $method, string $path) {
        switch($method) {
        case "GET":
        case "POST":
        case "PUT":
        case "DELETE":
            global $cont;
            $this->router($path, $method);
            break;
        default:
            header("HTTP/1.1 405 Method Not Allowed");
            header("Allow: GET, POST, PUT, DELETE");
        }
    }

    public function _addListener(string $path, string $method, callable $callback) {
        if(!isset($this->paths[$path])) {
            $this->paths[$path] = [];
        }
        array_push($this->paths[$path], array("path" => new Path($path), "method" => $method, "callback" => $callback));
    }

    public function router(string $path, string $method) {
        foreach($this->paths as $p) {
            foreach($p as $path_listener) {
                if($path_listener["method"] == $method) {
                    $matches = $path_listener["path"]->matchPath($path);
                    if(isset($matches) && count($matches) > 0) {
                        $path_listener["callback"]($matches[0]);
                    }
                }
            }
        }
    }

    # user interface:
    # get("/blog/posts/{id}", function(array("id" => 23)) {
    #
    # -- logic using $kvs["id"] and so on here.
    #
    # });

    public function get(string $path, callable $callback) {
        $this->_addListener($path, "GET", $callback);
    }

    public function post(string $path, callable $callback) {
        $this->_addListener($path, "POST", $callback);
    }

    public function put(string $path, callable $callback) {
        $this->_addListener($path, "PUT", $callback);
    }

    public function delete(string $path, callable $callback) {
        $this->_addListener($path, "DELETE", $callback);
    }
}
?>

