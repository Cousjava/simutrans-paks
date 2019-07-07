<?php

/* global object data */
function ob_read($obj_id)
{
  if (!is_numeric($obj_id) or $obj_id <= 0) die("ob_read object_id error");
  $reg = "SELECT * FROM `objects` WHERE `object_id`='".intval($obj_id)."'";
    //echo $reg;
  $err = db_query($reg);
  return(db_fetch_object($err));
}

function ob_read_by_name($version,$obj_name)
{ global $st_dbi;
  $object_exists = 0;
  $obj_id = 0;
  $obj_st = mysqli_prepare($st_dbi, "SELECT object_id, obj_name FROM objects WHERE obj_name=? AND version_version_id=?");
       if ($obj_st === false) echo "db_error".mysqli_error($st_dbi);
            mysqli_stmt_bind_param   ($obj_st,'si',$obj_name,$version);
       if (!mysqli_stmt_execute      ($obj_st)) exit(mysqli_stmt_error($obj_st));
            mysqli_stmt_bind_result  ($obj_st, $obj_id,$db_obj_name);
       if  (mysqli_stmt_fetch        ($obj_st)) $object_exists = 1;
       if  (mysqli_stmt_more_results ($obj_st)) die ("database error duplicate object".$obj_name);
            mysqli_stmt_close        ($obj_st);
  if ($db_obj_name != $obj_name) $obj_id = -33; // Incons. Db: Object Name differs internally
  if ($object_exists != 1) $obj_id = 0;
  return $obj_id;
}

/*  save object parameter in table objects */
/* return  30 = object id is wrong or missing 
           31 = objekt not found 
           24 = object is unchanged
           10 = successful saved
           35 = SQL Error: update failure not written
           37 = Databas Error: entry double
           38 = Object exist already
           39 = User does not have sufficient write permissions!
     all errors found in load3  as is handeled in  tr_translate_text             */
function ob_save_obj_parameter( $update_id, $new_name, $new_type,$new_note,$new_copr) 
{ if (!is_numeric($update_id) or $update_id <= 0) return 30;
  $u_level=array("admin", "pakadmin");
  if ( !isset($_SESSION['role']) or !compare_userlevels($u_level, $_SESSION['role'])) return 39;

  $errg= ob_read($update_id);
  if ($errg === NULL ) return 31; // notfound
  if ($errg->object_id != $update_id) return 30; // bug

  if ($new_note === NULL) $new_note = $errg->note;
  if ($new_copr === NULL) $new_copr = $errg->obj_copyright;
  
  $version_id   = $errg->version_version_id;
  $current_name = $errg->obj_name;

  if ($version_id != BASE_TEXTS_SET_ID and
      $version_id != EXTE_TEXTS_SET_ID) $new_name = trim($new_name);
  $new_type = trim($new_type);
  $new_note = trim($new_note);
  $new_copr = trim($new_copr);
  
  if ($errg->obj_name      == $new_name and
      $errg->obj           == $new_type and
      $errg->note          == $new_note and
      $errg->obj_copyright == $new_copr) return 24; // unchanged
      
  if ( $current_name != $new_name and ob_read_by_name($version_id,$new_name) > 0) return 38; // exist already

  $log_text = "object edit";

  $update_name = db_real_escape_string($new_name);

  $sql_update_obj = "UPDATE `objects` SET `obj_name`='"     .$update_name.
                                      "', `obj`='"          .db_real_escape_string($new_type).
                                      "', `note`='"         .db_real_escape_string($new_note).
                                      "', `obj_copyright`='".db_real_escape_string($new_copr).
                                 "' WHERE `object_id`=".$update_id.";";
  $save_res = db_query($sql_update_obj); 

  if (!$save_res) return 35;
  $c = db_affected_rows(); if ($c != 1) return 37;

  if ( $current_name != $new_name)
  { $log_text = "object rename";
    $tab = 'translations_'.$version_id;
   // update translations table
    $qy = "UPDATE `".$tab."` SET `object_obj_name`='".$update_name."' WHERE `object_object_id`=".$update_id.";";
    $result = db_query($qy);
   // update property table
    $qy = "UPDATE `property` SET `having_obj_name`='".$update_name."' WHERE `having_obj_id`=".$update_id.";";
    $result = db_query($qy);
   // update images table  
    $tab = 'images_'.$version_id;               
    $qy = "UPDATE `".$tab."` SET `object_obj_name`='".$update_name."' WHERE `object_obj_id`=".$update_id.";";
    $result = db_query($qy);
  }
  // update log
  $t = date("Y-m-d H:i:s", time());
  $user = $_SESSION['userId'];
  // Date and Time | User | Object type | Object name | Language | Message | Object Id
  $data = $t."|".$user."|".$new_type."|".$new_name."||".$log_text."|".$update_id."\n";
  write_log($version_id, $data);

  return 10;
}

/* 
  save object propertys in table objects 
  
  save_obj_property(set_id, obj_id, obj_name, obj_type, property_array)
*/
function ob_save_obj_property($update_id, $p_name, $p_value) 
{ if (!is_numeric($update_id) or $update_id <= 0) return 30; 
  $u_level=array("admin", "pakadmin");
  if ( !isset($_SESSION['role']) or !compare_userlevels($u_level, $_SESSION['role'])) return 39;

  $errg= ob_read($update_id);
  if ($errg === NULL ) return 31; // notfound
  if ($errg->object_id != $update_id) return 30; // bug
  $version_id = $errg->version_version_id;
  $obj_name   = $errg->obj_name;
  $obj_type   = $errg->obj;
  
  $p_name  = trim(hex2bin($p_name)); // send $p_name with bin2hex because constraint[prev][0] -> php read this as array 
  $p_value = trim($p_value);

  $sql = "SELECT property_id,p_name, p_value FROM property WHERE having_obj_id=$update_id AND p_name='".db_real_escape_string($p_name)."'";
  $query = db_query($sql);
  $row=db_fetch_array($query);
  if ($row === NULL ) return 31; // notfound
  if ( $row['p_value'] != $p_value ) 
  { $qy = "UPDATE property SET p_value='".db_real_escape_string($p_value)."' WHERE property_id='".$row['property_id']."'";
    $result = db_query($qy);
  } else return 24; // unchanged
  if (!$result) return 35;
  $c = db_affected_rows(); if ($c != 1) return 37;

  db_free_result($query);
  // update log
  $log_text = "object property";
  $t = date("Y-m-d H:i:s", time());
  $user = $_SESSION['userId'];
  // Date and Time | User | Object type | Object name | Language | Message | Object Id
  $data = $t."|".$user."|".$obj_type."|".$obj_name."||".$log_text."|".$update_id."\n";
  write_log($version_id, $data);
  return 10;
}

// export all objects to txt file
function ob_export_object_list($vid,$file_name)
{ 
  $sql = "SELECT `object_id`, `obj_name`, `obj`, `note` FROM `objects` WHERE `version_version_id`=".$vid." ORDER BY LOWER(`obj`),`obj_name` COLLATE utf8_unicode_ci";
  $query = db_query($sql);

  $ausgabe = "";
  while ($row=db_fetch_array($query)) 
  {  $ausgabe .= "obj="  . $row['obj'] . "\n";
     $ausgabe .= "name=" . $row['obj_name'] . "\n";
     $ausgabe .= "note=" . $row['note'] . "\n";
     $ausgabe .= "-\n";
  }
  db_free_result($query);

  $fp=fopen($file_name,"wb");    
  fwrite($fp, $ausgabe);
  fclose($fp); 
}


?>
