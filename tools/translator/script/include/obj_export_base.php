<?php
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//                SimuTranslator Object 1 Object Export Class                 //
//                                    PHP 4                                   //
//                                                                            //
//                              Tomas Kubes 2006                              //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

die ('nicht aktiv');


require_once ("include/general.php");
require_once ("include/object.php");

/*
    These constants are used for internal communication between routines
    keep 0 free as it will be used to determine faulty condition (FALSE)
*/

//city building top-down, special image ordering
define("CITY_BUILDING", 1);

//any x*y building
//define("SPECIAL_BUILDING", 2);

//building with icon (rest like above)
//define("PLAYER_BUILDING",  3);

//any vehicle left to right
define("VEHICLE", 4);

//way
//define("WAY", 5);

//ground objects
//define("TERRAIN", 5);

define("TREE", 7);

////////////////////////////////////////////////////////////////////////////////
define("VEHICLE_SHEET_COLUMNS", 9);
define("VEHICLE_SHEET_ROWS", 10);
define("BUILDING_SHEET_COLUMNS", 10);
define("TREE_SHEET_COLUMNS", 6);
define("TREE_SHEET_ROWS", 8);


////////////////////////////////////////////////////////////////////////////////
//determines the type of image ordering used for this object
//if not yet set, returns FALSE
function determine_tile_placing ($obj, $image_count, $additional_type = FALSE)
{
    if ($obj == "vehicle")
    {
        return VEHICLE;
    } elseif ($obj == "tree")
    {
        return TREE;
    } elseif ($obj == "building")
    {
        if ((strcasecmp($additional_type, "com") == 0) or (strcasecmp($additional_type, "ind") == 0) or (strcasecmp($additional_type, "res") == 0))
        {
            return CITY_BUILDING;
        } else
        {
            return FALSE;
        }
    }

    return FALSE;
}


/*
    This function will determine the space occupied by object images in
    the image sheet.

    returns array(tilesX, tilesY)
*/
function determine_occupied_tiles ($placing, $image_count)
{
    //$res = array (0, 0);

    if ($placing == VEHICLE)
    {
        //9 do not forget 1 information tile
        //ceil returns float
        return array (VEHICLE_SHEET_COLUMNS, ((int) ceil ($image_count / 8)));

    }  elseif ($placing == TREE)
    {
        //ceil returns float
        return array (TREE_SHEET_COLUMNS, ((int) ceil ($image_count / 5)));

    } elseif ($placing == CITY_BUILDING)
    {
        //do not forget information tile row
        return array (1, $image_count + 1);
    }

    return FALSE;
}


/*
This function creates export directories and returns base path
root for /dat and /images ditrectory
*/
function Create_Export_Directories ($tmp_path)
{
    //create random directory
    $base_path = Create_Temp_Directory ($tmp_path);

    //create specific directory structure
    //php 4 does not know recursive attribut for mkdir
    $success1 = mkdir ($base_path . "images/", 0777);
    $success2 = mkdir ($base_path . "dat/",    0777);
    if (!($success1 AND $success2))
    {
        echo "<h1>Creation Failed!</h1>";
        require_once ("include/footer.php");
    }
    print_line ("<p class='tight'>Success!</p>");

    return $base_path;
}

//this function creates bat file for make obj
//returns FALSE or bat file name on succes
function Create_Bat_File ($base_path, $dat_file_name, $tile_size)
{
    //create name for pak file
    $base_name = str_replace (".dat", "", $dat_file_name);

    //create content
    $bat_file = "makeobj.exe pak$tile_size ./$base_name.pak ./dat/$dat_file_name > compilation_result_$base_name.txt\n";

    //write to the file
    $file_handle = fopen($base_path . "compile-$base_name.bat", 'w');
    $bytes_written = fwrite ($file_handle, $bat_file);
    fclose($file_handle);

    return ($bytes_written == 0)?FALSE:($base_path . "compile-$base_name.bat");
}



/*
    This class will take care of the export of one single object
    (writing images to the image sheet, creating dat file string)

    Created from the object holding simu_object data
*/
class simu_object_exporter
{
    var $simu_object;
    var $image_ordering;

    //constructor
    //only keep reference to the object, do not copy, we will modify it!
    function simu_object_exporter (&$obj)
    {
        $this -> simu_object = $obj;
        $this -> image_ordering = determine_tile_placing ($obj->obj,$obj->image_count(), $obj->get_attribute_value("type"));
    }

    function write_info_tile (&$image_sheet, $target_tileX, $target_tileY)
    {
        //determine font (smaller tiles need smaller font!)
        $font_number = ((($this->simu_object->tile_size) < 100))?1:4;
        $height      = ((($this->simu_object->tile_size) < 100))?10:15;

        //convert tile to pixel position
        $startX = $target_tileX*($this->simu_object->tile_size);
        $startY = $target_tileY*($this->simu_object->tile_size);

        //at first, create the "notes area" - white
        imagefilledrectangle ($image_sheet, $startX, $startY, $startX + ($this->simu_object->tile_size) - 1, $startY + ($this->simu_object->tile_size) - 1, 0xFFFFFF);

        //write info to the image
        //fonts 1 - times?
        //      3 - courier bold
        imagestring ($image_sheet, $font_number, $startX + 1, $startY + 1          , $this->simu_object->name, 0x000000);
        imagestring ($image_sheet, $font_number, $startX + 1, $startY + 1 + $height, $this->simu_object->obj, 0x000000);

        imagestring ($image_sheet, $font_number, $startX + 1, $startY + 5 + 2*$height, $this->simu_object->copyright, 0x000000);
        imagestring ($image_sheet, $font_number, $startX + 1, $startY + 5 + 3*$height, date("d-M-Y", $this->simu_object->mod_date), 0x000000);

        imagestring ($image_sheet, $font_number, $startX + 1, $startY + 10+ 4*$height, $this->simu_object->img_state, ((($this->simu_object->img_state)=="active")?0x000000:0xFF0000));
    }

