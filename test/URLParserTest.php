<?php declare(strict_types=1);

require_once dirname(__FILE__) . "/../src/UrlParser.php";

use PHPUnit\Framework\TestCase;

final class URLParserTest extends TestCase {
    private $parser;
    
    public function setUp() {
        $this->parser = new UrlParser();
    }

    public function testBasicPlaceholderDetection() {
        $tests = [
            "path",
            "to",
            "blog",
            "{id}"
        ];

        foreach($tests as $test) {
            $result = $this->parser->parseElement($test);
            $this->assertArrayHasKey("type", $result);
            $this->assertArrayHasKey("value", $result);

            if($test == "{id}") {
                $this->assertEquals("placeholder", $result["type"]);
            } else {
                $this->assertEquals("default", $result["type"]);
            }
        }
    }
}