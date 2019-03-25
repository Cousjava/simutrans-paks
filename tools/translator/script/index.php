<?php
  //required to do the session handling
  require_once("./include/parameter.php"); 
  require_once ("./include/dblib.php");
  include_once ("./include/general.php");

  // log out user
  if ( isset($_GET['logout']) && $_GET['logout'] == 1 )
  { unset($_SESSION['userId'],
          $_SESSION['real_name'],
          $_SESSION['role'],
          $_SESSION['maintainter'],
          $_SESSION['edit_lang'],
          $_SESSION['config4'],
          $_SESSION['config3'],
          $_SESSION['config2'],
          $_SESSION['config1'],
          $_SESSION['ref_lang']);
  }
  
  //check if user is logged in or not
  if (!isset($_SESSION['userId']) || is_null($_SESSION['userId']))
  { $title = 'login';
    require_once("./tpl_script/header.php");
    require_once("./tpl_script/login_form.php");
  } else {
    //if we are logged user, redirect to main.php
    require_once("./main.php");
  }

  //general closing clause
  include_once("./tpl_script/footer.php");
?>




