<?php
	error_reporting(0);


   	          /**================================================================================================**/
	         /**================================================================================================**/
	        //*   											   				   				   			       *//
	       //*               			Author 		 : Muhammad Zainuddin Ansori							  *//
	      //*               			Email 		 : m.zainuddin.a@gmail.com								 *//
	     //*   							Date Release : 05 May 2017										    *//
	    //*   							Title 		 : API Generator									   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/

	

	/* START CATCH DATA TO VARIABLE GLOBAL*/
	parse_str(file_get_contents('php://input'), $params);
	$GLOBALS["_PUT"] 	= $params;
	$GLOBALS["_DELETE"] = $params;
	/* END PUT DATA TO VARIABLE GLOBAL*/



	/* START SETTING */
	$host			= 'localhost';
	$username		= 'root';
	$password		= '';
	$db_name		= 'api';
	$table 			= 'users';
	$key 			= ''; /* PRIMARY KEY or INDEX of the table or leave it BLANK for automatic selection */
	$query_type 	= ''; /* join or manual or leave BLANK for default*/
	$db 			= mysqli_connect($host, $username, $password, $db_name);
	/* END SETTING */



	/* START FUNCTION TO PROTECT PARAMETER */
	function protect($value='')
	{
		return mysql_escape_string(strip_tags(stripslashes(trim($value))));
	}
	/* END FUNCTION TO PROTECT PARAMETER */

	

	/* START QUERY TYPE JOIN */
	$join_table = 'messages,users';
	$join_table	= explode(",", $join_table);
	$join_key	= 'uid_fk,uid';
	$join_key	= explode(",", $join_key);
	$query_join = '';
	if (!empty($join_key)) {
		$query_join		= "SELECT * FROM ".$join_table[0]." a LEFT JOIN ".$join_table[1]." b ON a.".$join_key[0]."=b.".$join_key[1]." WHERE 1=1";
	}
	/* END QUERY TYPE JOIN */



   	        /**================================================================================================**/
	       /**================================================================================================**/
	      //*  											   				   				   				     *//
	     //*               		Access API with parameter q											    	*//
	    //*   					Example = http://localhost/api.php?q=SELECT * FROM users 				   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/
	/* START QUERY MANUAL */
	$query_manual = '';
	if (isset($_GET['q']) && $_GET['q']!='') {
		$query_manual = str_replace("\\", "", urldecode(protect($_GET['q'])));
		//echo $query_manual;
	}
	/* END QUERY MANUAL */



	/* START GET THE TABLE FROM PARAMETER */
	if (isset($_GET['table']) && $_GET['table']!='') {
		$table = protect($_GET['table']);
	}
	/* END GET THE TABLE FROM PARAMETER */



	/* START GET QUERY */
	$query 		= "SELECT * FROM ".$table." WHERE 1=1";
	if ($query_join=='') {
		$query_join = $query;
	} else if ($query_manual=='') {
		$query_manual = $query;
	}
	/* END GET QUERY */



	/* START SWITCH QUERY TYPE */
	switch ($query_type) {
		case 'join':
			$query_type = $query_join;
			break;

		case 'manual':
			$query_type = $query_manual;
			break;
		
		default:
			$query_type = $query;
			break;
	}
	/* END SWITCH QUERY TYPE */



	/* START GET PRIMARY KEY FROM TABLE */
	$q_key_table = mysqli_query($db, "SHOW INDEX FROM $table");
	//$q_key_table = mysqli_query($db, "SHOW KEYS FROM $table WHERE Key_name = 'PRIMARY'");
	$key_table = mysqli_fetch_array($q_key_table);
	if ($key=='') {
		$key = $key_table['Column_name'];
	}
	/* END GET PRIMARY KEY FROM TABLE */



   	        /**================================================================================================**/
	       /**================================================================================================**/
	      //*  											   				   				   				     *//
	     //*               			Direct access to api without parameter								    *//
	    //*   						Example = http://localhost/api.php 									   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/
	/* START GET ALL */
	if (!isset($_GET['id']) && !isset($_GET['search'])) {
		$q = mysqli_query($db, $query_type);
		$c = mysqli_num_rows($q);
		if ($c>0) {
			while($row = mysqli_fetch_object($q))
				$data[] = $row;
				//$rows[] = $row;
		} else {
			$data['status'] = 'error';
			$data['message'] = 'Not found!';
		}
		print_r(json_encode($data));
	}
	/* END GET ALL */



   	        /**================================================================================================**/
	       /**================================================================================================**/
	      //*  											   				   				   				     *//
	     //*               			Access to api with method POST and parameter						    *//
	    //*   						Example Base Url = http://localhost/api.php 						   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/
	/* START INSERT */
	if ($_POST) {
		$q = mysqli_query($db, $query);
		$a = '';
		$b = '';
		$coba = '';
		$condition = '';
		$update_data = '';
		while($data = mysqli_fetch_field($q)){
			if ($data->name!=$key) {
				$a .= $data->name.",";
				$b .= "'".@$_POST[$data->name]."',";
				$update_data .= $data->name."='".@$_PUT[$data->name]."',";
				$condition .= isset($_POST[$data->name]) && !empty($_POST[$data->name])." && ";
			}
		}
		$update_data = trim(rtrim($update_data,","));
		if (trim(rtrim($condition, " && "))) {
			$r = "INSERT INTO $table (".rtrim($a,",").") VALUES (".rtrim($b,",").")";
			$execute = mysqli_query($db, $r);
			if ($execute) {
				$result['status'] = 'success';
			} else {
				$result['status'] = 'error';
				$result['message'] = 'Failed!';
			}
			print_r(json_encode($result));
		}
	}
	/* END INSERT */



	        /**================================================================================================**/
	       /**================================================================================================**/
	      //*  											   				   				   				     *//
	     //*               			Access to api with method GET and parameter 						    *//
	    //*   		Example = http://localhost/api.php?table=user&type=equal&field=name&search=admin 	   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/
	/* START GET BY FIELD AND KEY */
	if (isset($_GET['type']) && !empty($_GET['type']) && isset($_GET['field']) && !empty($_GET['field']) && isset($_GET['search']) && !empty($_GET['search'])) {
		$type = protect($_GET['type']);
		$field = protect($_GET['field']);
		$search = protect($_GET['search']);
		switch ($type) {
			case 'equal':
				$q_r = " = '$search'";
				break;
			case 'like':
				$q_r = " LIKE '%$search%'";
				break;
		}
		$q = mysqli_query($db, $query." AND ".$field.$q_r);
		$c = mysqli_num_rows($q);
		if ($c==1) {
			$data = mysqli_fetch_object($q);
			//$data->status = 'success';
		} else {
			$data['status'] = 'error';
			$data['message'] = 'Not found!';
		}
		
		print_r(json_encode($data));
	}
	/* END GET BY FIELD AND KEY */



	        /**================================================================================================**/
	       /**================================================================================================**/
	      //*  											   				   				   				     *//
	     //*               			Access to api with method GET and parameter ID						    *//
	    //*   							Example = http://localhost/api.php?id=1 						   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/
	/* START GET BY ID */
	if (isset($_GET['id']) && !empty($_GET['id'])) {
		$id = protect($_GET['id']);
		$q = mysqli_query($db, $query." AND ".$key."='$id'");
		$c = mysqli_num_rows($q);
		if ($c==1) {
			$data = mysqli_fetch_object($q);
			//$data->status = 'success';
		} else {
			$data['status'] = 'error';
			$data['message'] = 'Not found!';
		}
		
		print_r(json_encode($data));
	}
	/* END GET BY ID */



	        /**================================================================================================**/
	       /**================================================================================================**/
	      //*  											   				   				   				     *//
	     //*               			Access to api with method DELETE and parameter ID					    *//
	    //*   						Example Base Url = http://localhost/api.php 						   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/
	/* START DELETE */
	if (isset($_DELETE['id_delete']) && $_DELETE['id_delete']!='') {
		$id = $_DELETE['id_delete'];
		$check = mysqli_query($db, "SELECT * FROM $table WHERE $key='$id'");
		$c = mysqli_num_rows($check);
		if ($c==1) {
			$execute = mysqli_query($db, "DELETE FROM $table WHERE $key='$id'");
			if ($execute) {
				$result['status'] = 'success';
			} else {
				$result['status'] = 'error';
				$result['message'] = 'Failed!';
			}
			print_r(json_encode($result));
		} else {
			$result['status'] = 'error';
			$result['message'] = 'Not found!';
			print_r(json_encode($result));
		}
	}
	/* END DELETE */



	        /**================================================================================================**/
	       /**================================================================================================**/
	      //*  											   				   				   				     *//
	     //*               			Access to api with method PUT and parameter 						    *//
	    //*   						Example Base Url = http://localhost/api.php 						   *//
	   //*  																							  *//
	  /**================================================================================================**/
     /**================================================================================================**/
	/* START UPDATE */
	if (isset($_PUT['id_update']) && $_PUT['id_update']!='') {
		$id = $_PUT['id_update'];
		$check = mysqli_query($db, "SELECT * FROM $table WHERE $key='$id'");
		$c = mysqli_num_rows($check);
		if ($c==1) {
			$execute = mysqli_query($db, "UPDATE $table SET $update_data WHERE $key='$id'");
			if ($execute) {
				$result['status'] = 'success';
			} else {
				$result['status'] = 'error';
				$result['message'] = 'Failed!';
			}
			print_r(json_encode($result));
		} else {
			$result['status'] = 'error';
			$result['message'] = 'Not found!';
			print_r(json_encode($result));
		}
	}
	/* END UPDATE */



?>