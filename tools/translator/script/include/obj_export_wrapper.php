<?php
////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//                 SimuTranslator Object Export Datastructures                //
//                                    PHP 4                                   //
//                                                                            //
//                              Tomas Kubes 2006                              //
//                                                                            //
////////////////////////////////////////////////////////////////////////////////

#require_once ("include/object.php");
require_once ("include/obj_export_base.php");
require_once ("include/parameter.php");


//defined in general.php
//define("TMP_PATH", "/home/st/public_html/tmp/");


//used for parameter passing to vehicle exporter
define("NONE",  1);
define("PSG",   2);
define("TREE",  3);
define("OTHER", 255);


/*
    This calse will be used as base for creating exporters for
    various object types
*/
class obj_export_base
{
    var $image_sheet;
    var $image_sheet_path;
    var $object_list;
    var $dat_file;
    var $dat_file_name;
    var $current_object_no;
    var $next_object_no;
    var $version_id;
    var $version_name;
    var $tile_size;
    var $current_tileX;
    var $current_tileY;

    //this variable wil lhold pointer to function to be caleld after eache processed object
    //need to be set in childs constructor
    var $goto_next_function;

    //type of first object will be used to detect this
    var $image_ordering;

    //basic constructor - reads objects using query
    //query should provide only these 3 fields: version, name, obj
    //as it will be only used to querry for specific objects later
    function obj_export_base ($object_query, $vid)
    {
        //convert number to string
        $vid = ($vid==0)?"0":$vid;

        $this -> image_sheet = 0;
        //sets random export path
        $this -> export_base_path = "";
        $this -> image_sheet_path = "";
        $this -> image_ordering = 0;
        $this -> dat_file = "";
        $this -> current_object_no = 0;
        $this -> next_object_no = 0;
        $this -> version_id = $vid;
        $this -> version_name = db_one_field_query ("SELECT v_name FROM versions WHERE version_id=$vid;");
        //this is here as we do not use sprintf in qurry below
        $this -> tile_size = db_one_field_query ("SELECT tile_size FROM versions WHERE version_id=$vid;");
        $this -> current_tileX = 0;
        $this -> current_tileY = 0;

        //security check, ensure that query begins with select
        //if not stop immediately
        if (stristr($object_query, "SELECT") === FALSE)
        {
            echo "<h1>Invalid query for object selection!</h1>$object_query\n";
            include_once ("include/footer.php");
            die;
        }

        //create /dat and /images directory in some random directory
        $this -> export_base_path = Create_Export_Directories (TMP_PATH);

        print_line ("<h3>Reading Object Data</h3>", 1);
        print_line ("<ul>", 1);

        //now fetch real objects
        //we need to load them al in advance so that it will be possible to determine
        //the sheet size needed for them
        $obj_list_q = db_query($object_query);

        while ($object_line = db_fetch_array($obj_list_q))
        {
            //create object information
            $object = new simu_object($this->version_id, $this->tile_size);
            $object -> load_from_database ($object_line["version_version_id"], $object_line["obj_name"], $object_line["obj"], $this->tile_size);
            //$object -> debug_html ();

            //add to the object array (last spot)
            $this -> object_list[] = $object;

            //output
            print_line ("<li>Read object: " . $object->name . ", type: " . $object->obj . "</li>", 2);

        }
        db_free_result($obj_list_q);

        //finished reading
        print_line ("</ul>", 1);
        print_line ("<p class='tight'>Read " . count($this -> object_list) . " objects.</p><br />",2);
    }

    /*
    //this function is called after processing of each object
    //would be virtual in C++, needs to be defined and assigned to veriable in PHP
    function goto_next_object ()
    {

    }
    */