    //this function copies one tile to the iamge sheet (and saves data for dat file creation)
    //input is a database row (image name, image data)
    //returns TRUE on success
    function write_image_tile (&$db_row, &$image_sheet, &$image_file_path, $target_tileX, $target_tileY)
    {
        //pass the string data - png file(as reference)
        //and convert them to image resource
        $image_tile = imagecreatefromstring($db_row[1]);
        if ($image_tile == FALSE)
        {
            echo "<h3>Error: obj_export_inc.php : simu_object_exporter->write_image_tile() : Unable to open the image ($db_row[0]) for object ", $this->simu_object->name, " !</h3>\n";
            return FALSE;
        }

        //compute target destination in pixels
        $tpX = $target_tileX*($this->simu_object->tile_size);
        $tpY = $target_tileY*($this->simu_object->tile_size);

        //check if target sheet is big enough
        if(($tpX < 0) or ($tpY < 0) or (($tpX+($this->simu_object->tile_size)-1) > imagesx($image_sheet)) or (($tpY+($this->simu_object->tile_size)-1) > imagesy($image_sheet)))
        {
            echo "<h3>Error: obj_export_inc.php : simu_object_exporter->write_image_tile() : Requested to copy image tile ($db_row[0]) for object ", $this->simu_object->name, " outside of sheet x:$tpX, y:$tpY!</h3>\n";
            return FALSE;
        }

        //copy th tile to the target
        //bool imagecopy ( resource dst_im, resource src_im, int dst_x, int dst_y, int src_x, int src_y, int src_w, int src_h )
        $succ = imagecopy ($image_sheet, $image_tile, $tpX, $tpY, 0, 0, $this->simu_object->tile_size, $this->simu_object->tile_size);
        imagedestroy ($image_tile);

        //update the object image information with stored path and tile position
        $this->simu_object -> update_image_info ($db_row[0], $image_file_path, $target_tileX, $target_tileY);

        //return the result of copy operation
        return $succ;
    }


    //this will do all the work creating image sheet
    //(and prepares data for dat file write)
    //sheet is passed as reference as it will be modified
    function write_images ($image_sheet, $image_file_path, $start_tileX, $start_tileY)
    {
        //city buildings use special ordering of images (so thet image on tile sheet is nice top-bottom)
        $ordering = ((($this -> image_ordering)==CITY_BUILDING)?"DESC":"ASC");

        //write the info tile (always to the begining)
        $this -> write_info_tile ($image_sheet, $start_tileX, $start_tileY);

        //determine first target tile for image
        //vehicles start to the lef, other images start to the bottom
        $current_tileX = $start_tileX + (((($this->image_ordering)==VEHICLE) or (($this->image_ordering)==TREE))?1:0);
        $current_tileY = $start_tileY + ((($this->image_ordering)==CITY_BUILDING)?1:0);

        //now extract the images
        //get the data from db and detects format automatically.
        //resource imagecreatefromstring()
        $image_query = db_query ("SELECT image_name, image_data FROM images WHERE object_obj_name='" . $this ->simu_object->name . "' AND object_version_version_id=" . $this ->simu_object->version_id . " ORDER BY image_order, image_name $ordering;");
        $images_processed = 0;
        while ($tile_data = db_fetch_row($image_query))
        {
            $succ = $this -> write_image_tile ($tile_data, $image_sheet, $image_file_path, $current_tileX, $current_tileY);
            if ($succ == FALSE)
            {
                echo "<h3>Error: obj_export_inc.php : simu_object_exporter->write_images() : Image tile copy for object ", $this->simu_object->name, " to the image sheet failed ($tile_data[0])!</h3>\n";
                return FALSE;
            }

            //if we got here, image was saved sucessfully
            $images_processed++;

            //move to next tile
            if (($this->image_ordering)==VEHICLE)
            {
                //go one row down and back to the left afterp rocessing 8 images, except for first image
                if ((($images_processed % 8) == 0) AND ($images_processed != 0))
                {
                    $current_tileX = 1;
                    $current_tileY ++;

                }else
                {
                    $current_tileX++;
                }
            } elseif (($this->image_ordering)==TREE)
            {
                //go one row down and back to the left afterp rocessing 8 images, except for first image
                if ((($images_processed % 5) == 0) AND ($images_processed != 0))
                {
                    $current_tileX = 1;
                    $current_tileY ++;

                }else
                {
                    $current_tileX++;
                }
            }

            elseif (($this->image_ordering)==CITY_BUILDING)
            {
                $current_tileY ++;
            }
        }

        db_free_result($image_query);

        //check if we processed all images
        if ($images_processed != ($this ->simu_object->image_count ()))
        {
            echo "<h3>Error: obj_export_inc.php : simu_object_exporter->write_images() : Did not save all object's images to the image sheet!</h3>\n";
            return FALSE;
        }

        return TRUE;
    }


    //generates dat file
    //now only need to call function of object
    //since everything should be prepared
    function create_dat_file ()
    {
        return $this -> simu_object -> generate_dat_file ();
    }

}

?>
