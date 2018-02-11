<?php declare(strict_types=1);

require_once dirname(__FILE__) . "/../src/Controller.php";

function printAssoc(array $arr, string $sep="<br>") {
    return urldecode(http_build_query($arr, "", $sep));
}

$cont = new Controller();
$cont->get("/ControllerTest.php/post/{id}", function(array $req) {
    $str = "<p>Success! Id is " . $req["id"] . "</p>";
    $str .= "<p>req is <pre>" . printAssoc($req) . "</pre></p>";
    return $str;
});

?>

