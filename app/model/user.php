<?php
$current_view = $config['VIEW_PATH'] . $page . DS;
$filepath = $config['DATA_PATH'] . $page.'.txt';
$errorpath = $config['DATA_PATH'] . $page.'_errors.txt';
$clienteventpath = $config['DATA_PATH'] . 'clientevent.txt';
$notificationpath = $config['DATA_PATH'] . 'notification.txt';

  switch (get('action')){ #Modular switch. $page variable dictates read/write location 
    case 'view':{
        $view = $current_view . 'view.phtml';
		$fileinfo = $con->query("SELECT * FROM ".$page." ORDER BY 1");
		$header_items = $con->query("describe ".$page);
		
		logAction ($con);
        break;
    }
	case 'add':{//Username must be unique
        $view = $current_view . 'add.phtml';
        break;
    }
	
	case 'doAdd':{ #Will abort add and redirect to data input if validation fails
		$inputs = array(
					get ("FirstName"), get ("LastName"), get ("E-mail"),
					get ("CellNumber"),get ("Position"),
					get ("Username"),
					password_hash(get ("Password"),PASSWORD_DEFAULT),
					get("Status"),
					get ("Photo")
				);
		
		
		if (validate_input ($page, $inputs,$errorpath)){
			doAddDB ($page,$inputs,$con);
			logAction ($con);
			header('location: ?page='.$page.'&action=view');
		}else{
			header('location: ?page='.$page.'&action=add');
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
					get ("FirstName"), get ("LastName"), get ("E-mail"),
					get ("CellNumber"),get ("Position"),
					get ("Username"),
					get("Status")
				);
				
		if(is_uploaded_file($_FILES['image']['tmp_name']) ){
			$file_name = $_FILES['image']['name'];
			$file_tmp = $_FILES['image']['tmp_name'];
			
			$extention = explode('.',$file_name);
			$ext = end($extention);
			move_uploaded_file($file_tmp,"uploads".DS.$id.".".$ext);
			$inputs[] = "<img src = 'uploads".DS.$id.".".$ext."' width=55 height=55>";
	   }
		
		
				
		//do not update password
		$header_items = $con->query("describe ".$page);
		while($row = $header_items->fetch_assoc()){
			foreach ($row as $piece){
				if (strcmp($piece,'Password')!=0){
					$headers[] = $piece;
				}
				break;
			}
		}
		
		
		if (validate_input ($page, $inputs,$errorpath)){
			doUpdateDB($page,$inputs,$headers,$con);
			logAction($con);
			header('location: ?page='.$page.'&action=view');
		}else{
			header('location: ?page='.$page.'&action=update&ID='.$id);
		}
		
		break;  
	 }
	
	
	
  }
  
?>



<?php
$source_links [] = "<br><a href='/folder_view/vs.php?s=" . __FILE__ . "' target='_blank'>View Source of ". __FILE__;
?>