    function export ()
    {
        print_line ("<h3>Exporting Objects</h3>", 1);

        //traverse all loaded objects
        foreach ($this -> object_list as  $object)
        {
            //create exporter for this object
            $exporter = new simu_object_exporter ($object);

            //export object
            $path_to_be_saved_in_dat_file = "../" . $this->image_sheet_path;
            $exporter -> write_images ($this->image_sheet, $path_to_be_saved_in_dat_file, $this -> current_tileX, $this -> current_tileY);

            //export dat file
            $this -> dat_file .= $exporter -> create_dat_file ();
            $this -> dat_file .= "--------------------------------------------------------------------------------\n";

            //this pointer might be used by goto next function
            $this -> next_object_no++;

            //last thing for this object
            print_line ("<p class='tight'>Exported object: " . $object->name . ", type: " . $object->obj . "</p>",2);

            //initiate updates
            //call to variable containing function (defined in child)
            //the call through variable must be done indirectly (PHP cannot pars the cal to $this->var)
            $goto_n = $this->goto_next_function;
            $this->$goto_n();

            $this -> current_object_no++;
        }
    }

    //this function save actual image sheet
    //does nothing if none is open
    function save_image_sheet ()
    {
        //save old sheet if nay
        if ($this -> image_sheet != 0)
        {
            //store result to check if creatiobn was successful
            $res = imagepng ($this -> image_sheet, $this -> export_base_path . $this -> image_sheet_path);
            imagedestroy ($this -> image_sheet);

             //debug
             //echo "<img src='http://translator.simutrans.com/tmp/" .$this -> image_sheet_path.  "' alt='" . $this->image_sheet_path . "'><br />\n";
             return $res;
        }

        //if we got here, no image sheet was saved
        return FALSE;
    }

    //adds mark to the dat file for new image sheet
    function add_dat_file_sheet_delimiter ()
    {
        //already added by the next object
        //$this -> dat_file .= "-------------------------------------------------------------------------------#\n";
        $this -> dat_file .= "#                                                                              #\n";
        $this -> dat_file .= "#              Next objects have their images in the image sheet:              #\n";
        $this -> dat_file .= "#" .      str_pad ($this -> image_sheet_path, 78 , " ", STR_PAD_BOTH) .       "#\n";
        $this -> dat_file .= "#                                                                              #\n";
        $this -> dat_file .= "--------------------------------------------------------------------------------\n";
    }


    //this function frees the image data, writs the outputs, cleans tmp files...
    function finalize ()
    {
        GLOBAL $exportpfad,$versions_all;
        //saves the last remaining image sheet to file
        $this->save_image_sheet ();

        //user message
        print_line ("<h3>Creating Dat File</h3>", 1);


        //create dat file header
        $dat_header  = "#------------------------------------------------------------------------------#\n";
        $dat_header .= "#                                                                              #\n";
        $dat_header .= "#" . str_pad ("Simutrans dat file: " . $this -> dat_file_name, 78 , " ", STR_PAD_BOTH) . "#\n";
        $dat_header .= "#" . str_pad ("for set: " . $versions_all[$this->version_id] . " ($this->tile_size)", 78 , " ", STR_PAD_BOTH) . "#\n";
        $dat_header .= "#                                                                              #\n";
        $dat_header .= "#" . str_pad ("Generated by SimuTranslator on " . date ("d-M-Y"), 78 , " ", STR_PAD_BOTH) . "#\n";
        $dat_header .= "#                                                                              #\n";
        $dat_header .= "--------------------------------------------------------------------------------\n";

        //create dat file footer
        $dat_footer  = "#\n#\n#\n";
        $dat_footer .= "#------------------------------------------------------------------------------#\n";
        $dat_footer .= "#                                                                              #\n";
        $dat_footer .= "#" .    str_pad ("END OF FILE " . $this -> dat_file_name, 78 , " ", STR_PAD_BOTH) .      "#\n";
        $dat_footer .= "#                                                                              #\n";
        $dat_footer .= "#------------------------------------------------------------------------------#\n";

        //complete the dat file
        $this -> dat_file = $dat_header . $this -> dat_file . $dat_footer;

        //save dat file to the file
        //create a corresponding dat file for makeobj
        $dat_file_handle = fopen( $this -> export_base_path . "dat/" . $this -> dat_file_name, 'w');
        $bytes_written = fwrite ($dat_file_handle, $this -> dat_file);
        fclose($dat_file_handle);

        //add proper bat file
        Create_Bat_File ($this -> export_base_path, $this -> dat_file_name, $this -> tile_size);

        //packs everything, and delete temp files
        $zip_name = str_replace (".dat", ".zip", $this -> dat_file_name);
        zip_target_directory ($this -> export_base_path, TMP_PATH, $zip_name);

        copy ( TMP_PATH.$zip_name, "../".$exportpfad.$zip_name );
        
        //link output file
        echo ('<h3><a href="'.$exportpfad.$zip_name.'">Download '.$zip_name.'</a></h3>');

    }
}

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
class vehicle_exporter extends obj_export_base
{
    //number of used rows in the sheet
    var $filled_rows;

