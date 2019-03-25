<?php
include ('./include/images.php');
include ('./include/select.php');

$title='Objekt-Browser';
include ("tpl_script/header.php");
$user=$_SESSION['userId'];
//painters and admins can browse objects
$u_level=array('admin','gu','painter','pakadmin');

if ( !isset($_SESSION['role']) or !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

// setadmin link liste
include('./tpl_script/setadmin_links.php');

function htmllink ($link,$title,$class='') {
  if (!empty($class)) { $class=" class=\"$class\""; }
  return sprintf ('<a href="%s"%s>%s</a>',$link,$class,$title);
}

function obj_info ($obj_id)
{ GLOBAL $user,$versions_all, $object_text, $LNG_OBJ_BROWSER,$LNG_LOAD3,$LNG_MANAGE;

  echo "<HR>";
  echo "<h2>$LNG_OBJ_BROWSER[2]</h2>";
  if (empty($obj_id)) 
  { echo $LNG_OBJ_BROWSER[3];
    return;
  }
  $sql = sprintf ("SELECT * FROM objects WHERE object_id=%s", $obj_id);

//echo $sql;
  $query = db_query($sql);
  if ($row=db_fetch_array($query)) 
  {
    printf ( '<form enctype="multipart/form-data" action="obj_edit.php?obj_id=%s" method="post">',$row['object_id'] );
    printf ( '<input type="hidden" name="obj_id" value="%s">',$row['object_id']);

    echo table_head;
    $tr="";
    $tr.=sprintf ( table_field, $LNG_LOAD3[6] );
    $obj_name_line='<b>'.htmlentities($row['obj_name'], ENT_QUOTES).'</b>';

    $obj_name_line .= sprintf(' (<A href="edit.php?obj_id=%s" target="_blank">%s</A>)'
        ,$row['object_id']
        ,$LNG_OBJ_BROWSER[1]);

    if ( in_array($user, get_setmaintainter($row['version_version_id'])) )
    { $obj_name_line .= ' ('.htmllink("obj_edit.php?obj_id=".$row['object_id'],$LNG_MANAGE[5]).')';
      $obj_name_line .= ' ('.htmllink("obj_delete.php?obj_id=".$row['object_id'],$LNG_OBJ_BROWSER[4]).')';
    } 

    $tr.=sprintf ( table_field, $obj_name_line );
    printf ( table_line, $tr ); $tr="";


// copyright - set 0 = note
    if ( in_array($row['obj'], $object_text) ) {
        $tr.=sprintf ( table_field, $LNG_OBJ_BROWSER[12] );
        if ( $row['note'] == '' ) { $row['note'] = "&nbsp;"; }
        $tr.=sprintf ( table_field, $row['note'] );
    } else  { 
        $tr.=sprintf ( table_field, $LNG_OBJ_BROWSER[13] );
        if ( $row['obj_copyright'] == '' ) { $row['obj_copyright'] = "&nbsp;"; }
        $tr.=sprintf ( table_field, $row['obj_copyright'] );
    }
    printf ( table_line, $tr ); $tr="";
   
    $tr.=sprintf ( table_field, $LNG_OBJ_BROWSER[11] );
    
    $tr.=sprintf ( table_field, $row['version_version_id']." - ".$versions_all[$row['version_version_id']] );
    printf ( table_line, $tr ); $tr="";
    $tr.=sprintf ( table_field, $LNG_OBJ_BROWSER[7] );
    $tr.=sprintf ( table_field, $row['obj'] );
    printf ( table_line, $tr ); $tr="";

    $show_img = 1;
    if ( $row['version_version_id'] == 0 )     $show_img = 0;
    if ( in_array($row['obj'], $object_text) ) $show_img = 0;

    if ( $show_img == 1 ) 
    { $tr.=sprintf ( table_field, $LNG_OBJ_BROWSER[8] );
      // Grafiken auslesen
      $preview = display_image_tile($row['object_id'], $row['version_version_id']);
      $tr.=sprintf ( table_field, $preview );
      printf ( table_line, $tr ); $tr="";
    }

    $sql = "SELECT p_name, p_value FROM property WHERE having_obj_id=$obj_id ORDER BY p_name";
    $query2 = db_query($sql);
    while ($row2=db_fetch_array($query2)) {
      $tr.=sprintf ( table_field, $row2['p_name'] );
      $tr.=sprintf ( table_field, $row2['p_value'] );
      printf ( table_line, $tr ); $tr="";
    }
    db_free_result($query2);

    echo table_foot;
    echo '</form>';

  } else {
    echo sprintf($LNG_OBJ_BROWSER[9],$obj_id);
    echo sprintf("sql='$sql'<br>db_num_rows=%d" ,db_num_rows($query));
  }
  db_free_result($query);
}

/* ----------------------------------------------------------------- */
/* Main Program                                                      */
/* ----------------------------------------------------------------- */

define ("table_head", '<table class="top" cellpadding="5" cellspacing="0" border="1">');
define ("table_head2", '<table cellpadding="0" cellspacing="0" border="0">');
define ("table_line", '<tr class="top">%s</tr>');
define ("table_field", '<td class="top" align="left">%s</td>');
define ("table_field2", '<td %s>%s</td>');
define ("table_foot", '</table>');

if (isset($_GET['obj_id'])) obj_info(intval($_GET['obj_id']));




  include("tpl_script/footer.php");
?>
