<?php
/*
    .Dat file parser for SimuTranslator - object importer

	encoding = iso8859-2

    Tomas Kubes
    2005
*/
//These variables need to be global because of performance reasons.
//(allow passing opened image between objects (otherwise import always ends in timeout))
//this is not really clean solution, I know
/*but it is a little price to pay for rather SIGNIFICANT performance increase
  (at least for pak128 and its 1152*1280 images which are common for tens of objects and take long to load.*/
$open_image = 0;
$open_path  = "";

////////////////////////////////////////////////////////////////////////////////
// this function converts image name to the nuber to achieve some natural image ordering
// default value is 128
// evaluation error 5
function determine_image_order ($image_name = "", $obj_type = "")
{
    //fall to default if information incomplete
    if (($image_name == "") OR ($obj_type== "")) return 128;

    //simple maping aray maps images to numbers
    //this will allow to order them nicely on ouput without much effort
    static $order_map = array (
                               "image[w]"         => 31,
                               "image[nw]"        => 32,
                               "image[n]"         => 33,
                               "image[ne]"        => 34,
                               "image[e]"         => 35,
                               "image[se]"        => 36,
                               "image[s]"         => 37,
                               "image[sw]"        => 38,

                               "emptyimage[w]"    =>  50,
                               "emptyimage[nw]"   =>  51,
                               "emptyimage[n]"    =>  52,
                               "emptyimage[ne]"   =>  53,
                               "emptyimage[e]"    =>  54,
                               "emptyimage[se]"   =>  55,
                               "emptyimage[s]"    =>  56,
                               "emptyimage[sw]"   =>  57,

                               "freightimage[w]" =>   150,
                               "freightimage[nw]" =>  151,
                               "freightimage[n]" =>   152,
                               "freightimage[ne]" =>  153,
                               "freightimage[e]" =>   154,
                               "freightimage[se]" =>  155,
                               "freightimage[s]" =>   156,
                               "freightimage[sw]" =>  157,

                               "freightimage[0][w]" =>   160,
                               "freightimage[0][nw]" =>  161,
                               "freightimage[0][n]" =>   162,
                               "freightimage[0][ne]" =>  163,
                               "freightimage[0][e]" =>   164,
                               "freightimage[0][se]" =>  165,
                               "freightimage[0][s]" =>   166,
                               "freightimage[0][sw]" =>  167,

                               "freightimage[1][w]" =>   170,
                               "freightimage[1][nw]" =>  171,
                               "freightimage[1][n]" =>   172,
                               "freightimage[1][ne]" =>  173,
                               "freightimage[1][e]" =>   174,
                               "freightimage[1][se]" =>  175,
                               "freightimage[1][s]" =>   176,
                               "freightimage[1][sw]" =>  177,

                               "freightimage[2][w]" =>   180,
                               "freightimage[2][nw]" =>  181,
                               "freightimage[2][n]" =>   182,
                               "freightimage[2][ne]" =>  183,
                               "freightimage[2][e]" =>   184,
                               "freightimage[2][se]" =>  185,
                               "freightimage[2][s]" =>   186,
                               "freightimage[2][sw]" =>  187,

                               "freightimage[3][w]" =>   190,
                               "freightimage[3][nw]" =>  191,
                               "freightimage[3][n]" =>   192,
                               "freightimage[3][ne]" =>  193,
                               "freightimage[3][e]" =>   194,
                               "freightimage[3][se]" =>  195,
                               "freightimage[3][s]" =>   196,
                               "freightimage[3][sw]" =>  197,

                               "freightimage[4][w]" =>   200,
                               "freightimage[4][nw]" =>  201,
                               "freightimage[4][n]" =>   202,
                               "freightimage[4][ne]" =>  203,
                               "freightimage[4][e]" =>   204,
                               "freightimage[4][se]" =>  205,
                               "freightimage[4][s]" =>   206,
                               "freightimage[4][sw]" =>  207,

                               "freightimage[5][w]" =>   210,
                               "freightimage[5][nw]" =>  211,
                               "freightimage[5][n]" =>   212,
                               "freightimage[5][ne]" =>  213,
                               "freightimage[5][e]" =>   214,
                               "freightimage[5][se]" =>  215,
                               "freightimage[5][s]" =>   216,
                               "freightimage[5][sw]" =>  217,

                               "freightimage[5][w]" =>   210,
                               "freightimage[5][nw]" =>  211,
                               "freightimage[5][n]" =>   212,
                               "freightimage[5][ne]" =>  213,
                               "freightimage[5][e]" =>   214,
                               "freightimage[5][se]" =>  215,
                               "freightimage[5][s]" =>   216,
                               "freightimage[5][sw]" =>  217,

                               "freightimage[6][w]" =>   220,
                               "freightimage[6][nw]" =>  221,
                               "freightimage[6][n]" =>   222,
                               "freightimage[6][ne]" =>  223,
                               "freightimage[6][e]" =>   224,
                               "freightimage[6][se]" =>  225,
                               "freightimage[6][s]" =>   226,
                               "freightimage[6][sw]" =>  227,

                               "freightimage[7][w]" =>   230,
                               "freightimage[7][nw]" =>  231,
                               "freightimage[7][n]" =>   232,
                               "freightimage[7][ne]" =>  233,
                               "freightimage[7][e]" =>   234,
                               "freightimage[7][se]" =>  235,
                               "freightimage[7][s]" =>   236,
                               "freightimage[7][sw]" =>  237,

                               "freightimage[8][w]" =>   240,
                               "freightimage[8][nw]" =>  241,
                               "freightimage[8][n]" =>   242,
                               "freightimage[8][ne]" =>  243,
                               "freightimage[8][e]" =>   244,
                               "freightimage[8][se]" =>  245,
                               "freightimage[8][s]" =>   246,
                               "freightimage[8][sw]" =>  247,

                               "image[0][0]" =>  120,
                               "image[1][0]" =>  121,
                               "image[2][0]" =>  122,
                               "image[3][0]" =>  123,
                               "image[4][0]" =>  124,
                               "image[5][0]" =>  125,
                               "image[6][0]" =>  126,
                               "image[7][0]" =>  127,
                               "image[8][0]" =>  128,
                               "image[9][0]" =>  129,
                               "image[10][0]" => 130,
                               "image[11][0]" => 131,
                               "image[12][0]" => 132,
                               "image[13][0]" => 133,
                               "image[14][0]" => 134,
                               "image[15][0]" => 135,
                               "image[16][0]" => 136,
                               "image[17][0]" => 137,
                               "image[18][0]" => 138,
                               "image[19][0]" => 139);




    if (($obj_type == "vehicle") OR ($obj_type == "citycar") OR ($obj_type == "pedestrian") OR ($obj_type == "tree"))
        //use order map for vehicles
        if (isset($order_map[$image_name])) return $order_map[$image_name];
        else                               return 5;
     else
        //no order defined for other types
        return 128;
}