    //this will be basically used only for sheet numbering
    var $processed_sheets;

    var $cargo_type;

    //only used to name sheets properly
    var $vehicle_type;

    function create_image_sheet ()
    {
        //save old sheet if nay
        $this->save_image_sheet ();

        //create sheet to be used for pasting images
        //vehicles - sheets have standrad size
        $this -> image_sheet = imagecreatetruecolor (9*$this->tile_size, VEHICLE_SHEET_ROWS*$this->tile_size);

        $this -> image_sheet_path = "images/vehicles-" . strtolower ($this -> vehicle_type) . "-". strtolower ($this -> cargo_type) . "-" . $this -> version_name. "-" . str_pad ($this -> processed_sheets, 2, "0", STR_PAD_LEFT) . ".png";

        $this -> processed_sheets ++;

        //note new sheet in the dat file
        $this -> add_dat_file_sheet_delimiter ();

        //user info
        print_line ("<p class='tight'><b>Created sheet: " . $this -> image_sheet_path . "</b></p>",2);
    }



    function goto_next_object ()
    {
        //get the size (we are only interest in the Y
        $size = determine_occupied_tiles (VEHICLE, $this->object_list[$this->current_object_no]->image_count ());
        $this -> filled_rows += $size[1];
        $this -> current_tileY = $this -> filled_rows;

        //do not continue further if we processed last object
        if ($this -> next_object_no >= count($this -> object_list))
        {
            return;
        }

        //if the next object wont fit, open new sheet.
        $next_size = determine_occupied_tiles (VEHICLE, $this->object_list[$this->next_object_no]->image_count ());
        if (($this -> filled_rows + $next_size[1]) > VEHICLE_SHEET_ROWS)
        {
            //clean row count
            $this -> filled_rows = 0;
            $this -> current_tileY = $this -> filled_rows;

            //open new sheet
            $this -> create_image_sheet ();
        }
    }


