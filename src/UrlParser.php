<?php declare(strict_types=1);

# split a /path/to/$key into its constituent parts and provide comparison routes.
class UrlParser {
    const OPEN_PLACEHOLDER = "{";
    const CLOSE_PLACEHOLDER = "}";

    /**
     * Convert a single element between /'s into either a placeholder or not.
     * @param string $urlBit the url "bit" e.g. text between two /'s.
     */
    public function parseElement(string $urlBit) {
        $quotedStr = UrlParser::isPlaceholder($urlBit);

        if(strlen($quotedStr) > 0) {
            return array("type" => "placeholder",
                         "value" => $quotedStr);
        } else {
            return array("type" => "default",
                         "value" => $urlBit);
        }
    }
 
    /**
     * Check if a given string is a URL placeholder. For now, it's just if it is a word surrounded in {}.
     * We could use regex but doing this here allows for future modification.
     * Only support the entire thing being a placeholder, for now anyway.
     *
     * @param string $urlBit the url "bit" e.g. text between two /'s.
     * @return the placeholder string, or an empty string if no placeholder was found.
     */
    public static function isPlaceholder(string $urlBit) {
        $quotedStr = "";
        $isQuoted = false;

        for($i=0; $i<strlen($urlBit); $i++) {
            $chara = $urlBit[$i];
            
            if($isQuoted) {
                if($chara == self::CLOSE_PLACEHOLDER) {
                    $isQuoted = false;
                } else {
                    $quotedStr .= $chara;
                }
            } else {
                if($chara == self::OPEN_PLACEHOLDER) {
                    $isQuoted = true;
                }
            }
        }

        return strlen($quotedStr) > 0 ? $quotedStr : "";
    }
}
?>

