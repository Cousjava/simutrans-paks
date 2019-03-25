<?php$title = "Object Export";require_once ("tpl_script/header.php");$user=$_SESSION['userId'];$u_level=array('admin','gu','painter','pakadmin');if ( !$_SESSION['role'] ) {      include_once("./tpl_script/main.php");} elseif ( !compare_userlevels($u_level, $_SESSION['role']) ) {      include_once("./tpl_script/main.php");} else {// setadmin link listeinclude('./tpl_script/setadmin_links.php');//this is just a die wrapper doing few things more    function my_die ($error_msg, $comment)    {        echo ("<h1>$error_msg</h1>");        echo "<p><b>$comment</b><br />POST data received (255 is treated as null value):<br />\n";        print_r ($_POST);        echo "</p>";        echo "<p>&nbsp;</p>";        echo "<h3 class='center'><a href='obj_export_menu.php'>Back to export selection menu</a></h3>";        //die        include_once ("tpl_script/footer.php");        die;    }    ////////////////////////////////////////////////////////////////////////////      #require_once ("include/object.php");    require_once ("include/obj_export_wrapper.php");  ////////////////////////////////////////////////////////////////////////////    ////////////////////////////////////////////////////////////////////////////    //at first find out, what are we doing    //decide upon input button pressed    //if no button pressed, do nothing    if (!(isset($_POST["submit"])))    {        //basically dies with error message and valid page footer        my_die ("Incorrect input data!","This page must be accessed through export selection menu.");    }    //otherwise decide what to experod based on the value    if ($_POST["submit"] == "Export city buildings")    {        //export city buildings        //test if we have all required data        if (($_POST["version"]=="255") or ($_POST["type"] == "255"))        {            //basically dies with error message and valid page footer            my_die ("You have not selected all fields!","All fields are mandatory.");        }        $exporter = new city_building_exporter($_POST["version"], $_POST["type"]);        $exporter -> export ();        $exporter -> finalize ();    } elseif ($_POST["submit"] == "Export vehicles")    {        //export vehicles        //test if we have all required data        if (($_POST["version"]=="255") or ($_POST["way_type"] == "255") or ($_POST["cargo_type"] == "255"))        {            //dies with error message and valid page footer            my_die ("You have not selected all fields!","All fields are mandatory.");        }        $exporter = new vehicle_exporter($_POST["version"], $_POST["way_type"], $_POST["cargo_type"], $_POST["ordering"]);        $exporter -> export ();        $exporter -> finalize ();    } elseif ($_POST["submit"] == "Export Trees")    {        //export vehicles        //test if we have all required data        if ($_POST["version"]=="255")        {            //dies with error message and valid page footer            my_die ("You have not selected correct values!","All fields are mandatory.");        }        $exporter = new tree_exporter($_POST["version"]);        $exporter -> export ();        $exporter -> finalize ();    }else    {        //in this case we do not know what to do        echo ("<h1>Unknonw export request!</h1>");        echo "<p>I do not recognize the export type (submit value)<br />POST data received:<br />\n";        print_r ($_POST);        echo "</p>";    }    //done    echo "<p>&nbsp;</p>";    echo "<h3 class='center'><a href='obj_export_menu.php'>Back to the export selection menu</a></h3>";    //footer, nothing ater this (closes page)  include_once ("tpl_script/footer.php");?>