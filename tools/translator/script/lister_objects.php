<?php    //determine the mode (needed for title)    $browse_only = ($_POST["browse_only"]==0)?0:((int) $_POST["browse_only"]);    $minimal_user_level=array('admin');    //avauilable to all logged users  include_once ("tpl_script/header.php");    $user=$_SESSION[userId];    $title=($browse_only==0)?$LNG_BROWSE_LIST[0]:(($browse_only==1)?$LNG_BROWSE_LIST[1]:$LNG_BROWSE_LIST[2]);    PrintTitle();// check role    $user_type = $_SESSION['role'];     if ( !compare_userlevels($minimal_user_level, $user_type) ) {  include_once ("tpl_script/footer.php");     }    //html-page formating functions    include ("./include/quotes.inc.php");//***************************** FUNCTION DECLARATIONS  $id = $_GET['id'];  $lng = $_GET['lng'];  $tab_set = 'translations_'.$id;// make text, "wraper-like" dump of all logged-user reservations// only in english (ie. just object namesfunction output(){  global $lang_id, $version, $user, $id, $lng, $tab_set;  $query= "SELECT * FROM `".$tab_set."` WHERE ".      "language_language_id='".$lng."' AND ".      "object_version_version_id=".$id." ".      "ORDER BY `object_object_id` ASC";  $query2= "SELECT * FROM `objects` WHERE ".      "version_version_id=".$id." ".      "ORDER BY `object_id` ASC";    $result = db_query($query);    $result2 = db_query($query2);  echo('<div style="float:left;width:50%;">');  while($row=db_fetch_object($result))  {         echo($row->object_obj_name."<br>");      echo($row->object_object_id."<br>");  }  echo '</div><div>';  while($row2=db_fetch_object($result2)) {      echo($row2->obj_name."<br>");      echo($row2->object_id."<br>");  }  echo("</div>");  db_free_result($result);  db_free_result($result2);}function remove_trans_objects() {  global $lang_id, $version, $user, $id, $lng, $tab_set;  $query= "SELECT * FROM `".$tab_set."` WHERE ".      "language_language_id='".$lng."' AND ".      "object_version_version_id=".$id." ".      "ORDER BY `object_object_id` ASC";    $result = db_query($query);  while($row=db_fetch_object($result))  {             $query2= "SELECT * FROM `objects` WHERE `object_id`=".$row->object_object_id;    $result2 = db_query($query2);          if ( db_num_rows($result2) == 0 ) {                 // echo($row->object_object_id." - ".$row->object_obj_name."<br>");                        $delete_transtations_q = "DELETE FROM `".$tab_set."` WHERE `object_object_id`=".$row->object_object_id;            $obj_res = db_query($delete_transtations_q);            echo db_affected_rows()." - ".$row->object_object_id." -- ".$row->object_obj_name."<br>";            if ($obj_res === FALSE)            {                abort ("SQL Error!", "Error: " . db_error() . " for query (<i>$delete_objects_q</i>).", TRUE);                $error = 1;            }                    }                  db_free_result($result2);  }}function add_trans_obj($row, $settab) {$languages = db_query("SELECT `language_id` FROM `languages`");    while ($row_lang = db_fetch_array($languages))    {        $qy = "INSERT INTO `".$settab."` (`object_object_id`, `object_obj_name`, `object_version_version_id`, `language_language_id`, `tr_text`, `suggestion`, `mod_date`, `reservator_user_id`, `date_to`, `author_user_id`) ".          "VALUES ($row->object_id, '".quote_smart($row->obj_name)."', $row->version_version_id, '$row_lang[0]', '', '', '', '', '', '');";        db_query($qy);    }    db_free_result($languages);}function add_trans_objects() {  global $id, $lng, $tab_set;        $tab = 'translations_'.$id;                 $query2= "SELECT * FROM `objects` WHERE `version_version_id`=".$id;        $result2 = db_query($query2);  while($row=db_fetch_object($result2))  {        $query=  "SELECT * FROM `".$tab."` WHERE ".      "language_language_id='".$lng."' AND ".      "object_object_id=".$row->object_id.";";       $result = db_query($query);    echo db_num_rows($result);          if ( db_num_rows($result) == 0 ) {            add_trans_obj($row, $tab);      }  }}output();//remove_trans_objects();//add_trans_objects();  include_once ("tpl_script/footer.php");?>