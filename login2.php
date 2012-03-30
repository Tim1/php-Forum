<html>
<head>
<meta http-equiv="refresh" content="0; URL=
<?php

$name = $_POST['username'];
$passwd = md5($_POST['passwd']);

// echo "name: ".$name;
// echo "<br/>pw: ".$passwd;

setcookie("User",$name,time()+60*60*24*365);
setcookie("passwd",$passwd,time()+60*60*24*365);

require_once 'lib/classes.php';

if(User::isValid($name, $passwd))
	echo "index.php";
else 
	echo "login.php?type=false";
?>
"> 

</head>

</html>