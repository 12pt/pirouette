<?php declare(strict_types=1);

namespace Pirouette;

require_once __DIR__ . "/Path.php";

/**
 * Handle the storage of paths and their related HTTP verbs and callback functions.
 */
abstract class ControllerBase {
    private $_paths;

    /**
     * Called automatically once the user has declared all their routes, this "kicks off" the
     * routing.
     *
     * @return void
     */
    protected function commence() {
        if (isset($_SERVER["REQUEST_METHOD"]) && isset($_SERVER["REQUEST_URI"])) {
            $this->_route($_SERVER["REQUEST_METHOD"], strtok($_SERVER["REQUEST_URI"], "?"));
        }
    }

    /**
     * Given a supported HTTP verb, delegate a method to handle it, return the response from that method
     * to be passed back up. This acts as a filter for unsupported HTTP verbs (currently OPTIONS).
     *
     * @param string $method the HTTP verb
     * @param string $path   the path e.g. /api/post/34
     *
     * @return void
     */
    private function _route(string $method, string $path) {
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

    /**
     * When a user tries to access a resource, try to find a generalised path that suits it.
     * For example, if $path is /blog/posts/345, try find a callback to call with the current
     * HTTP verb who matches with or without placeholders.
     *
     * @param string $path   the path e.g. /blog/posts/534
     * @param string $method the HTTP verb e.g. GET, POST, PUT, or DELETE.
     *
     * @return void
     */
    public function router(string $path, string $method) {
        foreach ($this->_paths as $p) {
            foreach ($p as $path_listener) {
                // perform no extra logic if this request is a different HTTP verb
                if ($path_listener["method"] == $method) {
                    $matches = $path_listener["path"]->matchPath($path);

                    // if the current path can have a generalised path applied to it:
                    if (isset($matches)) {
                        $request = $this->_populateRequest($matches, $method);
                        $result = $path_listener["callback"]($request);

                        // if something is returned, echo it to the client.
                        if (isset($result)) {
                            echo($result);
                        }
                    }
                }
            }
        }
    }

    /**
     * Add a route to be listened for.
     *
     * @param string   $path     the generalised path e.g. /blog/posts/{id}
     * @param string   $method   the HTTP verb e.g. GET
     * @param callable $callback the function to call on completion. Should accept an associative array as parameter.
     *
     * @return void
     */
    protected function addListener(string $path, string $method, callable $callback) {
        if (!isset($this->_paths[$path])) {
            $this->_paths[$path] = [];
        }
        array_push($this->_paths[$path], array("path" => new Path($path),
                                              "method" => $method,
                                              "callback" => $callback));
    }

    /**
     * Supply the given array with all the useful details from the request in a key called _params.
     *
     * @param array  $urlKeywords the array whose ["_params"] key should be populated
     * @param string $method      the HTTP method for this request
     *
     * @return $urlKeywords plus a populated ["_params"].
     */
    private function _populateRequest(array $urlKeywords, string $method) {
        // obtain data from superglobals:
        if (isset($_GET) && count($_GET) > 0) {
            $urlKeywords["_params"] = $_GET;
        }
        if (isset($_POST) && count($_POST) > 0) {
            $urlKeywords["_params"] = $_POST;
        }
        if (isset($_FILES) && count($_FILES) > 0) {
            $urlKeywords["_files"] = $_FILES;
        }

        // obtain data from php://input
        if ($method == "PUT") {
            try {
                parse_str(file_get_contents("php://input"), $put);
                if (isset($put) && count($put) > 0) {
                    $urlKeywords["_params"] = $put;
                }
            } catch(Exception $e) {
                error_log("Error trying to read parameters from put request: " . $e->getMessage());
            }
        } elseif ($method == "POST") {
            try {
                $data = json_decode(file_get_contents("php://input"), true);
                if (isset($data) && count($data) > 0) {
                    $urlKeywords["_json"] = $data;
                }
            } catch (Exception $e) {
                error_log("Error trying to read json from php://input: " . $e->getMessage());
            }
        }

        return $urlKeywords;
    }
}
?>
