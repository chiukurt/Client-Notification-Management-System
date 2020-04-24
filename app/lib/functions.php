<?php
#functions.php
#portions of code from 2019/maziar/wk5
#Kurt Chiu 101190261


function logAction ($con){
	$query = "INSERT INTO `log` 
	(`Username`, `Page`, `Action`, `DateTime`, `IP`)
	VALUES ('";
	$query.= $_COOKIE["user"]."', '".
	get ('page')."', '".
	get ('action')."', '".
	date('m/d/Y h:i:s a', time())."', '".
	$_SERVER['REMOTE_ADDR']."')";
	
	$con->query($query);
}

#Dynamic function, $page dictates table to insert, $column_list is an array of values to insert
function doAddDB ($page,$column_list,$con){
	$query = "INSERT INTO `".$page."` VALUES (NULL";
	
	foreach ($column_list as $col){
		$colu = $con->real_escape_string($col);
		$query.=", '".$colu."'";
	}
	$query.=")";
	$con->query($query);
}

#Dynamic function, $page dictates table to update, $column_list is an array of values to update, 
#$colnames is a list of the headers to easily do `columnname` = 'newvalue'
function doUpdateDB ($page,$column_list,$colnames,$con){
	$index = 1;//skip the ID which is at index 0
	$id = get ('ID');
	
	$query = "UPDATE `".$page."` SET ";
	
	foreach ($column_list as $col){
		$colu = $con->real_escape_string($col);
		$query.="`".$colnames[$index]."` = '".$colu."',";
		$index++;
	}
	$query = substr ($query,-1);
	$query.=" WHERE `".$page."`.`ID` = '".$id."'";
	
	$con->query($query);
}

#When a client or notification is deleted, so is their associated event.
# if $page == client, the event using clientID will be deleted. Same logic for notificationID
# idListToCheck is a secondary array that stores what is currently active from the opposite $page.
# this is important because if a notification is disabled and the client is activated, the event stays disabled.
function updateEvents($page,$con,$idListToCheck){
	//Validation is already checked.
	$id = get ('ID');
	$updateCell="";
	$newEventVal="";
	$sourceStatus = get('Status');
	$canUpdate = false;
	
	if ($page == 'client'){
	$updateCell="ClientID";
	$secondaryCell = "NotificationID";
	if ($sourceStatus == 'Active')
		$newEventVal='Active';
	else
		$newEventVal='Archive';
	}
	
	if ($page == 'notification'){
	$updateCell="NotificationID";
	$secondaryCell="ClientID";
	if ($sourceStatus == 'Enabled')
		$newEventVal='Active';
	else
		$newEventVal='Archive';
	}
	
	$query = "UPDATE `clientevent` SET `Status` = '".$newEventVal."' WHERE `clientevent`.".$updateCell." = '".$id."'";
	$query.= " AND `clientevent`.".$secondaryCell." IN ('".$idListToCheck[0]."'";

	foreach ($idListToCheck as $id){
		$query.= ",'".$id."'";
		
	}
	$query.=")";
	
	$con->query($query);
}


function doDeleteDB($page,$id,$con){
	$query = ("DELETE FROM `". $page ."` WHERE `". $page ."`.`ID` = ".$id);
	$con->query($query);
}

#same as ON DELETE CASCADE foreign key functionality in database
function deleteDependentsDB ($page,$id,$con){
	if ($page == 'client'){
		$query = ("DELETE FROM `clientevent` WHERE `clientevent`.`ClientID` = ".$id);
	}
	if ($page == 'notification'){
		$query = ("DELETE FROM `clientevent` WHERE `clientevent`.`NotificationID` = ".$id);
	}
	
	$con->query($query);
	//Will delete all instances due to WHERE clause
}

#return array of IDS where status = $value from $source table
#used for updateEvents function parameter
function return_idsDB($source,$value,$con){
	$result = $con->query("SELECT `ID` FROM `".$source."` WHERE `Status` ='".$value."'");
	$array_of_ids=[];
	while($row = $result->fetch_assoc()){
		foreach ($row as $piece){
			$array_of_ids[] = $piece;
			break;
		}
	}
	return $array_of_ids;
}

#Write array of errors to html file via echo
function write_errors($page,$file_name){
	$error_file = file ($file_name);
			
	if (count($error_file) > 0){ #Only print errors if they exist
		echo '<p><b style="color:red;">Submission failed due to reason(s) below:</b></p>';
		foreach ($error_file as $line){
			echo $line.'<br>';
		}
		#After writing, empty the file to prevent unwanted printing in future
		file_put_contents($file_name,'');
	}
	
}

#Saves errors to a file, to be printed by write_errors on refresh.
function errors_to_file($page, $error_list,$file_name){
	#File overwritten because errors are unique to each time func is called.
	$error_file = fopen ($file_name,'w') or die ("cannot open file");
	
	foreach ($error_list as $error){
		fwrite ($error_file, $error . PHP_EOL);
	}
}

#returns boolean of if phone number contains only spaces, dashes and numbers
function phone_valid ($input){
	$isvalid = true;
	$input_as_array = str_split ($input);
	foreach ($input_as_array as $chara){
		if (!(ord($chara) == ord('-') || ord($chara) == ord(' ') 
			|| ord ($chara) >= ord('0') && ord ($chara) <= ord ('9'))){
			$isvalid = false;
		}
	}
	return $isvalid;
}

#returns boolean of if contains a~z, A~Z and spaces only
function name_valid ($input){
	$isvalid = true;
	$input_as_array = str_split ($input);
	foreach ($input_as_array as $chara){
		if (!( (ord ($chara) >= ord('a') && ord ($chara) <= ord ('z'))
			|| (ord ($chara) >= ord('A') && ord ($chara) <= ord ('Z'))
			||  ord($chara) == ord(' ') )){
			$isvalid = false;
		}
	}
	return $isvalid;
}

#Takes specific format of date, splits, and then validates and returns boolean
function date_valid ($input){
	$isvalid = true;
	$input_as_array = explode ('-',$input);
	foreach ($input_as_array as $date_part){
		if (!is_numeric($date_part)){
			$isvalid=false;
		}
	}
	if ($isvalid){ #If input is all numeric, perform checkdate function 
		if (!checkdate ($input_as_array[1],$input_as_array[0],$input_as_array[0]) ){
			$isvalid = false;
		}
		
	}
	return $isvalid;
}


 #Used for both doAdd and doUpdate for all pages. 
function validate_input ($page, $inputs, $file_name, 
$active_clients = NULL, $enabled_notifications = NULL, 
$archived_clients = NULL, $disabled_notifications = NULL){
	$error_list=[]; #If fails, saves errors to file which is used after redirect to print errors
		switch ($page){ #Status is selected as disabled/archive by default. Will never pass as null.
			case 'client':{
				if ($inputs[0]==""){ #Validate company name (can have any characters)
					$error_list[] = 'Company name is a required field.';
				}
				if ($inputs[1]==""){ #Validate Business number
					$error_list[] = 'Business number is a required field.';
				}
				if ($inputs[2]==""){ #Validate first name
					$error_list[] = 'First Name is a required field.';
				}
				if ($inputs[3]==""){ #Validate last name
					$error_list[] = 'Last Name is a required field.';
				}
				if ($inputs[4]==""){ #Validate phone number
					$error_list[] = 'Phone Number is a required field.';
				}
				if ($inputs[5]==""){ #Validate cell number
					$error_list[] = 'Cell Number is a required field.';
				}
				
				if (!phone_valid ($inputs[1]) || !phone_valid ($inputs[4]) || !phone_valid ($inputs[5])){
					$error_list[] = 'Phone, Business, and Cell numbers can only contain numbers (0~9), dashes (-) and spaces';
				}
				
				if (!name_valid ($inputs[2]) || !name_valid ($inputs[3])){
					$error_list[] = 'First name and Last name can only contain letters and spaces. (a~z) or (A~Z)';
				}
				
				if (!filter_var($inputs[6], FILTER_VALIDATE_URL) && $inputs[6]!=""){
					$error_list[] = 'URL for website has failed validation standards (requires http:// etc)';
				}#Since it is optional, 'if field is empty error' not triggered.
				
				break;
			}
			
			case 'notification' :{
				if ($inputs[0]==""){ #Validate Name (can have any characters)
					$error_list[] = 'Notification name is a required field.';
				}
				if ($inputs[1]==""){ #Validate Type
					$error_list[] = 'Notification type is a required field.';
				}
				break;
			}
			
			case 'clientevent' :{
				if ($inputs[0]==""){ #Client ID
					$error_list[] = 'Client ID is a required field.';
				}
				if ($inputs[1]==""){ #Notification ID
					$error_list[] = 'Notification ID is a required field.';
				}
				if ($inputs[2]==""){ #Start date
					$error_list[] = 'Start date is a required field.';
				}
				if ($inputs[3]==""){ #Frequency
					$error_list[] = 'Frequency is a required field.';
				}
				
				$id_exists=true; #flag
				$notification_exists=true; #flag
				
				if (isset ($active_clients)){ #Checks if client ID is within all saved ids (active and archived)
					if (!in_array ($inputs[0],$active_clients) && !in_array ($inputs[0],$archived_clients)){
						$error_list[] = 'Client ID does not exist.';
						$id_exists=false;
					}
					
					#Error if ID is not within the 'Active' array. Skips if ID does not exist.
					if (strpos($inputs[4],'Active') !== false && $id_exists){ 
						if (!in_array ($inputs[0],$active_clients)){
							$error_list[] = 'Cannot set as active. Client ID is not active.';
						}
					}
				}
				
				#Checks if notification ID is within all saved ids (enabled and disabled)
				if (isset ($enabled_notifications)){
					if (!in_array ($inputs[1],$enabled_notifications) && !in_array ($inputs[1],$disabled_notifications)){
						$error_list[] = 'Notification ID does not exist.';
						$notification_exists=false;
					}
					
					#Error if ID is not within the 'Enabled' array. Skips if ID does not exist.
					if (strpos($inputs[4],'Active') !== false && $notification_exists){
						if (!in_array ($inputs[1],$enabled_notifications)){
							$error_list[] = 'Cannot set as active. Notification ID is disabled.';
						}
					}
				}
				
				if (!date_valid($inputs[2])){ #Date format and validity
					$error_list[] = 'Start date is invalid. Must be in the format day-month-year ex. 20-11-2009';
				}
				
				if (!(filter_var($inputs[3],FILTER_VALIDATE_INT) && $inputs[3]>0 )){ #Frequency
					$error_list[] = 'Frequency can only be a positive whole number in days';
				}
				
				
				break;
			}
			
			case 'user':{
				if ($inputs[0]==""){ #Validate Name (can have any characters)
					$error_list[] = 'First name is a required field.';
				}
				if ($inputs[1]==""){ #Validate Name (can have any characters)
					$error_list[] = 'Last name is a required field.';
				}
				if ($inputs[2]==""){ #Validate Name (can have any characters)
					$error_list[] = 'e-mail address is a required field.';
				}
				if ($inputs[3]==""){ #Validate Name (can have any characters)
					$error_list[] = 'cell phone is a required field.';
				}
				if ($inputs[4]==""){ #Validate Name (can have any characters)
					$error_list[] = 'position is a required field.';
				}
				if ($inputs[5]==""){ #Validate Name (can have any characters)
					$error_list[] = 'Username is a required field.';
				}
				if ($inputs[6]==""){ #Validate Name (can have any characters)
					$error_list[] = 'Password is a required field.';
				}
				
				if (!filter_var($inputs[2], FILTER_VALIDATE_EMAIL)) {
					$error_list[] = 'E-mail is in incorrect format.';
				}
				
				if (!phone_valid($inputs[3])){ #Validate Name (can have any characters)
					$error_list[] = 'Cell phone number in incorrect format (can only contain numbers (0~9), dashes (-) and spaces)';
				}
				break;
			}
			
			
			default :{
				break;
			}
		}#If anything is in the error list, validation has failed. 
		errors_to_file ($page,$error_list,$file_name); #Saves errors for future printing on page refresh if validation fails
		return !(isset ($error_list[0])); #arbitrary index is used as having any values is considered a failure of validation
}


#Used for printing generic form input rows for add and update views
function print_row ($label, $name, $value){
	echo
	'<tr>
		<td>'. $label .': </td>
		<td><input type="text" name="'.$name.'" value="'.$value.'"></td>
	</tr>';
}

function list_headers($header_items, $showOptions = TRUE){
	echo '<tr  style="background:lightgrey;" >';
	
	while($row = $header_items->fetch_assoc()){
		foreach ($row as $piece){
			if ($piece != 'Password'){
			echo '<td>' . $piece . '</td>';
			break;
			}
			break;
		}
	}
	
	if ($showOptions)
		echo '<td></td><td></td>';
}


#Lists all items with update and delete links. Filter is active upon search.
function list_itemsDB ($item_list,$filter=NULL,$page,$showOptions=TRUE){
	
	foreach ($item_list as $item) {
		$printrow=false;
        $item_info = $item;
		
		if (isset($filter) && $filter!=""){#Valid filter, allow printing of current row.
			foreach ($item_info as $info_piece) {
            if (stripos($info_piece,$filter)!==false)
				$printrow=true; 
			}
		}
		else{
			$printrow=true;#No filter, allow printing of all rows.
		}
		
		if ($printrow){
		
			echo '<tr>';
			
			foreach ($item_info as $index=>$info_piece) {
				
				if ($index != 'Password'){
					if (isset($filter) && stripos($info_piece,$filter) !==false) {
						echo '<td><b>' . $info_piece . '</b></td>';
					}else{
						echo '<td>' . $info_piece . '</td>';
					}
				}
				
			}
			
			if ($showOptions){#Do not show delete and update option for column headings
			echo '<td><a href="?page='.$page.'&action=delete&ID=' . $item_info['ID'] . '">Delete</td>';
			echo '<td><a href="?page='.$page.'&action=update&ID=' . $item_info['ID'] . '">Update</td>';
			
			}else
			{
				echo '<td></td><td></td>';
			}
			
			echo '</tr>';
		}
    }
}


function get($name,$def='')
{
   //PHP 7 return $_REQUEST[$name] ?? $def;
   return isset($_REQUEST[$name]) ? $_REQUEST[$name]  : $def;
}

function getID($type){
	$file_name = $type.'_ids';
	if (!file_exists($file_name))
	{
		touch ($file_name);
		$handle = fopen ($file_name,'r+');
		$id = 0;
	}
	else
	{
		$handle = fopen ($file_name,'r+');
		$id = fread ($handle,filesize($file_name));
		settype ($id,"integer");
	}
	rewind ($handle);
	fwrite ($handle,++$id);
	
	fclose ($handle);
	return $id;
}

//portions of code from https://www.youtube.com/watch?v=jgoWGXOsoWE&feature=emb_logo
# gets an array of table names, gets all data from tables
# creates an sql file that drops tables, creates tables and then inserts values. 
function generateDatabaseBackup($con){
		$tables = array();
		$result = mysqli_query($con,"SHOW TABLES");
		while($row = mysqli_fetch_row($result)){
			$tables[] = $row[0];
		}
		$return = '';
		foreach($tables as $table){
			$result = mysqli_query($con,"SELECT * FROM ".$table);
			$num_fields = mysqli_num_fields($result);
		  
			$return .= 'DROP TABLE '.$table.';';
			$row2 = mysqli_fetch_row(mysqli_query($con,"SHOW CREATE TABLE ".$table));
			$return .= "\n".$row2[1].";\n";
		  
			for($i=0;$i<$num_fields;$i++){
				while($row = mysqli_fetch_row($result)){
					$return .= "INSERT INTO ".$table." VALUES(";
					for($j=0;$j<$num_fields;$j++){
						$row[$j] = addslashes($row[$j]);
						if(isset($row[$j])){ $return .= '"'.$row[$j].'"';}
						else{ $return .= '""';}
						if($j<$num_fields-1){ $return .= ',';}
					}
				$return .= ");\n";
				}
			}
			$return .= "\n";
		}
		
		$filename = "backup".DS.getID('db').".sql";
		$handle = fopen($filename,"w+");
		fwrite($handle,$return);
		fclose($handle);
		return $filename;
}

#takes an sql file and splits it into individual queries by semicolons and then runs each
function restoreDatabase($con,$filename){
	$handle = fopen($filename,"r+");
	$contents = fread($handle,filesize($filename));
	$sql = explode(';',$contents);
	foreach($sql as $query){
		$result = mysqli_query($con,$query);
	}
	fclose($handle);
}

?><?php
$source_links [] = "<br><a href='/folder_view/vs.php?s=" . __FILE__ . "' target='_blank'>View Source of ". __FILE__;
?>