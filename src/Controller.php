<?php declare(strict_types=1);

namespace Pirouette;

require_once(__DIR__ . "/Path.php");

class Controller {
    private $paths;

    public function __construct() {
        $this->paths = [];
    }

    /**
     * Given a supported HTTP verb, delegate a method to handle it, return the response from that method
     * to be passed back up.
     *
     * @param string $method the HTTP verb
     * @param string $path the path e.g. /api/post/34
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

    /**
     * Add a route to be listened for.
     * 
     * @param string $path the generalised path e.g. /blog/posts/{id}
     * @param string $method the HTTP verb e.g. GET
     * @param callable $callback the function to call on completion. Should accept an associative array as parameter.
     */
    private function _addListener(string $path, string $method, callable $callback) {
        if(!isset($this->paths[$path])) {
            $this->paths[$path] = [];
        }
        array_push($this->paths[$path], array("path" => new Path($path), # the generalised path for this fn
                                              "method" => $method, # the HTTP verb we want to associate this fn with
                                              "callback" => $callback)); # the fn to call
    }

    /**
     * Supply the given array with all the useful details from the request in a key called _params.
     *
     * @param array $urlKeywords the array whose ["_params"] key should be populated
     * @param string $method the HTTP method for this request, only used to test for PUT & POST.
     * @return $urlKeywords plus a populated ["_params"].
     */
    private function populateRequest(array $urlKeywords, string $method) {
        # obtain data from superglobals:
        if(isset($_GET) && count($_GET) > 0) {
            $urlKeywords["_params"] = $_GET;
        }
        if(isset($_POST) && count($_POST) > 0) {
            $urlKeywords["_params"] = $_POST;
        }
        if(isset($_FILES) && count($_FILES) > 0) {
            $urlKeywords["_files"] = $_FILES;
        }

        # obtain data from php://input
        if($method == "PUT") {
            try {
                parse_str(file_get_contents("php://input"), $put);
                if(isset($put) && count($put) > 0) {
                    $urlKeywords["_params"] = $put;
                }
            } catch(Exception $e) {
                error_log("Error trying to read parameters from put request: " . $e->getMessage());
            }
        }
        elseif($method == "POST") {
            try {
                $data = json_decode(file_get_contents("php://input"), true);
                if(isset($data) && count($data) > 0) {
                    $urlKeywords["_json"] = $data;
                }
            } catch (Exception $e) {
                error_log("Error trying to read json from php://input: " . $e->getMessage());
            }
        }

        return $urlKeywords;
    }

    /**
     * When a user tries to access a resource, try to find a generalised path that suits it.
     * For example, if $path is /blog/posts/345, try find a callback to call with the current HTTP verb who matches with or without placeholders.
     *
     * @param string $path the path e.g. /blog/posts/534
     * @param string $method the HTTP verb e.g. GET, POST, PUT, or DELETE.
     */
    public function router(string $path, string $method) {
        foreach($this->paths as $p) {
            foreach($p as $path_listener) {
                # perform no extra logic if this request is a different HTTP verb
                if($path_listener["method"] == $method) {
                    $matches = $path_listener["path"]->matchPath($path);

                    # if the current path can have a generalised path applied to it:
                    if(isset($matches)) {
                        $request = $this->populateRequest($matches, $method);
                        $result = $path_listener["callback"]($request);

                        # if the user made the anonymous callback return something, echo it to the client.
                        if(isset($result)) {
                            echo($result);
                        }
                    }
                }
            }
        }
    }

    /**
     * Handle the route before we leave. This ensures the user literally only
     * needs to implement an API method and that's it, rather than calling a method like run() etc...
     * It might be a bit questionable though.
     */
    public function __destruct() {
        $this->_commence();
    }

    /**
     * To be called (hopefully automatically in the future) once the user has declared all their routes.
     */
    private function _commence() {
        if(isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] != null && $_SERVER["REQUEST_URI"] != null) {
            $this->route($_SERVER["REQUEST_METHOD"], strtok($_SERVER["REQUEST_URI"], "?"));
        }
    }

    # user interface:
    # get("/blog/posts/{id}", function(array("id" => 23)) {
    #
    # -- logic using $kvs["id"] and so on here.
    #
    # });

    /**
     * Set up a route for GET requests.
     * 
     * @param string $path the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     */
    public function get(string $path, callable $callback) {
        $this->_addListener($path, "GET", $callback);
    }

    /**
     * Set up a route for POST requests.
     * 
     * @param string $path the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     */
    public function post(string $path, callable $callback) {
        $this->_addListener($path, "POST", $callback);
    }

    /**
     * Set up a route for PUT requests.
     * 
     * @param string $path the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     */
    public function put(string $path, callable $callback) {
        $this->_addListener($path, "PUT", $callback);
    }

    /**
     * Set up a route for DELETE requests.
     * 
     * @param string $path the generalised path e.g. /blog/posts/{id}
     * @param callable $callback what to do on this path, should accept associative array as parameter.
     */
    public function delete(string $path, callable $callback) {
        $this->_addListener($path, "DELETE", $callback);
    }
}
?>

