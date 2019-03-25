<?php
    //header, no output before this (sends header information)
require_once ("tpl_script/header.php");
	//list of all user typs allowed on this page
	//header will block access for all other users
$user=$_SESSION['userId'];
$u_level=array('admin', 'pakadmin');

if ( !$_SESSION['role'] ) {
    	include_once("./tpl_script/main.php");
} elseif ( !compare_userlevels($u_level, $_SESSION['role']) ) {
    	include_once("./tpl_script/main.php");
} else {

    function abort ($name, $msg = "", $close_table = FALSE)
    {
        if ($close_table)
        {
            echo "</td></tr></table>";
            echo "</div>";
        }
        echo "<h1>$name</h1>\n<p>$msg</p>\n";
        echo "<p>&nbsp;</p>";
        echo "<h2><a href='obj_index.php'>Object Menu</a></h2>";
        include_once ("./tpl_script/footer.php");
        die();
    }


    //will be prefixed by "SimuTranslator - " for window title
	$title = "Purge Objects";



    //prints page title
    //called separately in case you have some special requirements
	PrintTitle();


include ( "./lang/".$_SESSION['user_lang']."/lng_manage.php" );
// setadmin link liste
include('./tpl_script/setadmin_links.php');

    //now decide what to do
    //either we received POST data and will delete or we will ask user
    if (isset($_POST["submit"]) and ($_POST["submit"]=="del all" || $_POST["submit"]=="del Objects"))
    {
        //PURGE
        $set = explode("#", $_POST['version']);
        $vid = intval($set[0]);
        $t = "";
        
        if ( $_POST["submit"] == "del Objects" ) { 
        	$objtype = $_POST['obj_auw'];
        	$t = "AND `obj`='".$objtype."'";
        } else { $objtype = 255; }
        
     
        //test legality
        if($_POST["confirm"]=="no")
        {
            abort ("No was Checked!", "Aborting...");
            //will call footer and die
        }

        //check if user is maintainer
        if (!in_array($user, get_setmaintainter($vid)))
        {  abort ("You are not the version maintainer!", "Only maintainer is allowed to perform this operation for version " . $vid . ".");
           //will call footer and die
        }
        
        //now delete
        echo "<div align=\"center\">";
        echo "<table class=\"tableback\" width=\"600\" align=\"center\"><tr><td>";
        db_query("START TRANSACTION");
        $tab = 'translations_'.$vid;
        
        if ( $_POST["submit"]=="del all" ) {
          
          
// del translations text          
          echo "<p class='tight'>Deleting translations for Set: $vid - $set[1]</p>\n";
        
          // different del for selected objects or all objects
	  $error = 0;
	  if ( $objtype != 255 ) 
	  { $object_auswahl = db_query("SELECT `object_id` FROM `objects` WHERE `version_version_id`=".$vid." ".$t.";" );
               while ($row = db_fetch_array($object_auswahl)) 
               { $delete_transtations_q = "DELETE FROM `".$tab."` WHERE `object_object_id`=".$row['object_id'].";";
                 $obj_res = db_query($delete_transtations_q);
                 $deleted_trans += db_affected_rows();
                 if ($obj_res === FALSE)
                    { abort ("SQL Error!", "Error: " . db_error() . " for query (<i>$delete_objects_q</i>).", TRUE);
                      $error = 1;
                    }
               }
               db_free_result($object_auswahl);

	  } else {
          	$delete_transtations_q = "DELETE FROM `".$tab."` WHERE `object_version_version_id`=$vid;";
          	$obj_res = db_query($delete_transtations_q);
          	$deleted_trans = db_affected_rows();
          	if ($obj_res === FALSE)
          	{
            		abort ("SQL Error!", "Error: " . db_error() . " for query (<i>$delete_objects_q</i>).", TRUE);
            		$error = 1;
          	}
      	  }

          if ( $error == 0 ) {
            echo "<p class='tight'>Deleted $deleted_trans objects.</p>\n";
          }
          echo "<p>&nbsp;</p>";
	  }

// del images
	  echo "<p class='tight'>Deleting images associated with Set: $vid - $set[1]</p>\n";
	$tab = 'images_'.$vid;

          // del image files
        $object_auswahl = db_query("SELECT `object_id` FROM `objects` WHERE `version_version_id`=".$vid." ".$t.";" );
    	    while ($row = db_fetch_array($object_auswahl)) {
    	      $query = "SELECT `image_name`, `filename`, `object_obj_type` FROM `".$tab."` WHERE `object_obj_id`=".$row['object_id']." AND `object_version_version_id`=".$vid.";";
    	      $result = db_query($query);
   	      	  $image_count = db_num_rows($result);
   	      
    	      for ($i=0; $i < $image_count; $i++)
    	      {
        		//get the image name and free it from array
        		$img_n = db_fetch_array($result);
        		if ( $img_n['filename'] != NULL ) {
        			$pfad = $imagepfad.$vid."/".$img_n['object_obj_type']."/".$img_n['filename'];
 					if ( file_exists($pfad) ) { unlink($pfad);  }
        	  	}

    	      }    
            }
          // different del for selected objects or all objects
	  if ( $objtype != 255 ) { 
            $sql_objects = " AND `object_obj_type`='".$objtype."'";
          } else {
            $sql_objects = "";
          }


          $delete_images_q = "DELETE FROM `".$tab."` WHERE object_version_version_id=".$vid.$sql_objects.";";
          $img_res = db_query($delete_images_q);
          $deleted_img = db_affected_rows();
          if ($img_res === FALSE)
          {
            abort ("SQL Error!", "Error: " . db_error() . " for query (<i>$delete_images_q</i>).", TRUE);
          } else {
            echo "<p class='tight'>Deleted $deleted_img images.</p>\n";
          }
          echo "<p>&nbsp;</p>";

// del properties
          echo "<p class='tight'>Deleting object properties associated with Set: $vid - $set[1]</p>\n";
         
        
          // different del for selected objects or all objects
        $deleted_prop = 0;
        $error = 0;
        $object_auswahl = db_query("SELECT `object_id` FROM `objects` WHERE `version_version_id`=".$vid." ".$t.";" );
             while ($row = db_fetch_array($object_auswahl)) {  
          	$delete_transtations_q = "DELETE FROM `property` WHERE `having_obj_id`=".$row['object_id'].";";
          	$prop_res = db_query($delete_transtations_q);
          	$deleted_prop += db_affected_rows();
          	if ($obj_res === FALSE)
          	{
            		abort ("SQL Error!", "Error: " . db_error() . " for query (<i>$delete_objects_q</i>).", TRUE);
            		$error = 1;
          	}
             }


          if ( $error == 0 ) {

            echo "<p class='tight'>Deleted $deleted_prop properties.</p>\n";
          }
          echo "<p>&nbsp;</p>";


// del objects
        echo "<p class='tight'>Deleting objects for version Set: $vid - $set[1]</p>\n";

          // different del for selected objects or all objects
	  if ( $objtype != 255 ) { 
            $sql_objects = " AND `obj`='".$objtype."'";
          } else {
            $sql_objects = "";
          }
        $delete_objects_q = "DELETE FROM objects WHERE version_version_id=".$vid.$sql_objects.";";
        $obj_res = db_query($delete_objects_q);
        $deleted_obj = db_affected_rows();
        if ($obj_res === FALSE)
        {
            abort ("SQL Error!", "Error: " . db_error() . " for query (<i>$delete_objects_q</i>).", TRUE);
        }else
        {
            echo "<p class='tight'>Deleted $deleted_obj objects.</p>\n";
        }
        echo "<p>&nbsp;</p>";

        db_query("COMMIT");
        echo "<p class='tight'>" . ($deleted_trans+$deleted_img+$deleted_prop+$deleted_obj) . " records were deleted.</p>\n";
        echo "</td></tr></table>";
        echo "</div>";


        echo "<p>&nbsp;</p>";
        echo "<h2><a href='obj_index.php'>Object Menu</a></h2>";


    }else
    {
        //no post datra - generate the form
        //very long html part...


        $res="<option value='255' selected='selected'>".$LNG_FORM[7]."</option>\n";
        $setname = array(); 

        $versions = db_query("SELECT * FROM versions");
        while ($row = db_fetch_array($versions)) {
          if ( $row['maintainer_user_id']  == $user 
            or $row['maintainer_user_id2'] == $user 
            or $row['maintainer_user_id3'] == $user ) 
          { $res .= '<option value="'.$row['version_id'].'#'.$row['v_name'].'">'.$row['v_name'].' ('.$row['version_id'].')</option>';
          }
        }
        db_free_result($versions);

        $res2="<option value='255' >".$LNG_FORM[1]."</option>\n";
        $objname = array();
        for ( $x = 0; $x < count($object_dif); $x++ )
        {   $t = "";
            $res2 .= sprintf ("<option value='%s' %s>%s </option>\n"
                    ,$object_dif[$x]
                    ,$t
                    ,$object_dif[$x]
                    );
	    $objname[$object_dif[$x]] = $object_dif[$x];
        }
        
    printf ('<form enctype="multipart/form-data" action="?" method="post">
        <p>
        <b>%s:</b><br />
        <select name="version">%s</select> &nbsp;
        <select name="obj_auw">%s</select> &nbsp;
	</p>', $LNG_FORM[0], $res, $res2 );

?>



<p><b>Are you sure?</b><br />
<input type="radio" name="confirm" id="yes" value="yes"/>
<label for="yes">Yes</label>
 &nbsp;
<input type="radio" name="confirm" id="no" value="no" checked/>
<label for="no">No</label>
</p>

<p>
<input type="submit"  name="submit" value="del Objects" />
<input type="submit"  name="submit" value="del all" />
</p>

</form>


<div align="center">
<table class="tableback" width="600px" align="center"><tr><td>
<p class="justified">
Pressing the button above will purge all existing objects belonging to the selected set,
including their properties and images. It will <b>NOT touch any texts</b>. All texts
data will be preserved and will automatically reassociate with newly uploaded
objects if they have the same name and obj value (type).
</p>

<p class="justified">
There is one exception: if orphan text control will be implemented in the future,
and someone will decide to discard all orphan texts while their objects are purged,
the texts will be discarded.
</p>

</td></tr></table>
</div>

<?php
        //contiune the else part for no psot data
    }

}
    //print_r ($_POST);
    //echo "NOT YET WORKING!";

    //footer, nothing ater this (closes page)
	include_once ("./tpl_script/footer.php");
?>




