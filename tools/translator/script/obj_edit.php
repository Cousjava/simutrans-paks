<?php
include ('./include/images.php');
include ('./include/translations.php');
include ('./include/select.php');
include ('./include/obj.php');

$title="Object Editor";
include ("tpl_script/header.php");
$u_level=array("admin", "pakadmin");

if ( !isset($_SESSION['role']) or !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

// setadmin link liste
include('./tpl_script/setadmin_links.php');

$tr_ok = array();;

if ( isset($_POST['save']) and  isset($_POST['obj_id']))
{ $update_id = intval($_POST['obj_id']);
  if($_POST['save'] == 'save object' ) 
  { $new_name = $_POST['obj_name'];
    $new_type = $_POST['obj'];
    $new_note = NULL;
    $new_copr = NULL;
    if (isset($_POST['note_text'])) $new_note = $_POST['note_text'];
    if (isset($_POST['copyright'])) $new_copr = $_POST['copyright'];
    $tr_ok[] = ob_save_obj_parameter($update_id, $new_name, $new_type,$new_note,$new_copr);
  }

  if ($_POST['save'] == 'save property' ) 
  { foreach ($_POST as $key => $p_value) 
    { if (substr($key,0,6)=='p_upd_')
     { $p_name = substr($key,6);
       $tr_ok[] = ob_save_obj_property($update_id, $p_name, $p_value);
     }
    }
  }
  foreach ($tr_ok as $ok) echo $LNG_LOAD3[$ok]."<br>\n"; 

}


function htmllink ($link,$title,$class='') {
  if (!empty($class)) { $class=" class=\"$class\""; }
  return sprintf ('<a href="%s"%s>%s</a>',$link,$class,$title);
}


function obj_info ($obj_id)
{ GLOBAL $user, $object_text, $way_type, $LNG_OBJ_BROWSER,$LNG_LOAD3,$LNG_MANAGE,$versions_all,$LANG_CONTACT,$maintainter;

  echo "<HR>";
  echo "<h2>Object details</h2>";
  $sql = sprintf ("SELECT * FROM objects WHERE object_id=%s", $obj_id);
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

    if ( in_array($row['version_version_id'],$maintainter) )
    { $obj_name_line .= ' ('.htmllink("obj_edit.php?obj_id=".$row['object_id'],$LNG_MANAGE[5]).')';
      $obj_name_line .= ' ('.htmllink("obj_delete.php?obj_id=".$row['object_id'],$LANG_CONTACT[4]).')';
    }

    $tr.=sprintf ( table_field, $obj_name_line );
    if ( $row['version_version_id'] == BASE_TEXTS_SET_ID or
         $row['version_version_id'] == EXTE_TEXTS_SET_ID)
    { $tr.=sprintf ( table_form_field3, "obj_name", $row['obj_name'] );
    } else 
    { $tr.=sprintf ( table_form_field1, $row['obj_name'], "obj_name" );
    }
    printf ( table_line, $tr ); $tr="";

    // texte = note | objects = copyright
    if ( in_array($row['obj'], $object_text) ) {
      $tr.=sprintf ( table_field, "note_text" );
      $tr.=sprintf ( table_field, $row['note'] );
      $tr.=sprintf ( table_form_field3, "note_text", $row['note'] );
    } else {
      $tr.=sprintf ( table_field, "copyright" );
      $tr.=sprintf ( table_field, $row['obj_copyright'] );
      $tr.=sprintf ( table_form_field1, $row['obj_copyright'], "copyright" );
    }
    printf ( table_line, $tr ); $tr="";
    
    // pakset
    $tr.=sprintf ( table_field, "version" );
    //$tr.=sprintf ( table_field, $pakset[1] );
    $tr.=sprintf ( table_field, $row['version_version_id']." - ".$versions_all[$row['version_version_id']] );
    $tr.=sprintf ( table_field, '&nbsp;' );
    printf ( table_line, $tr ); $tr="";
    // object
    $tr.=sprintf ( table_field, "obj" );
    $tr.=sprintf ( table_field, $row['obj'] );
    $res2 = '';
    if ( in_array($row['obj'], $object_text) ) 
    { foreach ($object_text as $ob_text)
      { if ( $ob_text == $row['obj'] ) $t = "selected='selected'"; 
        else                           $t = "";
        $res2 .= sprintf ("<option value='%s' %s>%s </option>\n"
                    ,$ob_text,$t,$ob_text);
      }
      $tr.=sprintf ( table_form_field2, 'obj', $res2 );

    } else $tr.=sprintf ( table_form_field1, $row['obj'], 'obj' );
    printf ( table_line, $tr ); $tr="";

    $show_img = 1;
    if ( $row['version_version_id'] == 0 )     $show_img = 0;
    if ( in_array($row['obj'], $object_text) ) $show_img = 0;

    if ( $show_img == 1 ) 
    { $tr.=sprintf ( table_field, $LNG_OBJ_BROWSER[8] );
      // Grafiken auslesen
      $preview = display_image_tile($row['object_id'], $row['version_version_id']);
      $tr.=sprintf ( table_field_img, $preview );
      printf ( table_line, $tr ); $tr="";
    }

    $tr.=sprintf ( table_field, '' );
    $tr.=sprintf ( table_field, '' );
    $tr.=sprintf ( table_field, '<input type="submit" name="save" value="save object" />');
    printf ( table_line, $tr ); $tr="";
    
    $sql = "SELECT p_name, p_value FROM property WHERE having_obj_id=$obj_id ORDER BY p_name";
    $query2 = db_query($sql);
    $res2 = '';
    while ($row2=db_fetch_array($query2)) 
    { $tr.=sprintf ( table_field, $row2['p_name'] );
      $tr.=sprintf ( table_field, $row2['p_value'] );
      $input_p_name = 'p_upd_'.bin2hex($row2['p_name']); // send $p_name with bin2hex because constraint[prev][0] -> php read this as array 
      // waytype    
      if ( $row2['p_name'] == 'waytype' ) 
      { for ( $x = 0; $x < count($way_type); $x++ ) 
        { if ( $way_type[$x] == $row2['p_value'] ) $t = ' selected="selected" '; 
          else                                     $t = "";
          $res2 .= sprintf ('<option value="%s" %s>%s </option>'."\n"
                    ,$way_type[$x]
                    ,$t
                    ,$way_type[$x]
                    );
        }
        $tr.=sprintf ( table_form_field2,$input_p_name, $res2 );
      } else $tr.=sprintf ( table_form_field1,$row2['p_value'], $input_p_name );
      printf ( table_line, $tr ); $tr="";
    }
    db_free_result($query2);
    
    if ( !in_array($row['obj'], $object_text) )
    {   $tr.=sprintf ( table_field, '' );
        $tr.=sprintf ( table_field, '');
        $tr.=sprintf ( table_field, '<input type="submit" name="save" value="save property" />');
        printf ( table_line, $tr ); $tr="";
    }
    echo table_foot;
    echo '</form>';

  } else 
  { echo sprintf($LNG_OBJ_BROWSER[9],$obj_id);
    echo sprintf("sql='$sql'<br>db_num_rows=%d",db_num_rows($query));
  }
  db_free_result($query);
}

define ("table_head", '<table class="top" cellpadding="5" cellspacing="0" border="1">');
define ("table_head2", '<table cellpadding="0" cellspacing="0" border="0">');
define ("table_line", '<tr class="top">%s</tr>'."\n");
define ("table_field", '<td class="top">%s</td>');
define ("table_field2", '<td %s>%s</td>');
define ("table_field_img", '<td colspan="2">%s</td>');
define ("table_foot", '</table>');

define ("table_form_field1", '<td><input type="text" value="%s" name="%s" size="80" maxlength="255"></td>');
define ("table_form_field2", '<td><select name="%s">%s</select></td>');
define ("table_form_field3", '<td><textarea name="%s" size="80" maxlength="250" rows="4" cols="80">%s</textarea></td>');

$obj_id = 0;
if (isset($_POST['obj_id'])) $obj_id = intval($_POST['obj_id']);
if (isset($_GET['obj_id']))  $obj_id = intval($_GET['obj_id']);
if ($obj_id > 0) obj_info($obj_id);


  include("tpl_script/footer.php");
?>
