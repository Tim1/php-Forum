<html>
<head>
<title><?php

require_once 'lib/classes.php';


$id = $_GET['id'];

$thread = new Thread($id);
echo $thread->getTitle();

?>
</title>

</head>

<body>
	<div style="max-width: 850px;">
		<div style="display: block; text-align: left; float: left;"><?php User::printLogin() ?></div>
		<div style="display: block; text-align: right;"><a href="index.php">Back to Frontpage </a>&nbsp;</div>
		
		<?php 
		$thread->printHTML();
		?>
	</div>
</body>
</html>
