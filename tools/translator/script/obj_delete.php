<?php
    //header, no output before this (sends header information)
$title="Object - Confirm delete";
include  ("tpl_script/header.php");
include ('./include/images.php');
  //list of all user typs allowed on this page
  //header will block access for all other users
$user=$_SESSION['userId'];
$u_level=array('admin', 'pakadmin');

if ( !isset($_SESSION['role']) or  !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 


// ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- //

define ("form_obj_delete", '<form enctype="multipart/form-data" action="obj_delete.php" method="POST">
 <input type="hidden" name="obj_id" value="%s">
 <b>Object name:</b> %s <br>
 <b>Version:</b> %s <br>
 <br>%s<br><br>
 <input type="submit" name="confirm" value="%s"> 
</form>');

// ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- //



function obj_delete($obj_id,$confirm) 
{ global $versions_all,$user,$LNG_OBJ_BROWSER;

  $sql = sprintf ("SELECT * FROM objects where object_id=%s", $obj_id);
  $query = db_query($sql);
  $row=db_fetch_array($query);
  if($row == false) 
  { printf ("<h2>".$LNG_OBJ_BROWSER[9]."!!</h2>",$obj_id);
    return;
  }
  if ($obj_id != $row['object_id']) die("pgm error");
  $name    = $row['obj_name'];
  $ver     = $row['version_version_id'];
  $obj_typ = $row['obj'];
  $ver_name = $versions_all[$ver];
  if (!in_array($user, get_setmaintainter($ver)))
  { echo "<h2>You are not the version maintainer!<br>Only maintainer is allowed to perform this operation for set: " . $ver_name . ".</h2>";
    return;
  }
  if ($confirm != 'Delete object')
  {    printf (form_obj_delete
      ,$obj_id
      ,$name
      ,$ver_name
      ,display_image_tile($row['object_id'], $ver)
      ,'Delete object'
    );
    return;
  }
  db_free_result($query);

  // update log
  $t = date("Y-m-d H:i:s", time());
  $log_o_name = str_replace("|", '!',$name);
  // Date and Time | User | Object type | Object name | Language | Message | Object Id
  $data = $t."|".$user."|".$obj_typ."|".$log_o_name."||object delete|".$obj_id."\n";
  write_log($ver, $data);

  echo "Deleting object: ".$name." Version: ".$ver_name."<br>\n";
    
    // Objekt löschen
    $sql = sprintf ("DELETE FROM objects
      where object_id='%s' and version_version_id=%d;", $obj_id, $ver );
    $query = db_query($sql);
    $recs=db_affected_rows();
     
    // Parameter löschen
    $sqldel = sprintf ("DELETE FROM property ".
      "where having_obj_id='%s' and having_version_version_id=%d;", $obj_id, $ver );
    $query = db_query($sqldel);
    $props=db_affected_rows();
    
    // Grafiken löschen
    $tab = 'images_'.$ver;
    $sqldel = "DELETE FROM `$tab` WHERE `object_obj_id`=".$obj_id." AND `object_version_version_id`=".$ver.";";
    $query = db_query($sqldel);
    $imgs=db_affected_rows();
    
    // Texte löschen
    $tab = 'translations_'.$ver;
    $sql = sprintf ("DELETE FROM `".$tab."` where object_object_id='%s' and object_version_version_id=%d;", $obj_id, $ver );
    $query = db_query($sql);
    $trans=db_affected_rows();
    echo "<p></p>OK. Deleted ".$recs." objects, ".$props." properties, ".$imgs." images,  ".$trans." translations.\n";
    echo '<HR><a href="obj_browser.php">Return to Object Browser<br>'."\n";
}

if     (isset($_POST['obj_id']) and intval($_POST['obj_id']) > 0) obj_delete(intval($_POST['obj_id']),$_POST['confirm']);
elseif (isset($_GET['obj_id'])  and intval($_GET['obj_id']) > 0)  obj_delete(intval($_GET['obj_id']),"need");

// ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- //
  include("tpl_script/footer.php");
?>

