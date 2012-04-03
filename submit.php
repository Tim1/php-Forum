<?php

require_once 'lib/classes.php';

//SQL Safe
$name = mysql_real_escape_string($_COOKIE['User']);
$passwd = mysql_real_escape_string($_COOKIE['passwd']);

$threadid = -1;

if(User::isValid($name, $passwd)){
	$id = User::getIdByName(mysql_real_escape_string($name));
	if($_POST['type'] == "thread"){
		$threadid = Thread::makeThread(mysql_real_escape_string($_POST['title']), $id);

		Post::makePost(mysql_real_escape_string($_POST['text']), $id, $threadid);

	}elseif ($_POST['type'] == "post" && is_numeric($_POST['threadid'])){
		$threadid = mysql_real_escape_string($_POST['threadid']);


		if(Thread::isValid($threadid))
			Post::makePost(mysql_real_escape_string($_POST['text']), $id, $threadid);
		else
			die("No such Thread");

	}

}
else{
	// 	echo "User: ".$name."<br/>";
	// 	echo "Pass: ".$passwd."<br/>";
	die("wrong Username or Password");
}

header("Location: thread.php?id=$threadid");
?>