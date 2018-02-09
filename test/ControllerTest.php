<?php declare(strict_types=1);

require_once dirname(__FILE__) . "/../src/Controller.php";

$cont = new Controller();
$cont->get("/ControllerTest.php/post/{id}", function(array $opts) {
    return "<p>Success! Id is " . $opts["id"] . "</p>";
});

?>