    //constructor - choose which vehicle type to export
    //way_type and engine_only, psg_mail, other_trailer
    function vehicle_exporter ($vid, $way_type, $cargo_type, $ordering_style)
    {
        //version id transformation
        $vid = ($vid==0)?"0":$vid;

        //vehicle specific assingemnts
        $this -> filled_rows = 0;
        $this -> processed_sheets = 0;
        $this -> vehicle_type = $way_type;
        $this -> cargo_type = $cargo_type;

        //wee need to decide if we need to select intro year or not
        $use_intro_y = ($ordering_style == "YEAR")?TRUE:FALSE;

        //$freight_condition = "LCASE(q.p_value)='none'";
        $freight_condition = "q.p_value='None'";

        //ordering string for query (default)
        $ordering = ($use_intro_y?"r.p_value ASC, ":"") . "o.obj_name ASC";

        //way type condition
        $way_type_condition = "p.p_value='$way_type'";
        if ($way_type == "track")
        {
            //track must include electrified track
            $way_type_condition = "(LCASE(p.p_value)='track' OR LCASE(p.p_value)='electrified_track')";
        }


        if($cargo_type == "PSG")
        {
            $freight_condition = "(q.p_value='Passagiere' OR q.p_value='Post')";
            //not needed, uses default
            //$ordering = "r.p_value ASC, o.obj_name ASC";
        }elseif($cargo_type == "OTHER")
        {
            $freight_condition = "q.p_value<>'Passagiere' AND q.p_value<>'Post' AND q.p_value<>'None'";
            $ordering = "q.p_value ASC, ". ($use_intro_y?"r.p_value ASC, ":"") . "o.obj_name ASC";
        }

        //query needs to be combined from pieces depending on the fact if we use time ordering or not
        $object_query = "SELECT o.version_version_id, o.obj_name, o.obj FROM objects o JOIN property p ON (o.obj_name=p.having_obj_name AND o.version_version_id=p.having_version_version_id) JOIN property q ON (o.obj_name=q.having_obj_name AND o.version_version_id=q.having_version_version_id) " . ($use_intro_y?"JOIN property r ON (o.obj_name=r.having_obj_name AND o.version_version_id=r.having_Version_version_id) ":"") . "WHERE o.version_version_id=$vid AND o.obj='vehicle' AND p.p_name='waytype' AND $way_type_condition " . ($use_intro_y?"AND r.p_name='intro_year' ":"") . "AND q.p_name='freight' AND $freight_condition ORDER BY $ordering;";
        echo "<!-- $object_query -->\n";

        //need to call constructor of parrent manually
        $this -> obj_export_base ($object_query, $vid);

        //create name for dat file
        $this -> dat_file_name = "vehicles-" . strtolower ($this -> vehicle_type) . "-". strtolower ($this -> cargo_type) . "-" . $this -> version_name . ".dat";

        //assign goto next function
        $this -> goto_next_function = 'goto_next_object';

        //oopen first image sheet
        $this -> create_image_sheet ();
    }
}



////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
class tree_exporter extends obj_export_base
{
    //number of used rows in the sheet
    var $filled_rows;

    //this will be basically used only for sheet numbering
    var $processed_sheets;

    function create_image_sheet ()
    {
        //save old sheet if nay
        $this->save_image_sheet ();

        //create sheet to be used for pasting images
        //vehicles - sheets have standrad size
        $this -> image_sheet = imagecreatetruecolor (TREE_SHEET_COLUMNS*$this->tile_size, TREE_SHEET_ROWS*$this->tile_size);

        $this -> image_sheet_path = "images/trees-" . str_pad ($this -> processed_sheets, 2, "0", STR_PAD_LEFT) . ".png";

        $this -> processed_sheets ++;

        //note new sheet in the dat file
        $this -> add_dat_file_sheet_delimiter ();

        //user info
        print_line ("<p class='tight'><b>Created sheet: " . $this -> image_sheet_path . "</b></p>",2);
    }



    function goto_next_object ()
    {
        //get the size (we are only interest in the Y
        $size = determine_occupied_tiles (TREE, $this->object_list[$this->current_object_no]->image_count ());
        $this -> filled_rows += $size[1];
        $this -> current_tileY = $this -> filled_rows;

        //do not continue further if we processed last object
        if ($this -> next_object_no >= count($this -> object_list))
        {
            return;
        }

        //if the next object won't fit, open new sheet.
        $next_size = determine_occupied_tiles (TREE, $this->object_list[$this->next_object_no]->image_count ());
        if (($this -> filled_rows + $next_size[1]) > TREE_SHEET_ROWS)
        {
            //clean row count
            $this -> filled_rows = 0;
            $this -> current_tileY = $this -> filled_rows;

            //open new sheet
            $this -> create_image_sheet ();
        }
    }


