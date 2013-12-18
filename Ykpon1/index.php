<!DOCTYPE html>
<?php
function __autoload() {
    $filename = __DIR__ . "/inc/*.php";
    include_once($filename);
}



?>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
		<?php
		// put your code here
		?>
    </body>
</html>
