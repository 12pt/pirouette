<?php declare(strict_types=1);

require_once dirname(__FILE__) . "/../src/Controller.php";

use PHPUnit\Framework\TestCase;
use Pirouette\Controller;

final class ControllerTest extends TestCase {
    private $_cont;

    public function setUp() {
        $this->_cont = new Controller();

    }

    public function testGet() {
        $this->_cont->get("/posts/{id}", function($params) {
            $this->assertEquals("42", $params["id"]);
            print "hello! " . $params["id"];
        });

        $this->expectOutputString("hello! 42");
        $this->_cont->router("/posts/42", "GET");
    }
        
    // can't test POST other than what is already covered by GET

    public function testPost() {
        $this->_cont->post("/posts", function($params) {
            print "hello from post!";
        });
        $this->expectOutputString("hello from post!");
        $this->_cont->router("/posts/", "POST");
    }

    // same with PUT really

    public function testPut() {
        $this->_cont->put("/posts/{num}", function($params) {
            $this->assertEquals("65", $params["num"]);
            print "hello from put! " . $params["num"];
        });
        $this->expectOutputString("hello from put! 65");
        $this->_cont->router("/posts/65", "PUT");
    }

    public function testDelete() {
        $this->_cont->delete("/posts/{index}/{category}", function($params) {
            print "deleting " . $params["index"] . "'s category: " . $params["category"];
        });

        $this->expectOutputString("deleting 76's category: moon facts");
        $this->_cont->router("/posts/76/moon facts", "DELETE");
    }
}

?>