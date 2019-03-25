<?php
  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('tpl/main.htm');
  // ----- Prepare data
  $v_att = array();

  // Different Titel for Main Page
  if ( $title == 'main' ) 
  { if (isset($_SESSION['real_name'])) $v_att['page_title'] = $LNG_MAIN[0].' '.$_SESSION['real_name'];
    else                               $v_att['page_title'] = $LNG_MAIN[0].' '.$LNG_MAIN[1];
  }

  //Am I logged as some known user?
  if (isset($_SESSION['userId'])) 
  { //If yes
    $content = pakset_activ(); 
    $v_att['setstatus']['set_activ_yes'] = $LNG_MAIN[5];    
    $v_att['setstatus']['set_activ_yes_sets'] = $content[0];    
    $v_att['setstatus']['set_activ_no'] = $LNG_MAIN[4];
    $v_att['setstatus']['set_activ_no_sets'] = $content[1];
  }

  $v_att['linklist'] = $LNG_MAIN[3]." - ".$LNG_MAIN[24];

  $v_att['subtitel'] = $LNG_MAIN[6];

  //now check for user type and offer allowed actions:

    $user_type = "guest";

    //Am I loogeed as some known user?
    if (isset($_SESSION['userId'])) {
        //If yes
        $user_type = $_SESSION['role'];
  }

    ////////////////////////////////////////////////////////////////////////////
    ////Display proper actions//////////////////////////////////////////////////


    //show text lists, and reservations
    //available for any translator and above
/* no longer supported
    $u_level = array('tr1','tr2','admin','gu','painter','pakadmin');
    if (compare_userlevels($u_level, $user_type)) {
        $v_att['menutitel'][$x] = $LNG_MAIN[7];
        $v_att['menu_description'][$x] = $LNG_MAIN[8];
    } 
*/

  // available allways
  // Gallery
  $x = 0;
  $v_att['menulist'][$x]['link_file'] = 'gallery.php?lang='.$st;
  $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[19];
  $v_att['menulist'][$x]['menu_description'] = $LNG_HEAD[20];

  // Pak Info
  $x++;
  $v_att['menulist'][$x]['link_file'] = 'pakset_info.php?lang='.$st;
  $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[18];
  $v_att['menulist'][$x]['menu_description'] = $LNG_HEAD[21];

  // object guide and search
  $x++;
  $v_att['menulist'][$x]['link_file'] = 'directions.php?lang='.$st;
  $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[5];
  $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[12];

  // text export
  $x++;
  $v_att['menulist'][$x]['link_file'] = 'main.php?lang='.$st.'&page=wrap';
  $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[3];
  $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[10];

  // text import
    //available to any tr2 and above
    $u_level = array('tr2','admin','gu','painter','pakadmin');
    if (compare_userlevels($u_level, $user_type)) {
      $x++;
        $v_att['menulist'][$x]['link_file'] = 'load.php?lang='.$st;
        $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[4];
        $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[11];
    }

  // compare translated objects from other sets
    //available to any tr1 and above
    $u_level = array('tr1', 'tr2','admin','gu','painter','pakadmin');
    if (compare_userlevels($u_level, $user_type)) {
      $x++;
        $v_att['menulist'][$x]['link_file'] = 'setcompare.php?lang='.$st;
        $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[13];
        $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[25];
    }

  
  // manage set objects
    //available to pakadmin only
    $u_level = array('pakadmin', 'painter', 'admin');
    if ( compare_userlevels($u_level, $user_type) ) {
      $x++;
        if ( $user_type == 'painter' ) {
          $v_att['menulist'][$x]['link_file'] = 'obj_browser.php?lang='.$st;
          $v_att['menulist'][$x]['menutitel'] = $LNG_MANAGE[3];
          $v_att['menulist'][$x]['menu_description'] = $LNG_MANAGE[4];
        } else {
          $v_att['menulist'][$x]['link_file'] = 'obj_index.php?lang='.$st;
          $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[6];
          $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[13];
        }
   }
  // statistics menu
  // available allways
    $x++;
    $v_att['menulist'][$x]['link_file'] = 'main.php?lang='.$st.'&page=stats_menu';
    $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[7];
    $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[14];


    //preferecnes are avlailable to any logged user
    if ($user_type != "guest") {
      $x++;
        $v_att['menulist'][$x]['link_file'] = 'preferences.php?lang='.$st;
        $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[8];
        $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[15];
    }

    //adminsitration allowed only to admins
    if ( $user_type == 'admin' ) {
      $x++;
        $v_att['menulist'][$x]['link_file'] = 'admin.php?lang='.$st;
        $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[9];
        $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[16];
  }

  // login / logout
    $x++;
    if ($user_type != "guest") {
      // logout
      $v_att['menulist'][$x]['link_file'] = 'index.php?lang='.$st.'&logout=1';
      $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[10].'&nbsp;'.$_SESSION['userId'];
        $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[17];
    } else {
      // login
      $v_att['menulist'][$x]['link_file'] = 'index.php?lang='.$st;
      $v_att['menulist'][$x]['menutitel'] = $LNG_HEAD[11];
        $v_att['menulist'][$x]['menu_description'] = $LNG_MAIN[18];
    }
/*
      $x++;
        $v_att['menulist'][$x]['link_file'] = ;
        $v_att['menulist'][$x]['menutitel'] = ;
        $v_att['menulist'][$x]['menu_description'] = ;
*/

  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');

// ----- Display result
echo $v_result;



?>


