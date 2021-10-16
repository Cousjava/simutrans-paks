<?php
  $title='Objekt-Manager';
  include("./tpl_script/header.php");
  //painters and admins can browse objects
  $minimal_user_level=array('admin','gu','painter','pakadmin');
  if ( !isset($_SESSION['role']) or  !compare_userlevels($minimal_user_level, $_SESSION['role'])) 
  { include("./tpl_script/main.php");
    include('./tpl_script/footer.php');
    die();
  }

  // setadmin link liste
  include('./tpl_script/setadmin_links.php');

 
 /*
// update status set
if ( isset($_GET['set']) && in_array($_GET['set'],  $maintainter) ) { 
  if ( $_GET['wert'] == 'enabled' ) { $t = 1; } elseif ( $_GET['wert'] == 'disabled' ) { $t = 0; }  
  $query="UPDATE `versions` SET `activ`='".$t."' WHERE `version_id`=".$_GET['set'].";";
  $result = db_query($query);
        $_GET['set'] = Null;
} 

if ( isset($_POST['set_lang']) ) {
    $sql="SELECT `language_id`,`language_name` FROM `languages` ORDER BY `language_id`;";
    $query = db_query($sql);
    $langlist = array();
    while ($row2=db_fetch_array($query)) {
      if ($_POST['translates_'.$row2['language_id']]) { 
         $langlist[$row2['language_id']] = $row2['language_id']; 
      }
    }
    
    for ( $x = 0; $x < count($maintainter); $x++ ) {
          if ($_POST['modifid_'.$maintainter[$x]]) { $setid = $maintainter[$x]; }
    }
    $setlang = implode("|", $langlist);
    $sql= sprintf ("UPDATE `versions` SET `lng_disabled`='%s' WHERE `version_id`='%s';"
      ,$setlang
      ,$setid 
      );
    db_query($sql);
} 

*/

foreach ($maintainter as $id)
{   if ( isset($versions_all[$id]))
        echo '<nobr>< <a href="main.php?lang=de&page=admin_set&set='.$id.'">'.$LNG_ADMIN[40].': <b>'.$versions_all[$id].'</b></a> ></nobr></br></br>';
     else 
      { echo '<nobr>< <a href="main.php?lang=de&page=admin_set&set='.$id.'"><font color="Red">'.$LNG_ADMIN[40].' '.$LNG_INFO[6].': <b>';
        echo db_one_field_query ("SELECT v_name FROM versions WHERE version_id=$id;");
        echo '   </b></a> ></font></nobr></br></br>';
      }
}
/*
$version_sql = "SELECT * FROM `versions`";
$db_result = db_query($version_sql);
while ($row=db_fetch_array($db_result))
{
  if ( in_array($row['version_id'],  $maintainter) ) {
     if ( $row['activ'] == 1 ) {
    echo '<nobr>< <b>'.$row['v_name'].'</b> <font color="Green">'.$LNG_MANAGE[0].'</font> <a href="?set='.$row['version_id'].'&wert=disabled">'.$LNG_MANAGE[1].'</a> ></nobr><br>';
      set_lng_disabled($row['lng_disabled'], $row['version_id']);
     } else {
          echo '<nobr>< <b>'.$row['v_name'].'</b> <a href="?set='.$row['version_id'].'&wert=enabled">'.$LNG_MANAGE[0].'</a> <font color="Red">'.$LNG_MANAGE[1].'</font> ></nobr><br>';              
     }
  
  echo '<nobr>< <a href="main.php?lang=de&page=admin_set&set='.$row['version_id'].'">'.$LNG_ADMIN[40].' <b>'.$row['v_name'].'</b></a> ></nobr></br></br>';

  }
}
db_free_result($db_result);
*/

/*
function set_lng_disabled($langs_dis, $set_id) {
 global $LNG_FORM, $LNG_LANGUAGE, $LNG_MANAGE;
 echo '<form enctype="multipart/form-data" action="obj_index.php" method="POST">';
 echo '<i><b>'.$LNG_MANAGE[18].'</b></i><br>';
 $x = 0;
  $sql="SELECT `language_id`,`language_name` FROM `languages` ORDER BY `language_name` ASC;";
  $query = db_query($sql);
  while ($row2=db_fetch_array($query)) {
    if ( empty($langs_dis) ) {
      $qry2 = array();
    } else {
      $qry2 = explode("|", $langs_dis);
    }
    $x++;
    if ( $x == 7 ) { $br='<br>'; $x = 1; } else { $br=' '; }
    printf ('%s<span style="white-space:nowrap;"><input type="checkbox" name="translates_%s"%s>%s (%s)</span>&nbsp;'
      ,$br
      ,$row2['language_id']
      ,(in_array($row2['language_id'], $qry2)?" CHECKED":"")
      ,$row2['language_id']
      ,$LNG_LANGUAGE[$row2['language_id']]
    );
  }
 echo '<br><input type="submit" name="modifid_'.$set_id.'" value="'.$LNG_FORM[42].'">';
 echo '<br><input type="hidden" name="set_lang" value="true">';
 echo '</form>';
} */


  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('./tpl/stats_menu.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['page_title'] =  $page_titel['stats_menu'];

  $v_att['subtitel'] = $LNG_STATS[1];

  // object browser
  $x = 0;
  $v_att['menulist'][$x]['link_file'] = 'obj_browser.php';
  $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[3];
  $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[4];

  // create / new object
  $x++;
  $v_att['menulist'][$x]['link_file'] = 'new_object.php';
  $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[19];
  $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[20];

  // object import
  $x++;
  $v_att['menulist'][$x]['link_file'] = 'obj_import.php';
  $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[7];
  $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[8];

  // file upload
  $x++;
  $v_att['menulist'][$x]['link_file'] = 'file_upload.php';
  $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[21];
  $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[22];

  $u_level = array('admin','pakadmin');
  if (compare_userlevels($u_level,$_SESSION['role']))
  { 
    // show Logs
    $x++;
    $v_att['menulist'][$x]['link_file'] = 'logs.php';
    $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[17];
    $v_att['menulist'][$x]['menu_description'] = "";

    //  delete object
    $x++;
    $v_att['menulist'][$x]['link_file'] = 'obj_export_menu.php';
    $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[13];
    $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[14];

    //  delete temp files
    $x++;
    $v_att['menulist'][$x]['link_file'] = 'obj_cvs_import.php';
    $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[9];
    $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[10];

    //  delete object
    $x++;
    $v_att['menulist'][$x]['link_file'] = 'obj_purge.php';
    $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[11];
    $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[12];

    //  delete temp files
    $x++;
    $v_att['menulist'][$x]['link_file'] = 'clean_temp.php';
    $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[15];
    $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[16];
  }
  

  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;

  include("./tpl_script/footer.php");
?>



