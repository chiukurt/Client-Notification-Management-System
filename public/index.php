<?php
#portions of code from 2019/maziar/wk5
#Kurt Chiu 101190261
//ob_start();

$con = mysqli_connect('localhost', 'f9190261_1', '12345','f9190261_assignment2');
if ($con->connecterrno){
	die ('connect failed'.$con->connect_error);
}


const DS = DIRECTORY_SEPARATOR; 
defined('APP_PATH') || define('APP_PATH', realpath(dirname(__FILE__)). DS . '..' . DS . 'app' . DS);
require APP_PATH . DS . 'config' . DS . 'config.php'; 

$page = get('page','home');

if (!isset ($_COOKIE["user"])){
	$page='login';
	$uname = get ('Username');
	$pass = get ('Password');
	
	
	$query = ("SELECT `Username`,`Password`,`Status` FROM user");
	$result = $con->query($query);

	while ($row = $result-> fetch_assoc()){
		if (strcmp ($row['Username'], $uname) == 0){
			if (strcmp ($row['Status'], 'Active') == 0){
				if (password_verify ($pass, $row['Password'])){
					setcookie ("user", $uname);
					$page='home';
				}
			}
		}
	}
}
// echo $row;

$model = $config['MODEL_PATH'] .  $page . '.php';
$view  = $config['VIEW_PATH']  .  $page . '.phtml';
$_404  = $config['VIEW_PATH'] . '404.phtml';
$source_links = [];
$source_links [] = "<br><a href='/folder_view/vs.php?s=" . __FILE__ . "' target='_blank'>View Source of ". __FILE__;
$source_links [] = "<br><a href='/folder_view/vs.php?s=/home/f9190261/public_html/comp1230/assignments/assignment2/app/config/config.php' target='_blank'>View Source of /home/f9190261/public_html/comp1230/assignments/assignment2/app/config/config.php";
$source_links [] = "<br><a href='/folder_view/vs.php?s=/home/f9190261/public_html/comp1230/assignments/assignment2/app/lib/functions.php' target='_blank'>View Source of /home/f9190261/public_html/comp1230/assignments/assignment2/app/lib/functions.php";



if(file_exists($model))
{
    include $model;
}

$main_content = $_404;
if(file_exists($view))
{
    $main_content = $view;
}
include   $config['VIEW_PATH'] . 'layout.phtml';


echo '<div class="container">';
foreach ($source_links as $link){
	echo $link;
}
echo '</div>';

mysqli_close($con);
//ob_end_flush();
?>