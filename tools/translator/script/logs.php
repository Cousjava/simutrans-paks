<?php
  $title="Logfiles";
  include ("tpl_script/header.php");
  //object import from dat files accessible to admins and pakadmin only!
  $u_level=array("admin", "pakadmin");

  if ( !isset($_SESSION['role']) or  !compare_userlevels($u_level, $_SESSION['role'])) 
  { include("./tpl_script/main.php");
    include('./tpl_script/footer.php');
    die();
  } 

  include('./tpl_script/setadmin_links.php');

  include ('./include/translations.php');
  include ('./include/select.php');

  // ----- Create the template object
  $v_template = new PclTemplate();
  // ----- Parse the template file   
  $v_template->parseFile('./tpl/logs.htm');  
  // ----- Prepare data
  $v_att = array();
  //prints page title
  $v_att['page_title'] = $LNG_MANAGE[17];

  $version_auswahl = select_box_read_version();
  select_box_version($version_auswahl); 
  $v_att['submit'] = $LNG_FORM[18];

//if ( isset($_POST['obj_s']) and $_POST['obj_s'] == $LNG_FORM[18] )
  if ($version_auswahl != 255)
  {
    $file = "../data/set_".$version_auswahl.".log";
    if ( file_exists($file) ) 
    {  $data = file($file);
       $v_att['log_table']['version_name'] = $versions_all[$version_auswahl];
       $v_att['log_table']['version']      = $version_auswahl;
       $v_att['log_table']['file']         = $file;
       $v_att['log_table']['count']        = count($data)." entries";

       $v_att['log_table']['date']         = "Object Name";
       $v_att['log_table']['user_name']    = $LNG_LOGIN[15];
       $v_att['log_table']['obj_typ']      = "Object Type";
       $v_att['log_table']['obj_name']     = "Object Name";
       $v_att['log_table']['lang']         = $LNG_MAIN[23];

       rsort($data); 

       if ( count($data) < 2000 )  $r = count($data);  else  $r = 2000; 
       for ( $x = 0; $x < $r; $x++ ) 
       {  $data2 = explode("|", $data[$x]);
          if (isset($LNG_LANGUAGE[$data2[4]])) $langw = $LNG_LANGUAGE[$data2[4]];
          else                                 $langw = '--';

          if ( $data2[6] != '' ) 
          {  $obj_link = '<A href="edit.php?obj_id='.$data2[6].'&version='.$version_auswahl.'#'.$data2[4].'" target="_blank">'
            .htmlspecialchars($data2[3], ENT_QUOTES, "UTF-8").'</A>';
          } else $obj_link = $data2[3];

          $v_att['log_table']['line'][$x]['nr'] = $x+1;
          $v_att['log_table']['line'][$x]['date'] = $data2[0];
          $v_att['log_table']['line'][$x]['user_name'] = $data2[1];
          $v_att['log_table']['line'][$x]['obj_typ'] = $data2[2];
          $v_att['log_table']['line'][$x]['obj_name'] = $obj_link;
          $v_att['log_table']['line'][$x]['lang'] = $langw;
          $v_att['log_table']['line'][$x]['action'] = $data2[5];
       }
    }
  }

  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;
  unset($v_att);     

  include("tpl_script/footer.php");
?>
