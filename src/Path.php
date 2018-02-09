<?php declare(strict_types=1);

final class Path {
    private $path;

    /**
     * Initialise the Path object.
     * @param string $targetPath the path (+ optional placeholders) to match actual paths against
     */
    public function __construct(string $targetPath) {
        $parser = new PathParser();
        $this->path = [];
        # path can contain regular ol' words
        # [NYI] path can contain regex 
        # path can contain placeholders e.g. {id}

        $path = Path::explodeTrim($targetPath);
        foreach($path as $element) {
            array_push($this->path, $parser->parseElement($element));
        }
    }

    /**
     * Explode the path into an array of strings. Pop off the final element
     * if it is null (for example the path ends in a slash), and then pop
     * off the first element if it's null.
     *
     * @param string $path the path.
     */
    public static function explodeTrim(string $path) {
        $ar = explode("/", $path);
        if(count($ar) > 0) {
            if($ar[count($ar) - 1] == null) {
                array_pop($ar);
            }
            if($ar[0] == null) {
                array_shift($ar);
            }
        }
        return $ar;
    }

    /**
     * Match a given path e.g. /blog/post/34 to a template path /blog/post/{id}
     *
     * @return an associative array binding ids to values if the path matches (or an empty array
     * if it matches and no placeholders were used) or null if it didnt match.
     */
    public function matchPath(string $targetPath) {
        $path = Path::explodeTrim($targetPath);
        $boundPlaceholders = [];

        $numSegments = count($path);
        $targetNum = count($this->path);

        if($numSegments == $targetNum) {
            for($i=0; $i<$numSegments; $i++) {

                if($this->path[$i]["type"] == "placeholder")  {
                    array_push($boundPlaceholders, array($this->path[$i]["value"] => $path[$i]));
                } else {
                    if($this->path[$i]["value"] != $path[$i]) {
                        # fail if ANY part is not right.
                        return null;
                    }
                }
            }
        }

        return $boundPlaceholders;
    }
}
?>

