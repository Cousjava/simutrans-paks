<?php

  require_once("./include/parameter.php");
  require_once('./include/pcltemplate/pcltemplate.class.php');


  $title = 'Preferences_User';
  //preferences available to all logged users
  require_once("./tpl_script/header.php");

  $user=$_SESSION['userId'];

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/pref_user.htm');
  // ----- Prepare data
  $v_att_t = array();
  //include_once ("./include/pref_pass.php");
  //include_once ("./include/pref_user.php");

function change_password () {
  global $LNG_USER, $LNG_FORM, $v_att_t;
    $err = 0;

    if (!isset($_POST['user_id'])) {
      $v_att_t['value_message']['messages'][$err]['message'] = $LNG_USER[7];
      $v_att_t['value_message']['messages'][$err]['css_message'] = "err_message";
      return;
    }
    $sql = sprintf ("SELECT `u_user_id` ".
      " FROM `users` WHERE `u_user_id`='%s';"
      ,$_POST['user_id']
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      $v_att_t['value_message']['messages'][$err]['message'] = sprintf ($LNG_USER[17]."...",$_POST['user_id']); 
      $v_att_t['value_message']['messages'][$err]['css_message'] = "err_message";
      return;
    }
    if (empty($_POST['pass'])) {
      $v_att_t['value_message']['messages'][$err]['message'] = $LNG_USER[21];
      $v_att_t['value_message']['messages'][$err]['css_message'] = "err_message";
      return;
    }
    if ($_POST['pass'] != $_POST['pass2']) {
      $v_att_t['value_message']['messages'][$err]['message'] = $LNG_USER[22];
      $v_att_t['value_message']['messages'][$err]['css_message'] = "err_message";
      return;
    }
    $user=$_POST['user_id'];

    //printf ($LNG_USER[23]."...<br>\n",$user);
    $v_att_t['value_message']['messages'][$err]['message'] = sprintf ($LNG_USER[23]."...<br>\n",$user);
    $err++;
    $sql=sprintf ("UPDATE `users` SET `pass_bin`='%s' WHERE `u_user_id`='%s';"
      ,password_hash($_POST['pass'], PASSWORD_DEFAULT) //crypt($_POST['pass'])
      ,$user
    );
    $updatequery = db_query($sql);

    $v_att_t['value_message']['messages'][$err]['message'] = $LNG_FORM[41];
    $v_att_t['value_message']['messages'][$err]['css_message'] = "ok_message";
}

function change_user () {
  global $LNG_USER, $LNG_FORM, $v_att_t;
  $err = 0;
  
    if (!isset($_POST['user_id'])) {
      $v_att_t['value_message']['messages'][$err]['message'] = "No user selected."; 
      $v_att_t['value_message']['messages'][$err]['css_message'] = "err_message";
      return;
    }
    $sql = sprintf ("SELECT `u_user_id` ".
      " FROM `users` WHERE `u_user_id`='%s';"
      ,$_POST['user_id']
    );
    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      $v_att_t['value_message']['messages'][$err]['message'] = sprintf ($LNG_USER[17]."...",$_POST['user_id']); 
      $v_att_t['value_message']['messages'][$err]['css_message'] = "err_message";
      return;
    }
    
    $v_att_t['value_message']['messages'][$err]['message'] = sprintf ($LNG_USER[18]."...",$_POST['user_id']); 
    $err++;
    
    $sql="UPDATE `users` SET `real_name`='%s',`email`='%s',note='%s' ".
      " ,`config1`='%s',`config2`='%s',`config3`='%s',`config4`='%s',`ref_lang`='%s',`user_lang`='%s' ".
      " WHERE `u_user_id`='%s';";
    $user=$_POST['user_id'];
                             
    if ( $_POST['userreflang'] == 255 ) { $userreflang = ''; } else { $userreflang = $_POST['userreflang']; }
    if ( $_POST['userlang'] == 255 ) { $userlang = ''; } else { $userlang = $_POST['userlang']; }
    
    $sql = sprintf ($sql
      ,$_POST['real_name']
      ,$_POST['email']
      ,$_POST['note']
      ,$_POST['config1']
      ,$_POST['config2']
      ,$_POST['config3']
      ,$_POST['config4']
      ,$userreflang
      ,$userlang
      ,$user
    );
    $updatequery = db_query($sql);

  // Änderungen in Session übernehmen
  $query = db_query ("SELECT * FROM `users` WHERE `u_user_id`='".$_SESSION['userId']."';");
  $row = db_fetch_array($query);
  db_free_result($query);

  $_SESSION['real_name'] = $row['real_name'];
  $_SESSION['config4'] = $row['config4'];
  $_SESSION['config3'] = $row['config3'];
  $_SESSION['config2'] = $row['config2'];
  $_SESSION['config1'] = $row['config1'];
  $_SESSION['ref_lang'] = $row['ref_lang'];

  $v_att_t['value_message']['messages'][$err]['message'] = $LNG_FORM[41];
  $v_att_t['value_message']['messages'][$err]['css_message'] = "ok_message";
}
  
  if (isset($_POST['submit']) && $_POST['submit']==$LNG_ADMIN[0]) change_password();
  if (isset($_POST['submit']) && $_POST['submit']==$LNG_EDIT[19]) change_user();

if ( $user == "" ) {
  include_once('./tpl_script/main.php');
  
} else { 
  include("./tpl_script/pref_user.php");

  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att_t, 'string');

  // ----- Display result
  echo $v_result;   
}
  // ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- //

  include("./tpl_script/footer.php");
?>