    //constructor - choose which vehicle type to export
    //way_type and engine_only, psg_mail, other_trailer
    function tree_exporter ($vid)
    {
        //version id transformation
        $vid = ($vid==0)?"0":$vid;

        //vehicle specific assingemnts
        $this -> filled_rows = 0;
        $this -> processed_sheets = 0;

        //$object_query = "SELECT o.Version_version_id, o.obj_name, o.obj FROM Objects o JOIN Property p ON (o.obj_name=p.having_obj_name AND o.Version_version_id=p.having_Version_version_id) JOIN Property q ON (o.obj_name=q.having_obj_name AND o.Version_version_id=q.having_Version_version_id) " . ($use_intro_y?"JOIN Property r ON (o.obj_name=r.having_obj_name AND o.Version_version_id=r.having_Version_version_id) ":"") . "WHERE o.Version_version_id=$vid AND o.obj='vehicle' AND p.p_name='waytype' AND $way_type_condition " . ($use_intro_y?"AND r.p_name='intro_year' ":"") . "AND q.p_name='freight' AND $freight_condition ORDER BY $ordering;";
        $object_query = "SELECT o.version_version_id, o.obj_name, o.obj FROM objects o JOIN property p ON (o.obj_name=p.having_obj_name AND o.version_version_id=p.having_version_version_id) WHERE o.version_version_id=$vid AND o.obj='tree' AND p.p_name='height' ORDER BY CAST(p.p_value AS UNSIGNED) ASC, o.obj_name ASC;";
        echo "<!-- $object_query -->\n";

        //need to call constructor of parent manually
        $this -> obj_export_base ($object_query, $vid);

        //create name for dat file
        $this -> dat_file_name = "trees-" . $this -> version_name . ".dat";

        //assign goto next function
        $this -> goto_next_function = 'goto_next_object';

        //oopen first image sheet
        $this -> create_image_sheet ();
    }
}






////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
class city_building_exporter extends obj_export_base
{
    //this will hold currently processed building level
    var $current_level;

    //no of occupied tiles (x, y)
    var $current_building_size;

    //com, inde, res
    var $building_type;

    //object levels for processing
    //>=
    var $lower_boundary;
    //<
    var $upper_boundary;



    function goto_next_object ()
    {
        //get the size
        $this->current_building_size = determine_occupied_tiles (CITY_BUILDING, $this->object_list[$this->current_object_no]->image_count ());

        //in general, just move down
        $this -> current_tileY += $this->current_building_size[1];

        //do not continue further if we processed last object
        if ($this -> next_object_no >= count($this -> object_list))
        {
            return;
        }

        //go to next column in image sheet if next object has higher level
        if ($this -> current_level != $this -> object_list[$this -> next_object_no] -> get_attribute_value ("level"))
        {
            //either go for new column or new sheet
            //new sheet every when we will be at upper boundary of current one
            if (($this -> object_list[$this -> next_object_no] -> get_attribute_value ("level")) >= $this -> upper_boundary)
            {
                //create new image sheet (save and close old one)
                $this -> create_image_sheet ($this -> upper_boundary, $this -> upper_boundary + BUILDING_SHEET_COLUMNS);

                //reset values
                $this -> current_tileX = 0;
                $this -> current_tileY = 0;
            }
            else
            {
                //go to next column (city b always occupy one column)
                $this -> current_tileX ++;

                //reset Y
                $this -> current_tileY  = 0;
            }

            //update level info
            $this -> current_level = $this -> object_list[$this -> next_object_no] -> get_attribute_value ("level");
        }
    }

    //this function determines the sheet size for objects
    //with level >= start and < end_index
    //retruns array (x,y) in number of tiles
    function determine_sheet_size ($start_lvl, $end_lvl)
    {
        //determine the sheet size (scan for the sizes of all objectes to be exported
        $rowY = 0;
        $lvl  = 0; //no lvl 0 - dummy
        $tilesX = 0;
        $tilesY = 0;

        for ($i = 0; $i < count ($this -> object_list); $i++)
        {
            //skip any object that do not have our lvl
            //now it checks correct objects (though I do not exactly uinderstand why)
            if (($this ->object_list[$i]->get_attribute_value ("level") < $start_lvl) or ($this ->object_list[$i]->get_attribute_value ("level") >= $end_lvl))
            {
                continue;
            }

            //get size for this building
            $bldg_s = determine_occupied_tiles (CITY_BUILDING, $this->object_list[$i]->image_count ());

            //do we start new row?
            //if we got to new level, store values for previous one (row)
            //on the first rune this is also colled setting the width to 1 and a correct level
            //intended behavior
            if ($lvl != $this ->object_list[$i]->get_attribute_value ("level"))
            {
                //each level takes 1 column
                $tilesX++;
                $tilesY  = ($tilesY < $rowY)?$rowY:($tilesY); //only keep gretest y dimension
                $rowY = 0;
                $lvl  = $this ->object_list[$i]->get_attribute_value ("level");
            }

            $rowY  += $bldg_s[1];
        }

        //and for last level
        $tilesY  = ($tilesY < $rowY)?$rowY:($tilesY); //only keep gretest y dimension

        return array ($tilesX, $tilesY);
    }

