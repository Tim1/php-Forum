<html>
<head>
<title>Login</title>

</head>

<body>
	<div style="max-width: 800px;">
		<form action="login2.php" method="post">
			<?php 
			if($_GET['type'] == "false"){
				echo '<font color="red">Wrong Username or Password!</font><br/>';	
			}
			?>
		
			Username:<input type="text" name="username"><br /> Passwort:<input
				type="password" name="passwd"><br /> <input type="submit"
				value="Login">
			
		</form>
	</div>
</body>
</html>
