<?php
include "config.php";

class User {
	private $id;
	private $name;
	private $passwd;

	function __construct($id){
		$this->id = $id;

		$sql = "SELECT name, passwd FROM `user` WHERE id = ".$id;
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			$this->name = $row->name;
			$this->passwd = $row->passwd;
		}
	}

	function printInfo(){
		echo "$this->id | $this->name | $this->passwd";
	}

	public static function getNameById($id){
		$sql = "SELECT name FROM `user` WHERE id = ".$id;
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			return $row->name;
		}
		return "no User";
	}

	public static function printLogin(){
		$user = $_COOKIE['User'];
		$passwd = $_COOKIE['passwd'];

		if(User::isValid($user, $passwd))
			echo "User: <b>". $user."</b>";
		else
			echo '<font color="red">No Login!</font> ';
	}


	public static function isValid($name,$passwd){
		$id = -1;
		$sql = "SELECT id FROM `user` WHERE name = '".$name."' AND passwd = '".$passwd."'";
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			return $row->id > 0 ? true : false;
		}

		return false;
	}

	public static function getPasswdByName($name){
		$sql = "SELECT passwd FROM `user` WHERE name = '".$name."'";
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			return $row->passwd;
		}
		return " ";
	}

	public static function getIdByName($name){
		$sql = "SELECT id FROM `user` WHERE name = '".$name."'";
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			return $row->id;
		}
		return -1;
	}

	public static function getPostsById($id){
		$sql = "SELECT count(*) as po FROM `post` WHERE user = ".$id;
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			return $row->po;
		}
		return 0;
	}
}

class Thread{
	private $id;
	private $title;
	private $user;
	private $post_count;
	private $date;

	function __construct($id){
		$this->id = $id;

		$sql = "SELECT title, user, date FROM `thread` WHERE id = ".$id;
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			$this->title = $row->title;
			$this->user = $row->user;
			$this->date = $row->date;
		}

		//wieviel Post im Thread  ERSTER Post z�hlt nicht mit!
		$sql = "SELECT count(*) as posts FROM `post` WHERE thread = ".$id;
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			$this->post_count = $row->posts - 1;
		}



	}

	function printInfo(){
		echo "$this->id | $this->title | ". User::getNameById($this->user) ." | $this->date | $this->post_count</br>";
	}

	function getTitle(){
		return $this->title;
	}

	function getUser(){
		User::getNameById($this->user);
	}

	function getDate(){
		$this->date;
	}

	public static function getTitleById($id){
		$sql = "SELECT title FROM `thread` WHERE id = ".$id;
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			return $row->title;
		}
		return "no Thread";
	}

	public static function getAllThreads(){
		$threads = array();

		$sql = "SELECT thread FROM `post` GROUP BY thread ORDER BY max( id ) DESC";
		$query = mysql_query($sql);

		$i = 0;
		$row = mysql_fetch_object($query);
		while($row){
			$threads[$i] = new Thread($row->thread);
			$row = mysql_fetch_object($query);
			$i++;
		}

		return $threads;
	}

	function printPreviewHTML(){
		$post = $this->getLastestPost();
		echo "<tr><td>";
		echo "<b><a href=\"thread.php?id=$this->id\"> $this->title </a></b><br/>";
		echo "<i>Last Post: ". $post->getDate() ."</i> by <b> ". $post->getUser()  ."</b><br/>";
		echo "<b>$this->post_count</b> Answers";

		echo "</td></tr>";
	}

	function getLastestPost(){
		$sql = "SELECT max(id) as maxid FROM `post` WHERE thread = ".$this->id;
		$query = mysql_query($sql);

		if($query){
			$row = mysql_fetch_object($query);
			return new Post($row->maxid);
		}
		return new Post(-1);
	}

	function printHTML(){
		echo '<table border="5">';

		echo '<tr><th></th><td>';

		echo "<font size=\"5\"><b> $this->title </b></font><br/>";
		echo "<i> $this->date by </i><b>".User::getNameById($this->user)."</b> <br/>";
		echo "$this->post_count Answers";
		echo '</tr>';

		echo "</td>";

		$posts = Post::getThreadPosts($this->id);
		foreach ($posts as $p)
			$p->printHTML();

		//Submit Form
		echo '<tr><td></td><td><br/><br/><br/> <b> New Post </b>';
		Submit::printFormPost($this->id);
		echo '</td></tr>';

		echo '</table> ';
	}

	public static function makeThread($title, $userid){
		$sql = "INSERT INTO `thread` (`id`, `title`, `user`, `date`) VALUES (NULL, '". htmlentities($title) ."', '". $userid ."', CURRENT_TIMESTAMP);";
		$query = mysql_query($sql);

		$sql = "SELECT max( id ) as maxID
		FROM `thread`";
		$query = mysql_query($sql);
		if ($query){
			$row = mysql_fetch_object($query);
			return $row->maxID;
		}

		return -1;
	}

	public static function isValid($id){
		$sql = "SELECT id FROM `thread` WHERE id = ".$id;
		$query = mysql_query($sql);

		$i = -1;
		if($query){
			$row = mysql_fetch_object($query);
			$i = $row->id;
		}
		return $id == $i ? true : false;
	}
}

