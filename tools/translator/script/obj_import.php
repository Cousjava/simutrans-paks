<?php
    //object import from dat files accessible to admins only!
$title='Object_Import';
include("./tpl_script/header.php");
$user=$_SESSION['userId'];
$u_level = array('gu','painter','pakadmin','admin');

if ( !isset($_SESSION['role']) or  !compare_userlevels($u_level, $_SESSION['role'])) 
{ include("./tpl_script/main.php");
  include('./tpl_script/footer.php');
  die();
} 

include('./tpl_script/setadmin_links.php');


    //now the object import things
    require_once ("./include/obj_import_inc.php");

    
    ////////////////////////////////////////////////////////////////////////////
    //this function will be used few lines below
    //it creates the option list for version selection menu
    function return_versions_html ()
    {
        GLOBAL $LNG_FORM;
        
        $version_sql = "SELECT `v_name`,`version_id`,`maintainer_user_id` FROM `versions`";
        $db_result = db_query($version_sql);

        //as first and default, place dummy - to prevent careles uploads
        $res="<option value='255' selected='selected'>".$LNG_FORM[7]."</option>\n";
        while ($row=db_fetch_array($db_result))
        {
          if ( in_array($_SESSION['userId'], get_setmaintainter($row['version_id'])) ) {
            $res .= sprintf ("<option value='%d'>%s <!--(maintainer: %s)--></option>\n"
                    ,$row['version_id']
                    ,$row['v_name']
                    ,$row['maintainer_user_id']);
          }
        }
        db_free_result($db_result);
        return $res;
    }


    ////////////////////////////////////////////////////////////////////////////
    //display the input dialog
    //(that id done always
    //note it is a printf so %s can be used to reference variables
    $subversion = "keine subversion";
    if (isset($_SESSION['subversion'])) $subversion = $_SESSION['subversion'];
    
    printf ('<form enctype="multipart/form-data" action="obj_import.php" method="post">
        <p>
        <b>'.$LNG_FORM[6].':</b><br />
        <select name="version">%s</select>
        </p>
        <!--div class="note">Only version maintainer will be allowed to import objects.</div-->



        <p>
        <b>'.$LNG_OBJ_IMPORT[28].':</b><br />
        <select name="upload_type">
            <option value="full" selected="selected">'.$LNG_OBJ_IMPORT[1].'</option>
            <option value="dat">'.$LNG_OBJ_IMPORT[2].'</option>
        </select>
        </p>
        <div class="note">'.$LNG_OBJ_IMPORT[3].'</div>



        <p>
        <b>'.$LNG_OBJ_IMPORT[4].':</b><br />
        <input type="file" id="uploadfile" name="uploadfile" size="30" />
        </p>
        <div class="note">'.$LNG_OBJ_IMPORT[5].'</div>

        <p><b>'.$LNG_OBJ_IMPORT[6].':</b><br />
        <select name="mode">
            <option value="verbose" selected="selected">'.$LNG_OBJ_IMPORT[7].'</option>
            <option value="quiet">'.$LNG_OBJ_IMPORT[8].'</option>
        </select>
        </p>

        <p><b>'.$LNG_FORM[6].':</b>
        <input type="text" name="subversion" value="'.$subversion.'" />
        </p>
        
        <p><input type="submit" name="import" value="'.$LNG_OBJ_IMPORT[9].'" /></p>
    </form>', return_versions_html());

    info_box ( $LNG_OBJ_IMPORT[10], $LNG_OBJ_IMPORT[11], "", "", "", "width:700px;");

    info_box ( $LNG_OBJ_IMPORT[12], $LNG_OBJ_IMPORT[13], "", "", "", "width:700px;");

    if ( isset($_POST['import']) and  $_POST['import'] == $LNG_OBJ_IMPORT[9])
    { //if file was uploaded..
      if (!empty($_FILES['uploadfile']['name']))
      { $subversion = "keine subversion";
        if (isset($_post['subversion'])) $subversion = $_post[subversion];
        $_SESSION['subversion'] = $subversion;
        if ($subversion == "keine subversion") $subversion = "";

        //this does the complete file import (touching directly available post data
        //it also includes all other checks
        //defined in: obj_import_inc.php
        import_file ();
      } else echo "<h1>".$LNG_OBJ_IMPORT[30]."</h1>\n";
    }



    // ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- //
  include("tpl_script/footer.php");
?>
