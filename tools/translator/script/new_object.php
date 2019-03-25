<?PHP
$title="ObjectCreator";
include ("./tpl_script/header.php");
$user=$_SESSION['userId'];
$u_level=array("admin", "pakadmin");

if ( !isset($_SESSION['role']) or  !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

// setadmin link liste
include('./tpl_script/setadmin_links.php');

include ('./include/obj.php');
include ('./include/translations.php');


$tr_ok_tab = array();;

// ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- // 

  $version_auswahl = 255;
  //if ( !isset($_GET['vers']) ) { $version_auswahl = 255; }
  if ( isset($_POST['version']) && ($_POST['version'] != 255) ) { 
    $version_auswahl = intval($_POST['version']);
  } 
  
  $obj_type = 255;
  //if ( !isset($_GET['obj']) ) { $obj_auswahl = 255; }
  if ( isset($_POST['obj']) && ($_POST['obj'] != 255) ) { 
    $obj_type = trim($_POST['obj']);
  }  
  
  if ( isset($_POST['obj_name']) && ($_POST['obj_name'] != 255) ) { 
    $obj_name = trim($_POST['obj_name'], "\x00..\x1F");
  }  

  if ( isset($_POST['note_text']) && ($_POST['note_text'] != 255) ) { 
    $note_text = trim($_POST['note_text']);
  }  

  $set_admin = get_maintainter(); 

  // insert object in db     
  if ( isset($_POST['save_object']) && $_POST['save_object'] == $LNG_ADMIN[53] ) 
  { // test exist object name in set
    $sqlobj = sprintf ("SELECT * FROM objects WHERE obj_name='%s' AND version_version_id=%d ;",
                         db_real_escape_string($obj_name) , $version_auswahl);
    $result = db_query($sqlobj);
    $objalt = db_fetch_object($result);

    if ($objalt === null)  // insert object to object table
    { $sql_insert_obj = "INSERT INTO objects (obj_name, version_version_id, obj, mod_date, note)".
                           " VALUES ('" . db_real_escape_string($obj_name) . "', ".$version_auswahl.", '" .
                                          db_real_escape_string($obj_type) . "', NULL, '" .
                                          db_real_escape_string($note_text). "')";
      db_query($sql_insert_obj);
      $object_id = db_insert_id();    

      if ( $object_id > 0 ) { 
        tr_update_obj_id($obj_name,$version_auswahl,$object_id);
        // update log create object
        // Date and Time | User | Object type | Object name | Language | action | Object id
        $t = date("Y-m-d H:i:s", time());
        $data = $t."|".$user."|".$obj_type."|".$obj_name."||create object|".$object_id."\n";
        write_log($version_auswahl, $data);

        foreach ($_POST as $key => $value) 
        { if (substr($key,0,7)=='trtext_')
          { $tr_ok_tab[] = tr_update($object_id,$value,"take_from_obj_id",substr($key,7),1,'t','c');
          }
        }
        $tr_ok_tab[] = 1; // successful written
        ob_export_object_list($version_auswahl,$datapfad.'set_'.$version_auswahl.'_objectlist.txt');
      } else $tr_ok_tab[] = 35; // Database error
    } else   $tr_ok_tab[] = 38; //obj_exists; 
  }

  include("./tpl_script/new_text_object.php");

  include("./tpl_script/footer.php");
?>
