<?php declare(strict_types=1);

require_once dirname(__FILE__) . "/../src/Controller.php";

$cont = new Controller();
$cont->get("/ControllerTest.php/post/{id}", function(array $opts) {
    echo "Success! Id is " . $opts["id"];
});

# TODO: find a workaround to avoid this method call.
$cont->commence();
?>

