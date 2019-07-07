<?php
  //header, no output before this (sends header information)
  $title = "CSV Object Importer";
  include("./tpl_script/header.php");

	//list of all user typs allowed on this page
	//header will block access for all other users
  $minimal_user_level=array('admin','pakadmin');
  if ( !isset($_SESSION['role']) or  !compare_userlevels($minimal_user_level, $_SESSION['role']) 
  { include("./tpl_script/main.php");
    include('./tpl_script/footer.php');
    die();
  }

 include ("include/object.php");

  

/*
    Takes the name of file from the form,
    reads it, retrieves it from upload temp,
    opens it, informs user, parse the cvs lines,
    and returns the result.
    (cleans afterwards)

    Careful: values are not trimmed
*/
function read_uploaded_cvs_file ($post_file_name)
{
    /*
    The contents of $_FILES from the example form is as follows. Note that this assumes the use of the file upload name userfile, as used in the example script above. This can be any name.

    $_FILES['userfile']['name']
    The original name of the file on the client machine.

    $_FILES['userfile']['type']
    The mime type of the file, if the browser provided this information. An example would be "image/gif". This mime type is however not checked on the PHP side and therefore don't take its value for granted.

    $_FILES['userfile']['size']
    The size, in bytes, of the uploaded file.

    $_FILES['userfile']['tmp_name']
    The temporary filename of the file in which the uploaded file was stored on the server.

    $_FILES['userfile']['error']
    */

    //sets path for target file (name it after user to prevent confusion)
    $target_file_path = TMP_PATH . $_SESSION['userId'] ."_cvs_file.cvs";

    //get file from upload temp to our temp
    $upload_ok = move_uploaded_file($_FILES[$post_file_name]['tmp_name'], $target_file_path);
    if ($upload_ok)
    {
        //user message
        print_line ("<p class='tight'>Uploaded file: " . $_FILES[$post_file_name]['name'] . ", size: " . $_FILES[$post_file_name]['size'] . "b</p>", 1);
    } else
    {
        print_line ("<h2>ERROR: Unable to open input file. Upload failed!</h2>", 1);
        print_line ("<p class='tight'>" . $_FILES[$post_file_name]['tmp_name'] . "</p>", 1);
        return FALSE;
    }

    //open the file
    $cvs_file = fopen ($target_file_path, "rt");
    if ($cvs_file == '')
    {
        print_line ("<h2>ERROR: Unable to open input file. Upload failed!</h2>", 1);
        print_line ("<p class='tight'>$target_path</p>", 1);
        return FALSE;
    }

    //read it
    //array fgetcsv ( resource handle [, int max_length [, string delimiter [, string enclosure]]] )
    $result = array ();
    while ($row = fgetcsv ($cvs_file, 1000, ";"))
    {
        //parseRow($row,$object);
        $result[] = $row;
        //print_r ($row);
    }
    fclose ($cvs_file);

    //delete the temp file
    unlink ($target_file_path);

    return $result;
}


////////////////////////////////////////////////////////////////////////////////
function import_cvs_data ($vid, $post_file_name, $verbose = "quiet")
{
    //reads uploaded user file -
    $cvs_data = read_uploaded_cvs_file ($post_file_name);

    //stop if upload failed
    if ($cvs_data == FALSE) return FALSE;

    //find numbers of most important properties and create key map for others
    //first row must contain field names
    $obj_name_key = 0;
    $obj_type_key = 0;
    $field_names = array ();
    foreach ( $cvs_data[0] as $key => $property_name)
    {
        //trim
        $property_name = trim ($property_name);

        //get important keys
        if (($property_name == "name") OR ($property_name == "obj_name")) $obj_name_key = $key;
        if (($property_name == "type") OR ($property_name == "obj")) $obj_type_key = $key;

        //store other to map
        $field_names[$key] = $property_name;
    }

    //debug
    //print_line ("<p class='tight'>keys: name:$obj_name_key, type: $obj_type_key</p>", 2);

    print_line ("<ul>", 1);
    foreach ($cvs_data as $line_no => $cvs_line)
    {
        //skip first line
        if ($line_no == 0) continue;

        //retrieve given object from database
        $object = new simu_object ($vid, 0);
        $found = $object -> load_from_database ($vid, trim($cvs_line[$obj_name_key]), trim($cvs_line[$obj_type_key]));
        if ($found == FALSE)
        {
            print_line ("<h2>Object Not Found in the DB!</h2>", 2);
            print_line ("<p class='tight'>vid.: $vid, name: $cvs_line[$obj_name_key], type: $cvs_line[$obj_type_key]</p>", 2);
            return FALSE;
        }

        print_line ("<li><strong>$object->name, $object->obj</strong>", 2);
        print_line ("<ul>", 3);
        $changes = FALSE;
        //object was found, modify it
        //do not touch name, type and vid (or copyright), only porperties
        foreach ($cvs_line as $property_no => $property_value)
        {
            //print_line ("<p class='tight'>$field_names[$property_no] = $property_value</p>", 3);
            //$object->attributes = array ();
            $property_value = trim ($property_value);

            //special treatment for properties not stored in property array (skip)
            $property_name = $field_names[$property_no];
            if (($property_name == "obj_name") OR ($property_name == "name")
                 OR ($property_name == "type")  OR ($property_name == "obj")
                OR ($property_name == "comments"))
            {
                if ($verbose == 'verbose') print_line ("<li class='grey'>Skipped $property_name</li>", 4);
                continue;
            } elseif (($property_name == "obj_copyright")  OR ($property_name == "copyright"))
            {
                //store copyright
                if ($object->copyright != $property_value)
                {
                    $object->copyright = $property_value;
                    print_line ("<li>copyright <strong>changed</strong> to: '$property_value'</li>", 4);
                    $changes = TRUE;
                }
                continue;
            }

            //check if object already has property with this name
            //we need to distinguish it, since we do not want to add values with value 0 (but we might want to change them)
            if (isset($object->attributes[$field_names[$property_no]]))
            {
                //then update it if it has changed
                //identity checks to pass "0" updates
                if ($object->attributes[$field_names[$property_no]] !== $property_value)
                {
                    //update value or remove the entry
                    print_line ("<li>$field_names[$property_no] <strong>updated</strong> from: '" . $object->attributes[$field_names[$property_no]] . "' to: '$property_value'</li>", 4);
                    $object->attributes[$field_names[$property_no]] = $property_value;
                    $changes = TRUE;

                }else
                {
                    if ($verbose == 'verbose') print_line ("<li class='grey'>$field_names[$property_no] not changed, value '$property_value'</li>", 4);
                }


            } else
            {
                //skip empty values
                //($property_value == 0) this skipped when on tried to set value to 0  OR
                //test for identity, because we want to insert "0" (which causes havoc otherwise at it is evaluated to 0 - FALSE as well otherwise)
                if ($property_value === "") continue;

                //otherwise
                //add new property
                $object->attributes[$field_names[$property_no]] = $property_value;
                print_line ("<li>$field_names[$property_no] <strong>added</strong> with value: '$property_value'</li>", 4);
                $changes = TRUE;

            }

        }

        if ($changes == FALSE)
        {
            print_line ("<li class='grey'>No changes</li>", 4);

        }else
        {
            //if changed, update object!
            //do not touch images - dat file path not necessary
            print_line ("<li>", 4);
            $object->save_object_to_db ("", FALSE);
            print_line ("</li>", 4);
        }

        print_line ("</ul>", 3);

        print_line ("</li>", 2);

        //print_line ("<p class='tight'>$cvs_line[$obj_name_key], $cvs_line[$obj_type_key]</p>", 2);
        //print_r($cvs_line);

    }
    print_line ("</ul>", 1);
}






////////////////////////////////////////////////////////////////////////////////
//this function will output xhtml input form
function print_selection_menu ()
{
    print_line ("<h2>Please Select File to Upload</h2>", 0);

    print_line ("<div class='width600'>", 0);
    print_line ("<form enctype='multipart/form-data' action='obj_cvs_import.php' method='post' id='obj_cvs'>", 1);


    version_Selection_menu ();

    //way_type_selection_menu ();

    print_line ("<p><b>Select CVS file:</b><br />", 1);
    print_line ("<input type='file' name='cvs_file' size='35' />", 1);
    print_line ("</p>", 1);

    print_line ("<p><b>Comment Mode:</b><br />", 1);
    print_line ("<select name='mode'>", 1);
    print_line ("<option value='verbose' selected='selected'>Verbose</option>", 2);
    print_line ("<option value='quiet'>Quiet</option>", 2);
    print_line ("</select>", 1);
    print_line ("</p>", 1);


    print_line ("<p><input type='submit' name='submit' value='Import' /></p>", 1);
    print_line ("</form>", 1);

    info_box ("This page allows you to upload a cvs file and directly modify properties of
               <strong>existing objects</strong> in the SimuTranslator database.
               Supported delimiter is ; , first line must contain field names.
               Objects are matched by <strong>name</strong>, <strong>type</strong>, and version selected on this page.
               Object names and types cannot be changed this way.",
              "Complete user's manual can be found in <a href='http://translator.simutrans.com/help/object_cvs.htm'>SimuTranslator Help</a>",
              "Please note that this is admin's tool and that there are no user input checks. Misplaced semicolon will lead to catastrophic results.");

    print_line ("</div>", 0);
}



////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////// M A I N ////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//check if we have post data
if (isset($_POST["submit"]))
{
    //display statistics
    //adjust version id to string
    $vid = ($_POST["version"]==0)?"0":$_POST["version"];

    //check if thiongs were selected
    // OR ($_POST["cvs_file"] == "")
    if (($vid == 255) OR ($_FILES['cvs_file']['tmp_name']== ""))
    {
         //wrong post data
	    print_line ("<h2>I do not understand your request.</h2>", 1);
        print_line ("<p>Please fill all fields properly.</p>");

    }else
    {
        //import (will get file from post data)
        import_cvs_data ($vid, 'cvs_file', $_POST["mode"]);
    }

    print_line ("<h3 class='center'><a href='obj_cvs_import.php'>Back to Target Selection Menu</a></h3>");
    print_line ("<h3 class='center'><a href='main.php'>Back to Main Menu</a></h3>");


} else
{
    //display selection menu
    print_selection_menu ();
}


//footer, nothing ater this (closes the page)
include_once ("include/footer.php");
?>
