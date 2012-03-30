<html>
<head>
<title>Frontpage</title>
</head>
<?php 
require_once 'lib/classes.php';
?>

<body>
	<div style="max-width: 850px;">
		<div style="display: block; text-align: left; float: left;">
			<?php User::printLogin() ?>
		</div>
		<div style="display: block; text-align: right;">
			<a href="login.php">Login </a>&nbsp;
		</div>

		<?php 
		Frontpage::show();
		?>
	</div>
</body>
</html>
