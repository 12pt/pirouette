<?php declare(strict_types=1);

require_once dirname(__FILE__) . "/../src/Controller.php";

function printAssoc(array $arr, string $sep="<br>") {
    return urldecode(http_build_query($arr, "", $sep));
}

$cont = new Controller();
$cont->get("/ControllerTest.php/post/{id}", function(array $opts) {
    $str = "<p>Success! Id is " . $opts["id"] . "</p>";
    $str .= "<p>opts is <pre>" . printAssoc($opts) . "</pre></p>";
    return $str;
});

?>