////////////////////////////////////////////////////////////////////////////////
//tmp function for case insensitive file search
function case_insensitive_file_search ($source_image_path)
{

    //file does not exist (in case sensitive comparism)
    //read all files in the directory
    echo "<p class='tight'>Image '$source_image_path' not found (doing case insensitive search).</p>\n";

    //at first we need to canonize the path (to properly detect file dir)
    //$canonical_path = realpath ($source_image_path);
    //above des not work
    $canonical_path = $source_image_path;

    //now get the directory in which to search for file
    //The following array elements are returned: dirname, basename and extension.
    $path_info = pathinfo ($canonical_path);
    $image_dir_path = $path_info['dirname'];
    //we also need a plin image name (the one from dat can contain some dir prefix...
    $image_plain_name = $path_info['basename'];

    //now get all files there to an array
    // avalable from 5.0 scandir ($image_dir_path);
    echo "<p class='tight'>Canonical path: '$canonical_path'. Dir to open: '$image_dir_path'. Name to find: '$image_plain_name'.</p>\n";
    $file_list = array ();
    $dir = opendir ($image_dir_path);
    while (($fl = readdir ($dir)) != FALSE) $file_list[] = $fl;
    closedir ($dir);


    foreach ($file_list as $file_name)
    {
        //check all (case insensitive)
        if (strcasecmp ($file_name, $image_plain_name) == 0)
        {
            $path_to_be_opened = ($image_dir_path . '/' . $file_name);
            echo "<p class='tight'>Updated image path '$source_image_path' to '$path_to_be_opened'.</p>\n";
            //no further search
            return $path_to_be_opened;
            break;
        }
    }

    //if we got here, nothing was found
    echo "<p class='tight'>Case insensitive search failed.</p>\n";
    return FALSE;
}


//this function outputs one attribute
//in case, its value it does not exist, it will create a commentp refixed line
//REMOVES attribute entry from array
function print_reqired_attribute ($name, $att_array, $mandatory = TRUE)
{
    //default (for nonmandatory, print nothing)
    $res = ($mandatory)?("#$name=?\n"):("");
    if (array_key_exists($name, $att_array))
    {
        //if it also has value, print
        if ($att_array[$name] != "")
        {
            $res = "$name=$att_array[$name]\n";
        }
        //remove this key entry
        unset ($att_array[$name]);
    }

    return $res;
}

//for a very common combination some shortcut function
//dat file creation
function print_intro_outro_dates ($att_array)
{
    $res  = print_reqired_attribute ("intro_year", $att_array);
    $res .= print_reqired_attribute ("intro_month", $att_array, FALSE);
    $res .= print_reqired_attribute ("retire_year", $att_array);
    $res .= print_reqired_attribute ("retire_month", $att_array, FALSE);
    return $res;
}

//tmp function for sorting images in the dat file output
function cmp_images ($a, $b)
{
    //in future this ca be restricted to sorting accoding to last 5 elements
    //so that the order of lines in dat file will defintiely correspond to the order of images
    return strcmp($a[0], $b[0]);
}



////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
class simu_object
{
    //this is tmp var to detect the cases were multiple separator lines
    //are behind each other and thus object with no content what so ever
    //would be created
    var $modified;

    var $obj;
    var $name;
    var $type;
    //used when saving program_text type objects
    var $untrimmed_name;
    var $version_id;
    var $tile_size;
    var $copyright;
    var $mod_date;

    //array, one line = one note line (without #)
    var $comments;

    //array [attribute] = value
    var $attributes;

    //array [imag_name] = [path, unzoomabe (t/f)]
    var $images;

    //constructor
    //always must be given version (image set)
    function __construct ($obj_version, $ts)
    {
        $modified = FALSE;

        $this->obj        = "";
        $this->name       = "";
        $this->type       = "";
        $this->untrimmed_name = "";
        $this->mod_date   = 0;

        //we assume thathose are cosntant durign the life
        //(tile size is take from outside to save constant queries
        $this->object_id  = 0;
        $this->version_id = $obj_version;
        $this->tile_size  = $ts;
        $this->copyright  = "";
        $this->note       = "";

        //each line = one entry
        $this->comments   = array ();

        //array[atr name] = value
        $this->attributes = array ();

        //images ["image[][]"] = array(path, zoom flag);
        $this->images     = array ();
        
    }

    //loads object information from a database
    //retruns TRUE if query succeeded (returned exactly 1 object to load)
    function load_from_database ($obj_version, $name, $obj)
    {
        //get the main data
        $obj_Array = db_query2array ("SELECT `object_id`, `obj_name`, `type`, `version_version_id`, `obj`, `image_path`, `comments`, `obj_copyright`,`note`, UNIX_TIMESTAMP(mod_date) AS 'mod_time' FROM `objects` WHERE `version_version_id`='$obj_version' AND `object_id`='$name' AND `obj`='$obj';");


        if ($obj_Array == FALSE)
        {
            //no object was found
            echo "<!-- object.php : load_from_database ($obj_version, $name, $obj) could not find object specified. -->\n";
            return FALSE;
        }

        //save the basic data
        $modified = TRUE;
        $this->obj        = $obj_Array["obj"];
        $this->object_id  = $obj_Array["object_id"];
        $this->name       = $obj_Array["obj_name"];
        $this->type       = $obj_Array["type"];
        $this->version_id = $obj_version;
        $this->copyright  = $obj_Array["obj_copyright"];
        $this->mod_date   = $obj_Array["mod_time"];
        $this->comments   = $obj_Array["comments"];
        $this->note       = $obj_Array["note"];

        
        //query for attributes
        foreach (db_fetch_result_as_table ("SELECT `p_name`, `p_value` FROM `property` WHERE `having_obj_name`='".db_real_escape_string($this->name)."' AND `having_version_version_id`='$this->version_id' AND `having_obj_id`='$this->object_id';") as $property)
        {
            $this->attributes[$property["p_name"]] = $property["p_value"];
        }

        //if object has iamges in db, load their names
        foreach (db_fetch_result_as_table ("SELECT `image_name`, `unzoomable`, `filename` FROM `images` WHERE `object_version_version_id`='$this->version_id' AND `object_obj_id`='$this->object_id';") as $image)
        {
           $this->images[$image["image_name"]] = array ("n/a", $image["unzoomable"], $image["filename"]);
           $this->tile_size  = $image["tile_size"];

        }

        return TRUE;
    }