class Post{
	private $id;
	private $text;
	private $thread;
	private $user;
	private $date;

	function __construct($id){
		$this->id = $id;
		$temp = "";

		$sql = "SELECT text, thread, user, date FROM `post` WHERE id = ".$id;
		$query = mysql_query($sql);
		if($query){
			$row = mysql_fetch_object($query);
			$temp = $row->text;
			$this->thread = $row->thread;
			$this->user = $row->user;
			$this->date = $row->date;
		}
		$order   = array("\r\n", "\n", "\r");
		$replace = '<br />';

		$this->text = str_replace($order, $replace, $temp);
	}

	function printInfo(){
		echo "Post: $this->id |".Thread::getTitleById($this->thread)." | ". User::getNameById($this->user) ."| $this->text | $this->date";
	}

	function printHTML(){
		echo '<tr><td>';

		echo "<b>".User::getNameById($this->user)."</b><br/>";
		echo "<i>$this->date</i><br/><br/>";
		echo "Posts: ".User::getPostsById($this->user);

		echo "</td><td> $this->text";


		echo '</td></tr>';
	}

	function getText(){
		return $this->text;
	}

	function getUser(){
		return User::getNameById($this->user);
	}

	function getDate(){
		return $this->date;
	}

	public function getThreadPosts($id){
		$posts = array();

		$sql = "SELECT id FROM `post` WHERE thread = ".$id." ORDER BY id";
		$query = mysql_query($sql);

		$i = 0;
		$row = mysql_fetch_object($query);
		while($row){
			$posts[$i] = new Post($row->id);
			$row = mysql_fetch_object($query);
			$i++;
		}

		return $posts;
	}

	public static function makePost($text, $userid, $threadid){
		$sql = "INSERT INTO `post` (`id`, `text`, `thread`, `user`, `date`) VALUES (NULL, '".htmlentities($text)."', '".$threadid."', '".$userid."', CURRENT_TIMESTAMP);";

		$query = mysql_query($sql);
	}
}

class Frontpage{
	public static function show(){
		$threads = Thread::getAllThreads();

		echo '<table border="5" style="width: 800px;">';
		foreach ($threads as  $t)
			$t->printPreviewHTML();

		echo "<tr><td><br/><br/><br/><br/> <b> New Thread </b>";
		Submit::printFormThread();
		echo "</tr></td>";
		echo '</table>';
	}
}

class Submit{
	public static function printFormThread(){
		echo '<form action="submit.php" method="post">
		<input type="text" name="title"/><br/>
		<textarea rows="8" cols="115" name="text"></textarea>
		<input type="hidden" name="type" value="thread">
		<br /> <input type="submit" value="Post Thread">
		</form>';
	}
	public static function printFormPost($threadid){
		echo '<form action="submit.php" method="post">
		<textarea rows="8" cols="90" name="text"></textarea>
		<input type="hidden" name="type" value="post">
		<input type="hidden" name="threadid" value="'.$threadid.'">
		<br /> <input type="submit" value="Post">
		</form>';
	}


}


?>