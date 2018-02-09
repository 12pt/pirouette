<?php declare(strict_types=1);

# split a /path/to/$key into its constituent parts and provide comparison routes.
class UrlParser {

    // /**
    //  * @param string $targetUrl the url (+ optional placeholders) to match actual urls against
    //  */
    // public function __construct(string $targetUrl) {
    //     # url can contain regex
    //     # url can contain placeholders e.g. {id}
    //     $path = explode($targetUrl, "/");
    //     $constructed = [];
    //     foreach($path as $element) {
    //         array_push($constructed, _parseElement($element));
    //     }
    // }

    /**
     * Convert a single element between /'s into either a placeholder or not.
     * Only support the entire thing being a placeholder, for now anyway.
     * it would be easy to add support for "post{id}" in the future but it's questionable
     * to have parts of the url that look static but aren't.
     */
    public function parseElement(string $urlBit) {
        $isQuoted = false;
        $quotedStr = "";

        for($i=0; $i<strlen($urlBit); $i++) {
            $chara = $urlBit[$i];
            
            if($isQuoted) {
                if($chara == "}") {
                    $isQuoted = false;
                } else {
                    $quotedStr .= $chara;
                }
            } else {
                if($chara == "{") {
                    $isQuoted = true;
                }
            }
        }

        if(strlen($quotedStr) > 0) {
            return array("type" => "placeholder",
                         "value" => $quotedStr);
        } else {
            return array("type" => "default",
                         "value" => $urlBit);
        }
    }

    /**
     * Match a given url e.g. /blog/post/34 to a template url /blog/post/{id}
     */
    public function match(string $url, mixed $kvs) {
        # return array('id' => 34);
    }
}
?>