    //this updates iamge entry in the field with new path and position
    //used when placing images to image sheet, with intention to generate dat file later
    function update_image_info ($image_name, $path, $posX, $posY)
    {
        $this->images[$image_name][0]= $path . "." . $posY . "." . $posX;
    }

    /*
        This function generates a dat file for object (return value)
        It realies on the fact, that image entries are properly filled
    */
    function generate_dat_file ()
    {
        //output buffer
        $dat_file = "";

        //at first add notes (we need to prefix removed #
        //notes can be stored in an array, line by line, or retrieved from database as
        //one line with linebreaks (take care of both cases)
        $comments = str_replace ("\n", "\n#", $this->comments);
        //in case that there are some notes, first line is not commented, correct it
        if ($comments != "")
        {
            $comments = "#" . $comments . "\n";
        }
        $dat_file .= $comments;

        //add general attributes
        $dat_file .= "obj=$this->obj\n";
        $dat_file .= "name=$this->name\n";
        //prefix copyright line if no copyright is known
        $dat_file .= (($this->copyright == "")?"#":"") . "copyright=$this->copyright\n";

        //start outputting attributes (so that they are in specified order)
        //create a copy of array, as we will destroy it during the process
        $tmp_attributes = $this->attributes;
        if ($this->obj == "building")
        {
            //all building have this
            $dat_file .= print_reqired_attribute ("type", $tmp_attributes);

            //city buildings have some special ones
            if ((strnatcasecmp($this->attributes["type"], "com") == 0) or (strnatcasecmp($this->attributes["type"], "ind") == 0) or (strnatcasecmp($this->attributes["type"], "res") == 0))
            {
                $dat_file .= print_reqired_attribute ("level", $tmp_attributes);
                $dat_file .= print_intro_outro_dates ( $tmp_attributes);
            }
        }   //now for vehicles
        elseif ($this->obj == "vehicle")
        {
            $dat_file .= print_reqired_attribute ("waytype", $tmp_attributes);
            $dat_file .= print_reqired_attribute ("engine_type", $tmp_attributes, FALSE);
            $dat_file .= print_reqired_attribute ("freight", $tmp_attributes);
            $dat_file .= print_reqired_attribute ("payload", $tmp_attributes);
            $dat_file .= print_reqired_attribute ("speed", $tmp_attributes);
            $dat_file .= print_reqired_attribute ("power", $tmp_attributes, FALSE);
            $dat_file .= print_reqired_attribute ("gear", $tmp_attributes, FALSE);
            $dat_file .= print_reqired_attribute ("weight", $tmp_attributes);
            $dat_file .= print_reqired_attribute ("cost", $tmp_attributes);
            $dat_file .= print_reqired_attribute ("runningcost", $tmp_attributes);
            $dat_file .= print_intro_outro_dates ($tmp_attributes);
        }


        //add other reamaining attrbutes (those printed should have been removed)
        ksort ($tmp_attributes, SORT_STRING);
        foreach ($tmp_attributes as $att_name => $att_value)
        {
            $dat_file .=$att_name . "=" . $att_value . "\n";
        }

        //add images
        //at first ensure systematic order
        //ksort ($this->images, SORT_STRING);
        uasort ($this->images, "cmp_images");

        foreach ($this->images as $img_name => $img_value)
        {
            //check if we are writing output for image which was palced to the image sheet
            if ($img_value[0] != "")
            {
                //strip the .png suffix
                $img_path = str_replace (".png", "", $img_value[0]);
                $dat_file .=$img_name . (($img_value[1] == TRUE)?"=> ":"=") . $img_path . "\n";
            } else
            {
                //bad case, image entry which was not saved to image sheet
                echo "<h3>Error: object.php : generate_dat_file () : Object has image ($img_name), which was not saved to the image sheet!</h3>\n";;
            }
        }

        return $dat_file;
    }


    //this function tests, if the object is valid
    //contains the minimal required data
    //(to prevent insertion of "comment objects" - pieces of comments enclosed in --
    function is_valid ()
    {   global $tr_name_pattern,$sub_waytypes;
        if ($this->obj       == "") return false;
        if ($this->name      == "") return false;

        //we also require some  attributes
        //oops, for example tunnels haven no attributes, only images
        //if (count($this->attributes)==  0) return false;
        // test for old dummy objects
        if ($this->obj       == "dummy_info")
        { echo "<p class='red'><h2>Error dummy_info Object found. This is outdated way for detail text.</h2></p>\n";
          return false;
        }
        // test for reserved pattern for special or detail text
        foreach ($tr_name_pattern as $tkey => $tv)
        { if ($tv == '?') continue;
          $ts = explode('?', $tv);
          $ts0 = strlen($ts[0]);
          $ts1 = strlen($ts[1]);
          if (substr($this->name,0,$ts0) == $ts[0] and
              substr($this->name,0-$ts1) == $ts[1])
          { echo "<p class='red'><h2>Error reserved Object Name found. It is not allowed to use special text names as object name</h2></p>\n";
            return false;
          }
        }

        // collect data for the db field type 
        $this->type = "";
        if (         $this->obj == 'building') $this->type = $this->attributes ['type'];
        if (in_array($this->obj,$sub_waytypes)) $this->type = $this->attributes ['waytype'];

        //if we got here, everything is ok
        return true;
    }


