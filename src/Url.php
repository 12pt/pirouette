<?php declare(strict_types=1);

final class Url {
    private $url;

    /**
     * Initialise the URL object.
     * @param string $targetUrl the url (+ optional placeholders) to match actual urls against
     */
    public function __construct(string $targetUrl) {
        $parser = new UrlParser();
        $this->url = [];
        # url can contain regular ol' words
        # [NYI] url can contain regex 
        # url can contain placeholders e.g. {id}

        $path = Url::explodeTrim($targetUrl);
        foreach($path as $element) {
            array_push($this->url, $parser->parseElement($element));
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
     * Match a given url e.g. /blog/post/34 to a template url /blog/post/{id}
     *
     * @return an associative array binding ids to values if the url matches (or an empty array
     * if it matches and no placeholders were used) or null if it didnt match.
     */
    public function matchUrl(string $targetUrl) {
        $path = Url::explodeTrim($targetUrl);
        $boundPlaceholders = [];

        $numSegments = count($path);
        $targetNum = count($this->url);

        if($numSegments == $targetNum) {
            for($i=0; $i<$numSegments; $i++) {

                if($this->url[$i]["type"] == "placeholder")  {
                    array_push($boundPlaceholders, array($this->url[$i]["value"] => $path[$i]));
                } else {
                    if($this->url[$i]["value"] != $path[$i]) {
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

