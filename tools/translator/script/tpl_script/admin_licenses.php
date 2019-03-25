<?php

function license_info ($ver) {
  global $LNG_ADMIN, $LNG_ADMIN2, $LANG_CONTACT, $LNG_FORM, $LNG_MESSAGE;

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_licenses.htm');
  // ----- Prepare data
  $v_att = array();

  $mes = 0;

  $v_att['form_license']['gui_licenseid'] = "id";     
  $v_att['form_license']['gui_license_name'] = $LNG_ADMIN[42];     
  $v_att['form_license']['gui_license_link'] = $LNG_ADMIN[45];  

   if ( isset($_GET['mode']) && $_GET['mode']=="add") {
    $v_att['form_license']['gui_subtitle'] = $LNG_ADMIN[46];

    $v_att['form_license']['value_mode'] = "add";
    $v_att['form_license']['gui_action_button'] = $LNG_ADMIN2['create'];
  } else {
    $v_att['form_license']['gui_subtitle'] = $LNG_ADMIN[47]; 
    
    $v_att['form_license']['value_mode'] = $LNG_ADMIN2['save'];
    if (!isset($ver)) {
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_MESSAGE[6];
      echo $v_template->generate($v_att, 'string');
      return;
    }
    $sql = "SELECT * FROM `licenses` WHERE `license_id`='$ver';";
    $query = db_query($sql);

    if ($row=db_fetch_array($query)) 
    {   $v_att['form_license']['value_licenseid'] = $row['license_id'];     
        $v_att['form_license']['value_license_name'] = $row['license_name'];     
       $v_att['form_license']['value_license_link'] = $row['license_link'];  

      db_free_result($query);
    } else {
      db_free_result($query);
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_MESSAGE[5];
      $mes++;
      // message: sql string
      $v_att['value_message']['messages'][$mes]['message'] = $sql;
      echo $v_template->generate($v_att, 'string');
      return;
    }  
    $v_att['form_license']['gui_action_button'] = $LNG_ADMIN2['modify'];
    $v_att['form_license']['delete_button']['gui_del_button'] = $LNG_ADMIN2['delete'];

  }



  echo $v_template->generate($v_att, 'string');
}

function license_post () {
  global $LNG_ADMIN, $LNG_ADMIN2, $LANG_CONTACT, $LNG_FORM, $LNG_MESSAGE;

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin_licenses.htm');
  // ----- Prepare data
  $v_att = array();

  $mes = 0;

  if ($_POST['mode']=="add") {

    $sql="INSERT INTO `licenses` (`license_name`,`license_link`) VALUES ( '%s','%s' );";
    
  }

  if ( isset($_POST['mode']) && $_POST['mode']=="edit") {
   if (!isset($_POST['flicense_id'])) {
      $v_att['value_message']['messages'][$mes]['message'] = $LNG_ADMIN[50];
      echo $v_template->generate($v_att, 'string');
      return;
    }
      
    $sql = sprintf ("SELECT `license_id` FROM `licenses` WHERE `license_id`=%s;",$_POST['flicense_id']);

    $query = db_query($sql);
    if ($row=db_fetch_array($query)) {
      db_free_result($query);
    } else {
      db_free_result($query);
      $v_att['value_message']['messages'][$mes]['message'] = sprintf (  $LNG_MESSAGE[4], 'id', $_POST['flicense_id']);
      echo $v_template->generate($v_att, 'string');
      return;
    }
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_MESSAGE[1], 'id', $_POST['flicense_id']);
    $mes++;
    $sql="UPDATE `licenses` SET `license_name`='%s',`license_link`='%s' WHERE `license_id`=%s;";
  }  
 
  if (isset($_POST['submit']) and $_POST['submit']=="Delete") {
    
    $v_att['value_message']['messages'][$mes]['message'] = sprintf ($LNG_MESSAGE[2], 'id', $_POST['flicense_id']);
    $mes++;

    $sql="DELETE FROM `licenses` WHERE `license_id`=%s;";
    $sql = sprintf ($sql,$_POST['flicense_id']);
    $deletequery = db_query($sql);
   
    $v_att['value_message']['messages'][$mes]['message'] = "OK";
    $mes++;
    echo $v_template->generate($v_att, 'string');

    return;
  }
  
 
  $sql = sprintf ($sql
    ,$_POST['flicense_name']
    ,$_POST['flicense_link']
    ,$_POST['flicense_id']
  );
  $updatequery = db_query($sql);


  $v_att['value_message']['messages'][$mes]['message'] = "OK";

  license_info ($_POST['flicense_id']);


  echo $v_template->generate($v_att, 'string');
}
 
?>
