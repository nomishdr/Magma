<!DOCTYPE html>
<html>
	<head>
			<title>Magma Demo</title>

	</head>
<body style="font-family:Helvetica, arial;">
<?php
require '../class.magma.php';

$Magma = new Inxk\Magma();
$Magma->debug = true;

//$Magma->new("MyDB");

$Magma->use("MyDB");

//$Magma->create("MyTable", ["id"=>"int,auto_increment","firstname"=>"text","lastname"=>"text","date"=>"date"]);

$Magma->load("MyTable");
$Magma->insert(
    ['', "Firstname1", "Lastname1", "d-m-Y"], // "d-m-y" will be replace by the date (e.g 15-06-2016)
    ['', "Firstname2", "Lastname2", ""], //4 column will be fill by the current date in English format (e.g 2016-06-15)
    ['', "Firstname3", "Lastname3", "2017-08-01"] //This also works :)
    //...
);
$result = $Magma->fetch(); //Fetch with no conditions
	echo("<h3>fetch() (fetch with no conditions) :</h3>");
	print_r($result);
	echo("<br />");
$result = $Magma->fetch(["firstname"=>"Firstname1"]); //Find every firstname equal to "Firstname1"
	echo('<h3>fetch(["firstname"=>"Firstname1"])</h3>');
	print_r($result);
	echo("<br />");
$result = $Magma->fetch(["firstname"=>"Firstname1"], ["LIMIT"=>1]); //Find the first first name equal to "Firstname1"
	echo('<h3>fetch(["firstname"=>"Firstname1"], ["LIMIT"=>1])</h3>');
	print_r($result);
	echo("<br />");

$Magma->update(["firstname"=>"New firstname ! :D"], ["id"=>3]); //Find id=3 and replace its firstname with the associate value

$Magma->delete(["id"=>1]); //Delete record where id=1
?>
</body>
</html>