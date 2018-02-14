<?php declare(strict_types=1);

namespace Pirouette;

require_once __DIR__ . "/ControllerBase.php";

/**
 * Expose routing API. Developer needs only worry about methods present in this class for
 * general use.
 */
class Controller extends ControllerBase {
    /**
     * Handle the route before we leave. This ensures the user literally only
     * needs to implement an API method and that's it, rather than calling a method like run() etc...
     * It might be a bit questionable though.
     */
    public function __destruct() {
        parent::commence();
    }

    /**
     * Set up a route for GET requests.
     *
     * @param string   $path     the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     *
     * @return void
     */
    public function get(string $path, callable $callback) {
        parent::addListener($path, "GET", $callback);
    }

    /**
     * Set up a route for POST requests.
     *
     * @param string   $path     the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     *
     * @return void
     */
    public function post(string $path, callable $callback) {
        parent::addListener($path, "POST", $callback);
    }

    /**
     * Set up a route for PUT requests.
     *
     * @param string   $path     the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     *
     * @return void
     */
    public function put(string $path, callable $callback) {
        parent::addListener($path, "PUT", $callback);
    }

    /**
     * Set up a route for DELETE requests.
     *
     * @param string   $path     the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     *
     * @return void
     */
    public function delete(string $path, callable $callback) {
        parent::addListener($path, "DELETE", $callback);
    }
}
?>