    //this function takes array [att, val] and saves it
    //beware, the input must be exactly like this!
    //first field must be lowercase
    function save_attribute ($tokens)
    {
    	GLOBAL $object_text;
 
        //check for special entries
        if ($tokens[0] == "obj")
        {
            $this->obj = strtolower($tokens[1]);
            return;
        }
        if ($tokens[0] == "name")
        {
            //if obj type is program_text use nonstripped version of the name
            //this requires the parameter obj to be present before the name to work!
            if ( in_array($this -> obj, $object_text) )
            {
                //now trim the line ending - but nothing else
                $this-> name = rtrim ($this -> untrimmed_name, "\n\r");
            }else
            {
                //for othe objects save the stripped name
                $this-> name = $tokens[1];
            }
            return;
        }
        if ($tokens[0] == "copyright")
        {
            $this->copyright = $tokens[1];
            return;
        }

        if ($tokens[0] == "note")
        {
            $this->note = $tokens[1];
            return;
        }

        //now catch image (trickier) keywords:
        //Image, BackImage, FrontImage, icon, cursor, Diagonal, EmptyImage, FreightImage
        //BackStart, FrontStart, BackRamp, FrontRamp, imageup, backpillar
        //remeber - first argument was lowercapsed
        //note, we need exactly FALSE (identity)
        if ((preg_match("#^(back|front|empty|freight|open|front_open|closed|front_closed)*(image|imageup|icon|cursor|diagonal|backstart|frontstart|backramp|frontramp|frontpillar|backpillar)#i"  , $tokens[0]) == 1)
           AND (preg_match("#^freightimagetype#i"  , $tokens[0]) != 1)) 
        {
            //take care of unzoomability
            //also need to get rid of extra space after >
            $unzoom = FALSE;
            if (strlen($tokens[1]) > 1 and $tokens[1][0] == '>')
            {
                $unzoom = TRUE;
                $tokens[1] = ltrim(substr ($tokens[1], 1));
            }
            //save the image line
            $this->images [$tokens[0]] = array($tokens[1], $unzoom);
            return;
        }

        //if we got here, then it is normal attribute
        $this->attributes [$tokens[0]] = $tokens[1];
    }


    //this function receives line and decides what to do
    //the line must containa at least 1 char
    //it must be trimed from the LEFT and NOT trimmed from the RIGHT
    //and it must not be --- line
    function parse_line ($line)
    {
        //we were modified
        $this->modified = TRUE;
        
        $line = str_replace("\t", ' ',$line);
        
        //decide what to do
        //comment, store in comment array
        if ($line[0] == '#')
        {
            //trim the comment #
            $line = trim(substr ($line, 1));
            if (substr_count($line, "-") == strlen ($line) )
            {
                //do not save this line
                return;
            } 
 //         echo "k+".$line."+k<br>";

            //add to the end of comments array
            $this->comments[] = $line;

            //finish
            return;
        }

        //makeobj discards anything after first space
        //do so as well--> nein dateinamen enthalten auch blank
        // aber es werden Kommentare hinter den Parametern geschrieben, aber in program_text kommt das auch vor
        $wl = explode(' #',$line,2);
        if (count ($wl) > 1 and $this->obj != 'program_text')
        { $line = $wl[0];
          $this->comments[] = trim($wl[1]);
 //       echo 'kommentar nach p'.$wl[1].'<br>';
        }

        //now we are sure that this is normal attribute line
        //process!
 //     echo "z+".$line."+z<br>";
        //split by = from left (only for first =, for wierd names with =)
        //we want 2 items! (attr = value)
        $tokens = explode ('=', $line , 2);

        //now trim the attribute name, as space is not interesting
        //also lowercase the 1st pos (case insensitive)
        $tokens[0] = strtolower (trim($tokens[0]));

        //now for the second part
        //if it does not exist, create empty value ""
        if (count ($tokens) == 1)
        {
            $tokens[1] = "";
        }else
        {
            //in normal case we want to trim the line
            //but if it is an object name, we must also preserve untrimmed version
            //as for program_text type objects are the additional spaces important
            //only trim the newline at the end
            if ($tokens[0] == "name")
            {
                //should be complete part of the line from = till line ending (included)
                $this -> untrimmed_name = $tokens[1];
            }
            $tokens[1] = trim ($tokens[1]);
        }

        //now we have perfect array to process further
        $this -> save_attribute ($tokens);
    }

    ////////////////////////////////////////////////////////////////////////////
    //prints debug objet info in XHTML format
    //assuming translators CSS
    function debug_html ()
    {

        //for valid object print title, for others, comment
        if ($this->is_valid ())
        {
            echo "<h3>Object name=". htmlentities($this->name, ENT_NOQUOTES) ." (type=" . $this->obj . ")</h3>\n";
        }else
        {
            echo "<h3>Comment (invalid object)</h3>\n";
        }

        echo "<table border='1px' cellspacing='1px' cellpadding='5px' width='100%'><tr><td>\n";
        //echo "<h3>Object name=". $this->name ." (type=" . $this->obj . ")</h3>\n";

        echo "<table border='0' cellspacing='5' cellpadding='0' width='100%'><tr>";
        echo "<td width='50%' valign='top'>\n";
        echo "<p class='tight'><b>Properties:</b></p>";
        echo "<ul>\n";

        if ($this->copyright != "")
        {
            echo "<li>copyright=<i>". htmlentities($this->copyright, ENT_NOQUOTES) ."</i></li>\n";
        }

        foreach ($this->attributes as $attr => $value)
        {
            echo "<li>$attr=<i>$value</i></li>\n";
        }
        echo "</ul>\n";
        echo "</td>\n";

        echo "<td  width='50%' valign='top'>\n";
        echo "<p class='tight'><b>Images:</b></p>";
        echo "<ul>\n";
        foreach ($this->images as $attr => $value)
        {
            echo "<li>$attr=<i>". $value[0] ."</i> ";
            if ($value[1] == TRUE) echo "(uz)";
            echo "</li>\n";
        }
        echo "</ul>\n";
        echo "</td>\n";

        //image here


        echo "</tr></table>\n";


        if (count($this->comments))
        {
            echo "<p class='tight'><b>Comments:</b></p>";
            echo "<p class='tight'>\n";
            foreach ($this->comments as $value)
            {
                echo "$value<br />\n";
            }
            echo "</p>\n";
        }

        echo "</td></tr></table>\n";
    }

