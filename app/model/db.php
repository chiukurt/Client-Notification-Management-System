<?php
$current_view = $config['VIEW_PATH'] . $page . DS;
$filepath = $config['DATA_PATH'] . $page.'.txt';
$errorpath = $config['DATA_PATH'] . $page.'_errors.txt';
$clienteventpath = $config['DATA_PATH'] . 'clientevent.txt';
$notificationpath = $config['DATA_PATH'] . 'notification.txt';

  switch (get('action')){ #Modular switch. $page variable dictates read/write location 
    case 'view':{
        $view = $current_view . 'view.phtml';
		logAction ($con);
        break;
    }

    case 'download':{
		$view = $current_view . 'download.phtml';
		$filename = generateDatabaseBackup($con);
		$downloadlink = '<a download="'.$filename.'" href="'.$filename.'">Download '.$filename.'</a>';
		logAction ($con);
		break;
	 }
	 
	case 'upload':{
		$view = $current_view . 'view.phtml';
		
			if(is_uploaded_file($_FILES['dbfile']['tmp_name'])
				&& substr($_FILES['dbfile']['name'],-3)=='sql'){
				$file_name = $_FILES['dbfile']['name'];
				$file_tmp = $_FILES['dbfile']['tmp_name'];
				move_uploaded_file($file_tmp,"uploads".DS.$file_name);
				
				$filename="uploads".DS.$file_name;
				restoreDatabase($con,$filename);
				logAction ($con);
				
				echo
				'<script>
					alert("Successfully restored database");
				</script>';
				
			}
			else
				echo
				'<script>
					alert("Failed in restoring database");
				</script>';
		break;
	}
	 
  }
  
?>



<?php
$source_links [] = "<br><a href='/folder_view/vs.php?s=" . __FILE__ . "' target='_blank'>View Source of ". __FILE__;
?>

