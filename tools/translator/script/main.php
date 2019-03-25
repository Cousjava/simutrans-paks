<?php

  //main available to all users

if ( isset($_GET['page']) ) $page = trim($_GET['page']);
else                        $page = "";


switch ( $page ) {
  case 'wrap':
    $title = 'wrap';
    include('./tpl_script/header.php');
    include('./tpl_script/wrap.php');
    break;

  case 'stats_menu':
    $title = 'stats_menu';
    include('./tpl_script/header.php');
    include('./tpl_script/stats_menu.php');
    break;

  case 'setinfo':
    $title = 'setinfo';
    include('./tpl_script/header.php');
    include('./tpl_script/setinfo.php');
    break;

  case 'rssinfo':
    $title = 'rssinfo';
    include('./tpl_script/header.php');
    include('./tpl_script/rssinfo.php');
    break;
    
  case 'contact':
    $title = 'contact';
    include('./tpl_script/header.php');
    include('./tpl_script/contact.php');
    break;

  case 'admin_set':
    $title = 'Preferences_Set';
    include('./tpl_script/header.php');
    include('./tpl_script/admin_set.php');
    break;

  case 'dsgvo':
    $title = 'DSGVO';
    include('./tpl_script/header.php');
    include('../dsgvo/dsgvo.htm');
    break;

  default:
    $title = 'main';
    include('./tpl_script/header.php');
    include('./tpl_script/main.php');

}

// page footer
include('./tpl_script/footer.php');
?>