    ////////////////////////////////////////////////////////////////////////////
    //save image to the db (pass parameter as reference to increase speed)//////
    function save_1image_to_db (&$im_old_id,$img_name, $zoomable, $image, $tilesize,$pos_x,$pos_y,$offset_x,$offset_y)
    {   GLOBAL $st_dbi,$im_unmodified, $im_updated, $im_deleted, $im_inserted;
        // echo "save_1image_to_db  ".$img_name."<br>";
        $tab_set = 'images_'.$this->version_id;
        /*
          now silly thing, php does not allow allow savig resources
          so to get image to the db without creating tmp file w need to
          start buffering output (bool ob_start ( [callback output_callback [, int chunk_size [, bool erase]]] ) )
          send there the image using save2png (imagepng)  (goes to std out by def)
          size check (int ob_get_length ( void ))
          now we get the string using  (string ob_get_clean ( void )
          also stops buffering (false on faliure)
        */
        if ($image != NULL)
        { ob_start();                   //start buffering
          imagepng ($image);            //will output it to the buffer        
          $img_size = ob_get_length (); //determine size
	if ($img_size == 0)
          { ob_end_clean ();  //disble buffering and go to next
            echo "<h3>Tried to save empty image tile (0B in size): ".basename($source_image_path)." . $pos_y . $pos_x to the database (internal image processing error).</h3>";
            return False;
          }
          if (ob_get_length () >= 65535)
          { ob_end_clean ();//disble buffering and go to next
            echo "<h3>Tried to save image tile: ".basename($source_image_path)." . $pos_y . $pos_x , but its size ($img_size) exceeds 65535B and will not fit into the database.</h3>";
            return False;
          }
          $png_tile = ob_get_clean (); //flush buffer to string variable
        }
        else $png_tile = "";
        
        //small patch to print 0 (wouldn ot be pritned otherwise
        $z = $zoomable ? 1 : 0;

        $image_order = determine_image_order ($img_name, $this->obj);
        $filename = "";
 
        // Grafik in Datenbank speichern
        if (array_key_exists($img_name, $im_old_id))
        { $im_s="SELECT * FROM $tab_set WHERE image_id=".$im_old_id[$img_name];
          $im_t = db_query($im_s);
          $im_a = mysqli_fetch_object($im_t);
          if ( $im_a->object_obj_id             == $this->object_id  and
               $im_a->object_obj_name           == $this->name       and
               $im_a->object_version_version_id == $this->version_id and
               $im_a->object_obj_type           == $this->obj        and
               $im_a->image_name                == $img_name         and
               $im_a->unzoomable                == $z                and
               $im_a->image_order               == $image_order      and
               $im_a->image_data                == $png_tile         and
               $im_a->tile_size                 == $tilesize         and
               $im_a->filename                  == $filename         and
               $im_a->offset_x                  == $offset_x         and
               $im_a->offset_y                  == $offset_y    )
          { $im_unmodified++;  
          }
          else
          { $im_u = mysqli_prepare($st_dbi,"UPDATE $tab_set SET ".
               " object_obj_id=?,".
               " object_obj_name=?,".
               " object_version_version_id=?,". 
               " object_obj_type=?,".
               " image_name=?,".
               " unzoomable=?,".
               " image_order=?,". 
               " image_data=?,".
               " tile_size=?,".
               " filename=?,". 
               " offset_x=?,". 
               " offset_y=? ". 
               "WHERE image_id=?");
            if ($im_u === false) die("db_error im upd".mysqli_error($st_dbi));
            mysqli_stmt_bind_param ($im_u,'isississisiii',
               $this->object_id ,
               $this->name,
               $this->version_id,
               $this->obj,
               $img_name ,
               $z,
               $image_order,
               $png_tile,
               $tilesize,
               $filename,
               $offset_x,
               $offset_y,
               $im_old_id[$img_name]);
            mysqli_stmt_execute ($im_u) or die("SQL error (cannot insert image): ".mysqli_stmt_error($im_u));
            $im_c = mysqli_stmt_affected_rows($im_u);
            if ($im_c != 1) echo "<h3>Error when update imageies:".$this->name."_".$this->version_id."#".$img_name."</h3>\n";
            $im_updated += $im_c;
            mysqli_stmt_close($im_u);
          }
          $im_old_id[$img_name] = 0;
        }  
        else
        { $im_i = mysqli_prepare($st_dbi,"INSERT INTO ".$tab_set.
              " (object_obj_id, object_obj_name, object_version_version_id,". 
               " object_obj_type, image_name, unzoomable, image_order,". 
               " image_data, tile_size, filename, offset_x, offset_y) ".
               "VALUES (?,?,?,?,?,?,?,?,?,?,?,?)"); 
          if ($im_i === false) die("db_error im ins".mysqli_error($st_dbi));
          mysqli_stmt_bind_param ($im_i,'isississisii',
               $this->object_id,
               $this->name,
               $this->version_id,
               $this->obj,
               $img_name ,
               $z,
               $image_order,
               $png_tile,
               $tilesize,
               $filename,
               $offset_x,
               $offset_y);
          mysqli_stmt_execute ($im_i) or die("SQL error (cannot insert image): ".mysqli_stmt_error($im_i));
          $im_c = mysqli_stmt_affected_rows($im_i);
          if ($im_c != 1) echo "<h3>Error when insert imageies:".$this->name."_".$this->version_id."#".$img_name."</h3>\n";
          $im_inserted += $im_c;
          mysqli_stmt_close($im_i);
        }        
    }


