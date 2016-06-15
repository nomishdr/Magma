# Magma
PHP Based database

# Description

Magma is a php database library. Easy to use, fast, and no require external server. At the moment only base method are implemented.
(SELECT, INSERT, UPDATE, DELETE) conditions are presents, limit. The online documentation will come soon :)

#Performance
This is the big point.I always upgrade the script for you get the best experience. For an example, approximately 0.05s to insert 10 000 (yes 10 000) lines. 

# Examples
The fasted way is to require the class, create a new Magma object and let's work with it !

```php
<?php
require 'class.magma.php';

$Magma = new Magma\Magma();
$Magma->debug = true;

$Magma->new("MyDB");
$Magma->use("MyDB");

$Magma->create("MyTable", ["id"=>"int,auto_increment","firstname"=>"text","lastname"=>"text","date"=>"date"]);
$Magma->load("MyTable");
$Magma->insert(
	['', "Firstname1", "Lastname1", "d-m-Y"], //"d-m-y" will be replaced by the date (e.g 15-06-2016)
	['', "Firstname2", "Lastname2", ""], //4 column will be fill by the current date in English format (e.g 2016-06-15)
	['', "Firstname3", "Lastname3", "2017-08-01"] //That work too :)
	//...
);
$result = $Magma->fetch(); //Fetch with no conditions
$result = $Magma->fetch(["firstname"=>"Firstname1"]); //Find all firstname equaled too "Firstname1"
$result = $Magma->fetch(["firstname"=>"Firstname1"], ["LIMIT"=>1]); //Find the first firstname equaled too "Firstname1"

$Magma->update(["firstname"=>"New firstname ! :D"], ["id"=>3]); //Find id=3 and replace his firstname with the associate value

$Magma->delete(["id"=>1]); //Delete record where id=1
?>
```