    function create_image_sheet ($new_lower_boundary, $new_upper_boundary)
    {
        //if old sheet is open, save it
        $this->save_image_sheet ();

        //mar next saving area
        $this -> lower_boundary = $new_lower_boundary;
        $this -> upper_boundary = $new_upper_boundary;

        //sheet for every 5 levels
        $sheet_size = $this -> determine_sheet_size ($new_lower_boundary, $new_upper_boundary);
        $this -> image_sheet_path = "images/buildings-city-" . strtolower ($this -> building_type). "-" . $this -> version_name . "-" . str_pad ($this -> current_level, 3, "0", STR_PAD_LEFT) . ".png";

        //create sheet to be used for pasting images
        $this -> image_sheet = imagecreatetruecolor ($sheet_size[0]*($this->tile_size) , $sheet_size[1]*($this->tile_size));

        //note new sheet in the dat file
        $this -> add_dat_file_sheet_delimiter ();

        //user info
        print_line ("<p class='tight'><b>Created sheet: " . $this -> image_sheet_path . ", size: " . $sheet_size[0]*($this->tile_size) . "x" . $sheet_size[1]*($this->tile_size) . " px</b></p>",2);
    }


    //constructor (last - we need to know functions it will use)
    function city_building_exporter ($vid, $type)
    {
        $this -> building_type = $type;

        //prepare the query (since we do not use sprintf correct vid)
        $vid = ($vid==0)?"0":$vid;
        //$object_query = "SELECT o.obj_name, o.Version_version_id, o.obj, o.image_path, o.obj_copyright,  FROM Objects o JOIN Property p ON (o.obj_name=p.having_obj_name AND o.Version_version_id=p.having_Version_version_id) JOIN Property q ON (o.obj_name=q.having_obj_name AND o.Version_version_id=q.having_Version_version_id) WHERE o.Version_version_id=$vid AND o.obj='building' AND p.p_name='type' AND p.p_value='res' AND q.p_name='level' AND q.p_value>0  AND q.p_value < 19 ORDER BY q.p_value ASC, o.obj_name ASC;";
        //select all buildings of given type and order by level and then name                                                                                                                                                                                                                                                                                                                                                 // LCASE(
        $object_query = "SELECT o.obj_name, o.version_version_id, o.obj, o.image_path, o.obj_copyright FROM objects o JOIN property p ON (o.obj_name=p.having_obj_name AND o.version_version_id=p.having_version_version_id) JOIN property q ON (o.obj_name=q.having_obj_name AND o.version_version_id=q.having_version_version_id) WHERE o.version_version_id=".$vid." AND o.obj='building' AND p.p_name='type' AND LCASE(p.p_value)='".$type."' AND q.p_name='level' AND q.p_value>0 ORDER BY LPAD(q.p_value, 2,'0') ASC, o.obj_name ASC;";

        //need to call constructor of parent manually
        //now we have filled the object list with data
        $this -> obj_export_base ($object_query, $vid);

        //set the name of dat file
        $this -> dat_file_name = "buildings-city-" . strtolower ($type) .  "-" . $this -> version_name . ".dat";

        //assign goto next function
        $this -> goto_next_function = 'goto_next_object';

        //set level of first building
        $this -> current_level  = $this -> object_list[0] -> get_attribute_value ("level");
        $this -> lower_boundary = $this -> current_level;

        //prepare image sheet
        $this -> create_image_sheet ($this -> lower_boundary, $this -> lower_boundary + BUILDING_SHEET_COLUMNS);
    }
}




?>