    ////////////////////////////////////////////////////////////////////////////
    function save_images_to_db ($dat_path)
    {   global $im_unmodified, $im_updated, $im_deleted, $im_inserted;

        $tab_set = 'images_'.$this->version_id;
        $im_old_id = array();
        $im_s="SELECT image_id, image_name FROM $tab_set WHERE object_obj_name='".db_real_escape_string($this->name) .
             "' AND object_version_version_id=".$this->version_id;
        $im_t = db_query($im_s);
        while ($im_a = db_fetch_object($im_t))
        { if (array_key_exists($im_a->image_name, $im_old_id)) 
          { $im_old_id['del_dup'.$im_a->image_id] = $im_a->image_id;
            echo "<h3>double entry in database! $this->name = $im_a->image_name : try delete </h3>\n";
          }
          else $im_old_id[$im_a->image_name] = $im_a->image_id;
        }
        mysqli_free_result($im_t);
       
        //performance hint
        //keep opened image resource and reuse if path is the same
        //using globals, share opened image between objects
        global $open_image, $open_path;
        //$open_image = 0;
        //$open_path  = "";

        //for each image, extract path, look for source and rip the square from it
        foreach ($this->images as $img_name => $value)
        {
            $img_target = $value[0];
            $zoomable   = $value[1];
          
            if ( $img_target != "-" ) {

            // check to offsets
            $r = explode(",", $img_target);
            $r_c = count($r);
            $offset_x = 0;
            $offset_y = 0;
            if ($r_c > 2 and preg_match("#^[-+]?[0-9]+\$#", $r[$r_c-1]) == 1) $offset_y = intval($r[--$r_c]);
            if ($r_c > 1 and preg_match("#^[-+]?[0-9]+\$#", $r[$r_c-1]) == 1) $offset_x = intval($r[--$r_c]);
            $img_target = implode (',', array_slice ($r, 0 , $r_c));

            $pos_x = 0;
            $pos_y = 0;
            $path = $img_target;
            $r = explode ('.', $img_target); 
            $r_c = count($r);

            //check if path name.y.x paths
            if (preg_match("#\.[0-9]+\.[0-9]+\$#", $img_target) == 1)
            {           //now we are sure that path ends with .number.number
 
               //rember name.y.x
              $pos_x = intval($r[$r_c - 1]);
              $pos_y = intval($r[$r_c - 2]);
              $path = implode ('.', array_slice ($r, 0 , $r_c - 2));

             }  //check if path name.x paths
             elseif (preg_match("#\.[0-9]+\$#", $img_target) == 1)
             { $r = explode ('.', $img_target);           //now we are sure that path ends with .number
               $r_c = count($r);
               //rember name.y.x
               $pos_x = intval($r[$r_c - 1]);
               $pos_y = 0;
               $path = implode ('.', array_slice ($r, 0 , $r_c - 1)); //now reassemble the image path
             }
             
             //and of course, suffix the .png
             $path = $path . ".png";
             $dat_p = $dat_path;
             if (substr($path,0,3) == "../")
             { $path = substr($path,3);
               $dat_p = substr($dat_p,0,strrpos($dat_p, "/"));
             }  
             if (substr($path,0,3) == "../")
             { $path = substr($path,3);
               $dat_p = substr($dat_p,0,strrpos($dat_p, "/"));
             }  
             if (substr($path,0,2) == "./") $path = substr($path,2);
             if (substr($path,0,1) == "/")  $path = substr($path,1);
               
            //debug
            //echo "   x$pos_x, y$pos_y, $path";
            //now we have the correct path, so get the file (and pray for correct case)
            //note: val should not be lowercaswed, so if dat is correct....

            //image path is derived from dat path (as in make obj)
            //this is path retrieved from dat file (often contains someting like /../ etc
            $source_image_path = $dat_p . '/' .  $path;
            
            if (strpos($source_image_path, " ") > 1) echo "blank in Dateiname:".$source_image_path."<br>";

            //performance tweak
            //open new image only if necessary
            //ie the path of previously opened (and not closed) image
            //differ from new required path
            if ($source_image_path != $open_path)
            {
                //debug
                //echo "Openinig image $source_image_path"

                //at first close the old (if any)
                if ($open_path != "")
                {
                    imagedestroy ($open_image);
                }

                //we need to check the path also in the case insensitive manner
                //there are some modifications made to it (ie it is cannonized)
                //then it is not possible to check it so for some performance tweaks
                //we need to keep both original path and modified path
                $path_to_be_opened = $source_image_path;

                //path check
                //we are running on linux server - case sensitive
                //files might be created on windows - case insensitive
                //so we need to check if the file exists
                //if not, try to find it in case insensitive manner
                if (!file_exists ($source_image_path))
                {
                    //do case insensitive search
                    $path_to_be_opened = case_insensitive_file_search ($source_image_path);

                    //if we failed to find a new path, image does not exist
                    //so do not try to load it, go to next one
                    if ($path_to_be_opened == FALSE)
                    {
                        echo "<h3>Cannot find image: ".basename($source_image_path)." , skipping!</h3>\n";
                        $open_path  = "";
                        continue;
                    }
                }

				$size = getimagesize($source_image_path);
				$pngsize = $size[0] * $size[1];
		 /*		if ( $pngsize > 1622017 ) {
                        echo "png file $source_image_path to big, skipping!<br />\n";
                     $open_path  = "";
                        continue;  
				} */

                //open the image
                $open_image = imagecreatefrompng ($path_to_be_opened);
  

                //for performance tweak we need to remember what image we have opened
                //but use the original path as memo (since it will make checking easier)
                $open_path  = $source_image_path;

                //error check
                //returns "" on faliure
                if ($open_image == "")
                {
                    echo "<h3>Error when loading the image: $source_image_path , skipping!</h3>\n";

                    //skip this image and try next time
                    $open_path  = "";
                    continue;
                }

                ImageAlphaBlending($open_image, false); 
                imagesavealpha($open_image, true);

            }

            //now get the required block
            $tile = imagecreatetruecolor ($this->tile_size, $this->tile_size);

            $background = imagecolorallocatealpha($tile, 220, 220, 220, 127);
            imagefill($tile, 0, 0, $background);

            ImageAlphaBlending($tile, false); 
            imagesavealpha($tile, true);

            //copy the block to the tile
            imagecopy ($tile, $open_image, 0, 0, ($this->tile_size)*$pos_x, ($this->tile_size)*$pos_y, $this->tile_size, $this->tile_size);

            //do the saving procedure (tile passed as reference!)
            $this -> save_1image_to_db ($im_old_id,$img_name,$zoomable, $tile, $this->tile_size,$pos_x,$pos_y,$offset_x,$offset_y);

            //cleanup
            imagedestroy ($tile);
	  } else {
            $this -> save_1image_to_db ($im_old_id,$img_name,$zoomable, NULL,  $this->tile_size,0,0,0,0);
	  }
        }

        //close the opened image
        //do not close it, next object will use it
        //so far last image remains open... should be destroyed by php right?
        /*little price to pay for SIGNIFICANT performance increase
          (at least for pak and its 1152*1280 imges which are common for tens of objects and take long to load.*/
        //imagedestroy ($open_image);

        foreach ($im_old_id as $i_id)
        { if ($i_id > 0)
          { $im_d = "DELETE FROM $tab_set WHERE image_id=".$i_id;
            db_query($im_d);
	  $im_deleted += db_affected_rows();
	}  
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    //saves all properties to database
    //asumes that there are none there (were deleted)
    //returns number of propertiesi nserted
    function save_properties_to_db ()
    {  global $st_dbi, $pr_unmodified, $pr_updated, $pr_deleted, $pr_inserted;
       $copy_at =  $this->attributes; // make no change on original
       
       $pr_s="SELECT * FROM property WHERE having_version_version_id=".$this->version_id." AND having_obj_id=".$this->object_id;
       $pr_t = db_query($pr_s);
       while ($pr_a = mysqli_fetch_object($pr_t))
         { $upd = false;
           if ($pr_a->having_obj_name !=$this->name) 
             { echo "false obj_name in properties :".$pr_a->having_obj_name."+ new:".$this->name."+ updatet<br>";
               $pr_a->having_obj_name = $this->name;
               $upd = true;
             }
           if (array_key_exists($pr_a->p_name, $copy_at))
             { if ($pr_a->p_value != $copy_at[$pr_a->p_name] or $upd)
                {  $pr_a->p_value  = $copy_at[$pr_a->p_name];
                   $pr_u = mysqli_prepare($st_dbi, "UPDATE property SET having_obj_name=?, p_value=? WHERE property_id=?");
                   if ($pr_u === false) die("db_error upd ".mysqli_error($st_dbi));
                   mysqli_stmt_bind_param   ($pr_u,'ssi',$pr_a->having_obj_name,$pr_a->p_value,$pr_a->property_id);  
                   mysqli_stmt_execute      ($pr_u) or die ("SQL UPDATE error: " . mysqli_error($st_dbi));
                   $pr_c = mysqli_stmt_affected_rows($pr_u);
                   mysqli_stmt_close        ($pr_u);
                   if ($pr_c != 1) echo "<h3>Error when UPDATE properties:".$pr_a->p_name."=".$pr_a->p_value."</h3>\n";
                   $pr_updated++;
                } else $pr_unmodified++;
               unset($copy_at[$pr_a->p_name]); // delete propertie / mark as done / no insert
             }
            else
             { db_query("DELETE FROM property WHERE property_id=".$pr_a->property_id);
               $pr_c = db_affected_rows();
               if ($pr_c != 1) echo "<h3>Error when DELETE properties:".$pr_a->p_name."=".$pr_a->p_value."</h3>\n";
               $pr_deleted++; 
             }
         }   
       mysqli_free_result($pr_t);
       
       $p_n = ""; $p_v = "";
       $pr_i = mysqli_prepare($st_dbi, "INSERT INTO property (having_obj_id, having_obj_name, having_version_version_id, p_name, p_value)".
                                                    " VALUES (?,             ?,               ?,                         ?,      ?)");
       if ($pr_i === false) die("db_error p ins".mysqli_error($st_dbi));
       mysqli_stmt_bind_param ($pr_i,'isiss',               $this->object_id,$this->name,     $this->version_id,         $p_n,   $p_v);

       foreach ($copy_at as $attr => $value)
         { if (strlen ($attr) > 50 or strlen($value) > 100)
               {  echo "<br>to long:".$attr."=".$value.":end<br>";
                  $attr  = substr($attr ,0,50);
                  $value = substr($value,0,100);
               }
           $p_n = $attr;
           $p_v = $value;
           mysqli_stmt_execute ($pr_i) or die("SQL error (cannot insert properties): ".mysqli_stmt_error($pr_i));
           $p_c = mysqli_stmt_affected_rows($pr_i);
           if ($p_c != 1) echo "<h3>Error when saving properties:".$p_n."=".$p_v."</h3>\n";
           $pr_inserted += $p_c;
          }
       mysqli_stmt_close($pr_i);
    }


    ////////////////////////////////////////////////////////////////////////////
    //this function is meant to be called from outside
    //by default this function is deleting and recreating images
    function save_object_to_db ($dat_path,$file_name, $store_n_delete_images, $verbose)
    {   GLOBAL $st_dbi,$object_nodisp,$sum_prob,$sum_img,$sum_tran,$sum_alles;
        GLOBAL $pr_unmodified, $pr_updated, $pr_deleted, $pr_inserted;
        GLOBAL $tr_unmodified, $tr_updated, $tr_deleted, $tr_inserted;
        GLOBAL $im_unmodified, $im_updated, $im_deleted, $im_inserted;
        GLOBAL $ob_unmodified, $ob_updated, $ob_inserted;
        $pr_unmodified=0; $pr_updated=0; $pr_deleted=0; $pr_inserted= 0;
        $tr_unmodified=0; $tr_updated=0; $tr_deleted=0; $tr_inserted= 0;
        $im_unmodified=0; $im_updated=0; $im_deleted=0; $im_inserted= 0;
        $ob_unm = 0;    
        $ob_upd = 0;
        $ob_ins = 0;

        if ( in_array($this->obj, $object_nodisp)) return(false);

        db_query("START TRANSACTION");
        $timestamp_start = microtime(true);

        //save object
 
        //finally join the lines (as were read) to one string which will go to DB
        $comments =  implode ("\n", $this->comments);

        //check length (strip if needed)
        if (strlen ($comments) > 16000)
        {   echo "<br>to Long max 16000:".$comments."<br>";
            $comments = substr ($comments, 0, 16000);
        }

        //set the path to image to db if any will be loaded or to none if no image exists
        //usefull to detect later if image is missing or dummy shall be displayed
        $image_path = (count($this->images) == 0)?"none":"db";


        //finally insert the object
     

        $sqlobj = sprintf ("SELECT * FROM objects WHERE obj_name='%s' AND version_version_id=%d ;",
                           db_real_escape_string($this->name) , $this->version_id);
        $result = db_query($sqlobj);
        $objalt = mysqli_fetch_object($result);
        if ($objalt === null) 
        { $sql_insert_obj = "INSERT INTO objects (obj_name, version_version_id, obj, type, image_path, comments, obj_copyright, mod_date, note)".
                           " VALUES ('" . db_real_escape_string($this->name)  . "', $this->version_id, '" . $this->obj . "','" . 
                                          db_real_escape_string($this->type) . "','". 
                                          db_real_escape_string($image_path) . "','". 
                                          db_real_escape_string($comments)."', '" .
                                          db_real_escape_string($this->copyright) . "',  NULL, '" .
                                          db_real_escape_string($this->note ). "')";
          db_query($sql_insert_obj);
          $this->object_id = mysqli_insert_id($st_dbi);
          if ($this->object_id < 1) die ("<b>SQL error</b> (cannot insert object row): ".mysqli_error($st_dbi)." SQL: ".$sql_insert_obj);
          $ob_ins++;
        } 
        else
        { if ($objalt->obj_name           != $this->name        or
              $objalt->version_version_id != $this->version_id) die ("Database ERROR Objectname is different<br>alt=".
                                                                      $objalt->obj_name."neu=".$this->name.
                                                                      "vers=$objalt->version_version_id == $this->version_id");
          $this->object_id = $objalt->object_id;
          if ($file_name == '_objectlist.dat') // if restore a sav.tab do not erase .dat
          { $this->type      = $objalt->type;
            $image_path      = $objalt->image_path;
            $comments        = $objalt->comments;
            $this->copyright = $objalt->obj_copyright;
          }
          if ($objalt->obj                == $this->obj        and
              $objalt->type               == $this->type       and
              $objalt->image_path         == $image_path       and
              $objalt->comments           == $comments         and
              $objalt->obj_copyright      == $this->copyright  and
              $objalt->note               == $this->note)  { $ob_unm++; }       
          else
          {  $upd_st = mysqli_prepare($st_dbi,"UPDATE objects SET  obj=?, type=?, image_path=?, comments=?, obj_copyright=?, note=? WHERE object_id=?");
             if ($upd_st === false) die("obj update db_error".mysqli_error($st_dbi));
             mysqli_stmt_bind_param   ($upd_st,'ssssssi',$this->obj, 
                                                         $this->type,
                                                         $image_path, 
                                                         $comments,
                                                         $this->copyright, 
                                                         $this->note,
                                                         $this->object_id);
             if (!mysqli_stmt_execute       ($upd_st)) die ("obj update db exe error".mysqli_stmt_error($upd_st));
             $ar= mysqli_stmt_affected_rows ($upd_st);
                  mysqli_stmt_close         ($upd_st);
             if ( $ar != 1)
             { echo "<h3>Warninig: Problem when saving object: " . $this->name . ", object was not saved properly - " . $ar . " affected rows.</h3>";
               //do not continue saving this object (as we would break integrity by inserting properties to nonexisting obj
               return false;
             }
             $ob_upd++;
          }  
          if ( mysqli_fetch_object($result) !== null) { echo "<h3>Error: Object is Duplicate in the database! </h3>\n"; return FALSE; }
        }
        mysqli_free_result($result);

        //save properties
        $timestamp_prob_save = microtime(true);
        if ($file_name != '_objectlist.dat') $this -> save_properties_to_db ();
        $sum_prob += microtime(true) - $timestamp_prob_save;
 
        //save images
        $timestamp_img_save = microtime(true);
        if ($file_name != '_objectlist.dat' and $store_n_delete_images) $this -> save_images_to_db ($dat_path);
        $sum_img += microtime(true) - $timestamp_img_save;

        //do special actions if object has any
        $timestamp_translate = microtime(true);
        // Update table 'translations'  
        // Update Object Ids
        tr_update_obj_id($this->name,$this->version_id,$this->object_id);
        $sum_tran += microtime(true) - $timestamp_translate;

        db_query("COMMIT");

        $sum_alles += microtime(true) - $timestamp_start;
        if (microtime(true) - $timestamp_start > 2) echo "<h3>LANGE LAUFZEIT, MEHR ALS 2 SEKUNDEN".$this->name."</h3>\n";

        if ($pr_updated+$pr_deleted+$pr_inserted+$ob_upd+
            $tr_updated+$tr_deleted+$tr_inserted+$ob_ins+
            $im_updated+$im_deleted+$im_inserted == 0) $ob_unmodified +=$ob_unm;
        else    
        { if ($ob_ins) { $ob_inserted++; $logtyp = "object import"; }
             else      { $ob_updated++;  $logtyp = "object update"; }
 
          // logfile Timestamp
          $t = date("Y-m-d H:i:s", time());
          $log_o_name = str_replace("|", '!',$this->name);
          // Date and Time | User | Object type | Object name | Language | Message | Object Id
          $data = $t."|".$_SESSION['userId']."|$this->obj|$log_o_name||$logtyp|$this->object_id\n";
          write_log($this->version_id, $data);
          
          if ($verbose) $this -> debug_html ();
	printf("Laufzeit= %01.2f Summe= %01.2f <br>",(microtime(true) - $timestamp_start),$sum_alles);
          //message (escape any special html chars in the object name)
          $img_msg = $store_n_delete_images?", unm=$pr_unmodified, upd=$im_updated, del=$im_deleted, ins=$im_inserted images":", images were not touched";
                           $at = "Modified object:";
          if ($ob_upd > 0) $at = "Updated object:";
          if ($ob_ins > 0) $at = "Inserted object:";

          echo "<p class='tight'>$at <b>" . htmlentities($this->name, ENT_NOQUOTES) . "</b> <i>(set: " . $this->version_id . ", type: " . $this->obj . ", tile-size:".$this->tile_size.")</i>, ".
          "unm=$pr_unmodified, upd=$pr_updated, del=$pr_deleted, ins=$pr_inserted properties, ".
          "unm=$tr_unmodified, upd=$tr_updated, del=$tr_deleted, ins=$tr_inserted translations" . $img_msg . ".</p>\n";
        }
        return true;
    }


    //only returns a number of entries in image array
    function image_count ()
    {
        return count($this->images);
    }

    //retruns a value of name specified attribute
    function get_attribute_value ($att_name)
    {
        if (array_key_exists($att_name, $this->attributes))
        {
            return $this->attributes[$att_name];
        }else
        {
            return FALSE;
        }
    }

}


?>







