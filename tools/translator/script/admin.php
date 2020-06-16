<?php

  $title = 'Administration';
  include_once ('./tpl_script/header.php');

  if (!isset($_SESSION['userId']) ) {
    include_once('./tpl_script/main.php');
 } elseif (getrole($_SESSION['userId'])=='admin') {

    $LNG_ADMIN2['save'] = $LNG_ADMIN[0];
    $LNG_ADMIN2['create'] = $LNG_ADMIN[1];
    $LNG_ADMIN2['modify'] = $LNG_FORM[42];

    $LNG_ADMIN2['delete'] = $LANG_CONTACT[4];

    include_once ('./tpl_script/admin_lang.php');
    include_once ('./tpl_script/admin_languser.php');
    include_once ('./tpl_script/admin_version.php');
    include_once ('./tpl_script/admin_user.php');
    include_once ('./tpl_script/admin_licenses.php');


  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file
  $v_template->parseFile('./tpl/admin.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['gui_pagetitle'] = $LNG_HEAD[9];     
  
  $x = 0;  
  $v_att['linklist'][$x]['page_link'] = "?action=language";     
  $v_att['linklist'][$x]['page_name'] = $LNG_ADMIN[8]; 
  if ( isset($_GET['action']) && $_GET['action'] == 'language' ) {
     $v_att['linklist'][$x]['css'] = "sellink";  
  }    
  
  $x++;
  $v_att['linklist'][$x]['page_link'] = "?action=version";     
  $v_att['linklist'][$x]['page_name'] = $LNG_ADMIN[48];     
  if ( isset($_GET['action']) && $_GET['action'] == 'version' ) {
     $v_att['linklist'][$x]['css'] = "sellink";  
  }   

  $x++;
  $v_att['linklist'][$x]['page_link'] = "?action=user";     
  $v_att['linklist'][$x]['page_name'] = $LNG_ADMIN[20];     
  if ( isset($_GET['action']) && $_GET['action'] == 'user' ) {
     $v_att['linklist'][$x]['css'] = "sellink";  
  }    

  $x++;
  $v_att['linklist'][$x]['page_link'] = "?action=license";     
  $v_att['linklist'][$x]['page_name'] = $LNG_ADMIN[7];     
  if ( isset($_GET['action']) && $_GET['action'] == 'license' ) {
     $v_att['linklist'][$x]['css'] = "sellink";  
  }    

  echo $v_template->generate($v_att, 'string');

  // ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- //

  if (isset($_GET['id']) || (isset($_GET['mode']) and $_GET['mode']=='add')) {
    switch ($_GET['action']) {
      case 'languser': languser_info ($_GET['lid']); 
        break;
      case 'language': lang_info ($_GET['id']); 
        break;
      case 'version':  ver_info ($_GET['id']);  
        break;
      case 'user':     user_info ($_GET['id']); 
        break;
      case 'license':  license_info ($_GET['id']); 
        break;
    }
  }

  if ( isset($_GET['did']) && !$_POST['mode'] ) {
    switch ($_GET['action']) {
      case 'languser': languser_info ($_GET['did']); 
        break;
    }
  }   
  
  if (isset($_POST['action'])) switch ($_POST['action']) {
    case 'languser': 
      languser_post ();
      //include('./tpl_script/list_languser.php');
        break;
    case 'language': 
      lang_post ();
      include('./tpl_script/list_language.php');
        break;
    case 'version': 
      ver_post ();
      include('./tpl_script/list_version.php');
        break;
    case 'user': 
      user_post ();
      include('./tpl_script/list_users.php');
        break;
    case 'license': 
      license_post ();
      include('./tpl_script/list_licenses.php');
        break;
  }

  if (isset($_GET['action'] )) switch ( $_GET['action'] ) {
    case 'languser': 
      //languser_list (); 
      include('./tpl_script/list_languser.php');
        break;
    case 'language': 
      //lang_list (); 
      include('./tpl_script/list_language.php');
        break;
    case 'version':  
      //ver_list ();  
      include('./tpl_script/list_version.php');
        break;
    case 'user':     
      //user_list (); 
      include('./tpl_script/list_users.php');
        break;
    case 'license':     
      //license_list (); 
      include('./tpl_script/list_licenses.php');
        break;
  }


  // ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- //

 } else {
  include_once('./tpl_script/main.php');

 }

  include('./tpl_script/footer.php');
?>
