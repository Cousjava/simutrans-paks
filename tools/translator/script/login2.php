<?php
include("./include/dblib.php");

function login($id) 
{ global $LNG_LANGUAGE;
  $_SESSION['userId'] = $id;

  $query = db_query ("SELECT * FROM `users` WHERE `u_user_id`='".$id."';");
  $row = db_fetch_array($query);
  db_free_result($query);

  $_SESSION['real_name'] = $row['real_name'];
  $_SESSION['role'] = $row['role'];
  $_SESSION['config4'] = $row['config4'];
  $_SESSION['config3'] = $row['config3'];
  $_SESSION['config2'] = $row['config2'];
  $_SESSION['config1'] = $row['config1'];
  $_SESSION['ref_lang'] = $row['ref_lang'];
  if ( !empty($row['user_lang']) ) { $_SESSION['user_lang'] = $row['user_lang']; }
                
  $n = $row['set_enabled'];
  if ( $n == '' || empty($n) || $n == NULL ) $name = array('all'); 
  else 
  { $name = unserialize($n); 
    if ( !is_array($name) )  $name = array(); 
  }  
  $_SESSION['set_edit'] = $name;

  //$_SESSION['role'] = $id['role'];
  $t = date("Y-m-d H:i:s", time());
  $query="UPDATE `users` SET `last_login`='".$t."' WHERE `u_user_id`='".$id."'";
  $result = db_query($query);     

  $result = db_query("SELECT * FROM `translate` WHERE `translator_user_id`='".$id."'");
  $lng_sort = array();
  while ($lang_row = db_fetch_object($result)) $lng_sort[] = $lang_row->lng_tr_language_id;
  db_free_result($result);
  $_SESSION['edit_lang'] = $lng_sort;

}

/*
                Login and redirect back
*/
if (isset($_POST['username'])) 
{  $givenu = $_POST['username'];
   if (preg_match('#^[a-zA-Z0-9@ \._-]{1,20}$#',$givenu) == 1)
   {
        $user=db_query("SELECT * FROM `users` WHERE `u_user_id`='".db_real_escape_string($givenu)."'");

        if ( (!isset($user)) || (db_num_rows($user) == 0) ) 
        {       // sending to main page + shows it didn't work
                Header ("Location: index.php?msg=login_failed_".$givenu."_notfound");
        } else 
        {    // else read from databases name, password ...
             $info = db_fetch_array($user);
             if ($info['state'] != 'active') Header ("Location: index.php?msg=login_failed_".$givenu."_not_aktiv");
             elseif ( crypt($_POST['password'], $info['pass_bin']) == $info['pass_bin']) 
             {  // log in
                login ($info['u_user_id']);
                Header ("Location: index.php?msg=login_ok_".$givenu);
             } else Header ("Location: index.php?msg=login_failed_".$givenu."_pass");
        }
        db_free_result($user);
   } else  Header ("Location: index.php?msg=login_badchar");
} else     Header ("Location: index.php?msg=login_no_name");
?>

