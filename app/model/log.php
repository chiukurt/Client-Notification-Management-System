<?php
$current_view = $config['VIEW_PATH'] . $page . DS;
$filepath = $config['DATA_PATH'] . $page.'.txt';
$errorpath = $config['DATA_PATH'] . $page.'_errors.txt';
$clienteventpath = $config['DATA_PATH'] . 'clientevent.txt';
$notificationpath = $config['DATA_PATH'] . 'notification.txt';


  switch (get('action')){ #Modular switch. $page variable dictates read/write location 
    case 'view':{
        $view = $current_view . 'view.phtml';
		$fileinfo = $con->query("SELECT * FROM ".$page." ORDER BY 1 DESC");
		$header_items = $con->query("describe ".$page);
		logAction ($con);
        break;
    }
  }
  
?>



<?php
$source_links [] = "<br><a href='/folder_view/vs.php?s=" . __FILE__ . "' target='_blank'>View Source of ". __FILE__;
?>

