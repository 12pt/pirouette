<?php declare(strict_types=1);

require_once dirname(__FILE__) . "/../src/Path.php";

use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase {
    private $path;

    public function setUp() {
        $this->path = new Path("/blog/post/{id}");
    }

    public function testRouteMatches() {
        $tests = [
            "/blog/post/23",
            "/blog/post/65/",
            "/blog/post/hello",
            # misspelled / unrelated routes
            "/blag/post/24",
            "/blog/posts/87",
            "/blog/post/54/extra",

            "blog/post/55",
            "blog/post/que/",
        ];

        $results = [];

        foreach($tests as $test) {
            $result = $this->path->matchPath($test);
            array_push($results, $result);
        }

        # first 3 should match
        $this->assertArrayHasKey("id", $results[0][0]);
        $this->assertArrayHasKey("id", $results[1][0]);
        $this->assertArrayHasKey("id", $results[2][0]);

        # next 3 should NOT match the routes.
        $this->assertEmpty($results[3]);
        $this->assertEmpty($results[4]);
        $this->assertEmpty($results[5]);

        # should match paths without preceeding /
        $this->assertArrayHasKey("id", $results[6][0]);
        $this->assertArrayHasKey("id", $results[7][0]);
    }
}