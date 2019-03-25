<?php
$title="Clean Temporary Directory";
include ("tpl_script/header.php");
//object import from dat files accessible to admins and pakadmin only!
$user=$_SESSION['userId'];
$u_level=array("admin");

if ( !isset($_SESSION['role']) or  !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

include('./tpl_script/setadmin_links.php');



////////////////////////////////////////////////////////////////////////////////

$cmd_return_value = 0;


        echo "<p class='left'>";

         
        verzeichnis_del("../data/temp");
        verzeichnis_del("tpl_cache");
        
        if ($cmd_return_value != 0)
        {
            //variable above holds return value of the system command, do not continue if else than 0
            echo "</p>";
            echo "<h1>Problem!</h1>";
            //include_once ("include/footer.php");
        } else {
          echo "</p>";

          print_line ("<p><strong>Finished!</strong></p>", 1);
          print_line ("<h2><a href='main.php'>Main Menu</a></h2>");
    }
////////////////////////////////////////////////////////////////////////////////

    //footer, nothing ater this (closes page)
  include_once ("tpl_script/footer.php");
?>

