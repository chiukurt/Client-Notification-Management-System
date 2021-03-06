<?php
$current_view = $config['VIEW_PATH'] . $page . DS;
$filepath = $config['DATA_PATH'] . $page.'.txt';
$errorpath = $config['DATA_PATH'] . $page.'_errors.txt';
$clientpath = $config['DATA_PATH'] . 'client.txt';
$notificationpath = $config['DATA_PATH'] . 'notification.txt';


switch (get('action')){ #Modular switch. $page variable dictates read/write location 
    case 'view':{
        $view = $current_view . 'view.phtml';
		$fileinfo = $con->query("SELECT * FROM ".$page." ORDER BY 1");
		$header_items = $con->query("describe ".$page);
		
		logAction ($con);
		break;
    }
	case 'add':{
        $view = $current_view . 'add.phtml';
        break;
    }
	
	case 'doAdd':{ #Will abort add and redirect to data input if validation fails
		$inputs = array(
				get('ClientID'),get('NotificationID'),
				get('StartDate'),get('Frequency'),
				get('Status')
				);
		
		$active_clients=return_idsDB('client','Active',$con);
		$archived_clients=return_idsDB('client','Archive',$con);
		$enabled_notifications=return_idsDB('notification','Enabled',$con);
		$disabled_notifications=return_idsDB('notification','Disabled',$con);
		
		if (validate_input ($page, $inputs,$errorpath,
		$active_clients,$enabled_notifications,
		$archived_clients,$disabled_notifications)){		
			doAddDB($page,$inputs,$con);
			logAction ($con);
			header('location: ?page='.$page.'&action=view');
		}else{
			header('location: ?page='.$page.'&action=add'.$test);
		}
		break;
    }
	
	case 'delete':{
      	$view = $current_view . 'delete.phtml';
        $id = get('ID');
		doDeleteDB ($page,$id,$con);
		logAction ($con);
		header('location: ?page='.$page.'&action=view');
        break;
    }
	
	case 'update':{
          $view = $current_view . 'update.phtml';
		$query = "SELECT * FROM `".$page."` WHERE ID = '".get('ID')."'";
		$result = $con->query($query);
		while ($row = $result-> fetch_assoc()){
			$row_to_update = $row;
		}
		
		break;
    }
    case 'doUpdate':{
		$id = get('ID');
		$inputs = array(
			get('ClientID'),get('NotificationID'),
			get('StartDate'),get('Frequency'),
			get('Status')
			);
			
		$header_items = $con->query("describe ".$page);
		while($row = $header_items->fetch_assoc()){
			foreach ($row as $piece){
				$headers[] = $piece;
				break;
			}
		}		
				
		
		$active_clients=return_idsDB('client','Active',$con);
		$archived_clients=return_idsDB('client','Archive',$con);
		$enabled_notifications=return_idsDB('notification','Enabled',$con);
		$disabled_notifications=return_idsDB('notification','Disabled',$con);
		
		if (validate_input ($page, $inputs,$errorpath,
		$active_clients,$enabled_notifications,
		$archived_clients,$disabled_notifications)){
			doUpdateDB($page,$inputs,$headers,$con);
			logAction($con);
			header('location: ?page='.$page.'&action=view');
		}else{
			header('location: ?page='.$page.'&action=update&ID='.$id);
		}
		break;  
		#echo '<a href="?page='.$page.'&action=update&id=' . $item_info[0] . '">Update</td>';
    }
	
	
	
  }
  
?>
<?php
$source_links [] = "<br><a href='/folder_view/vs.php?s=" . __FILE__ . "' target='_blank'>View Source of ". __FILE__;
?>
