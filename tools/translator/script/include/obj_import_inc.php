<?php
/*
    Include file for SimuTranslator - object importer
    (fragments of code based on original from P. Spilka)

    Tomas Kubes
    2005
*/
include ('./include/obj.php');
include ("./include/object.php"); 
include ("include/translations.php");

////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function path_html_process($p,$tmp_dir)
{ return htmlentities (str_replace($tmp_dir,'',$p), ENT_QUOTES, "UTF-8");
}

////////////////////////////////////////////////////////////////////////////////
//this function creat a list of all dat files in the tree startin at dir
//(inherited from original ST)
function browsedir1 ($dir,&$filecount,&$files,$rexp,$tile_size,$tmp_dir)
{
  if ($dp = @opendir($dir)) {
    $dn=0;
    $ds=array();
    $st_ignore = false;
    if (strpos($rexp,".dat"))
    {
      if (is_file($dir."/statsignore.conf")) 
      { $st_ignore = true;
        echo_table_line(path_html_process($dir,$tmp_dir),"statsignore.conf is found -> content of directory is ignored","");
        
      }  
      if (is_file($dir."/_pakmak.tab"))
      { $datfile_lines = file($dir."/_pakmak.tab");
        $nts = 0;
        foreach ($datfile_lines as $line)
        { $l_exp = explode(' ',$line);
          if (strtolower($l_exp[0]) == "size")
          { $nts = intval(array_filter($l_exp, 'strlen')[1]);
            // echo "_paktab gefunden $l_exp[0] $l_exp[1] = $nts <br>\n";
            break;
          }  
        }  
        if ($nts > 30 and $nts < 1000) 
        { echo_table_line(path_html_process($dir,$tmp_dir),"_pakmak.tab is found -> all objekts imported with tile-size", $nts);
          $tile_size = $nts;
        }
      }
    }
    while (($file = readdir($dp)) !== false) {
      $pfile=$dir."/".$file;
      if (($file !== ".") && ($file !== "..") && is_dir($pfile)) {
        $dn++; $ds[$dn]=$pfile;
      }
      if (!$st_ignore and (preg_match($rexp,$file) == 1) and is_file($pfile)) {
        $filecount++;
        $fileentry = array();
        $fileentry['filename']  = $pfile;
        $fileentry['tile_size'] = $tile_size;
        $files[$filecount] = $fileentry;
      }
    }
    closedir($dp);
    for ($i=1;$i<=$dn;$i++) {
      browsedir1($ds[$i],$filecount,$files,$rexp,$tile_size,$tmp_dir);
    }
  }
}

function browsedir ($dir,&$filecount,&$files,$rexp,$tile_size=0)
{ echo '<p style="text-align:left">';
  echo_table_start("","Directory","Action","tile-size");
  $filecount=0;
  $files=array();
  browsedir1 ($dir,$filecount,$files,$rexp,$tile_size,$dir);
  echo_table_end();
  echo '</p>';
}



////////////////////////////////////////////////////////////////////////////////
//this populates global variables (must be called at first
function get_version_tile_size($local_version_id)
{
    $version_q = sprintf ("SELECT tile_size FROM versions WHERE version_id=%d;",$local_version_id);
    $version_a = db_query2array ($version_q);
    return $version_a[0];
}


////////////////////////////////////////////////////////////////////////////////
//this checks if user has rights to import objects to this set
function user_access_check($version_id)
{  global $maintainter;
    if ( in_array($version_id,$maintainter) )return true;
    else
    {  echo("<h2>Error: $user you are not set version maintainer!</h2>\n".
            "<p>Only set version maintainer ".implode(' ',$maintainer_id).
            " is allowed to import objects in batches.</p>\n");
       return false;
    }
}


////////////////////////////////////////////////////////////////////////////////
//this function takes care of one dat file, processing it
//storing the information and other taks....
//for perfromance tweaks, requires also target version id and the tile size (to save queries)
function process_dat_file ($dat_file_name, $target_version_id, $t_size,$tmp_dir)
{   GLOBAL $LNG_OBJ_IMPORT;

    echo "<hr>\n".$LNG_OBJ_IMPORT[14].": <b>".path_html_process($dat_file_name,$tmp_dir)."</b>.";

    //count of object with successful save
    $obj_count = 0;
    //count of all found valid objects
    $found_count = 0;

    //detect if we need to stored images (this funct. will be included to script with post data)
    // dat = only dat stored
    $store_images = true; // "full" = true = default
    if (isset($_POST["upload_type"]) and $_POST["upload_type"]=="dat") $store_images = FALSE;
 
    $vb = true;  // "verbose" = true = default 
    if (isset($_POST["mode"]) and $_POST["mode"] == "quiet") $vb = false;

    //extracts a directory from the path
    //(used later for iamge referencing)
    $path_inf   = pathinfo($dat_file_name);
    $dat_dir    = $path_inf["dirname"];
    $file_name  = $path_inf['basename'];

    //reads a content of datfiles to an array of lines
    //dat files are usually rather small (at most few tens of KB)
    //array file ( string filename [, int use_include_path [, resource context]] )
    ini_set("auto_detect_line_endings", true);
    $datfile_lines = file($dat_file_name);
    
    // check if short cut 
    $short_cut = false;
    foreach ($datfile_lines as $line)
    { if ($line[0] == '#') continue;
      $tokens = explode ('=', strtolower (trim($line)) , 2);
      if (count ($tokens) == 1) continue;
      if ($tokens[0] == "name") continue;
      if (preg_match('#\[+[0-9]\-+[0-9]\]#', $tokens[0]) == 1) $short_cut = true;
//    if (preg_match('#\<\$*[0-9]\>#',       $tokens[1]) == 1) $short_cut = true;
      if (preg_match('#\<\$#',               $tokens[1]) == 1) $short_cut = true;
    }
    if ($short_cut)
    { echo "<br>Short Cut found<br>\n";
      $e_l =array();
      if (!is_file($dat_file_name)) die ('pgm error');
      $ret_code = 'exec fail';
      $dat_file_exp = $dat_file_name.'.exp';
      exec('/M11/WWW/MAKIE.178/translator/script/include/makeobj_fuer_include expand '.$dat_file_exp.' '.$dat_file_name,$e_l,$ret_code);
      if ($ret_code != 0)
      { echo "Return Code=".$ret_code."<br>\n";
        foreach ( $e_l as $error_line) 
        if (       $error_line      != ''        and
            substr($error_line,0,7) != 'writing' and
            substr($error_line,3,7) != 'reading' and
            substr($error_line,0,3) != '(c)'     and
            substr($error_line,0,7) != 'Makeobj')
            echo   $error_line."<br>\n";
      }
      $datfile_lines = file($dat_file_exp);

    }
    
    // check encoding
    $dat_as_string = implode($datfile_lines);
    $encode = "ISO-8859-1";
    if (substr($datfile_lines[0],0,3) == "\xEF\xBB\xBF") 
    { $datfile_lines[0] = substr($datfile_lines[0],3);
      $encode = "UTF-8";
    }  
     if (substr($datfile_lines[0],0,2) == "\xFE\xFF" or 
         substr($datfile_lines[0],0,2) == "\xFF\xFE" ) 
    { $datfile_lines[0] = substr($datfile_lines[0],2);
      $encode = "UTF-16";
    }  
    if (substr($datfile_lines[0],0,2) == "\xC2\xA7") // Schreibt Translator so für UTF 8
    { $datfile_lines[0] = substr($datfile_lines[0],2);
      $encode = "UTF-8";
    }  
    $encode = mb_detect_encoding($dat_as_string, "UTF-8,".$encode. ",ISO-8859-1,ISO-8859-2,Windows-1252",true);
    if ($encode == false) { echo "<h2>"."= encoding nicht erkannt</h2>\n"; return; }
    echo " encoding assumed=<b>".$encode. "</b><br>";

    //now add last line with --- to ensure, that we allways process last object
    $datfile_lines [] = "---";

    //this variable will hol currently processed object
    $current_object = new simu_object($target_version_id, $t_size);

    //go through all lines
    foreach ($datfile_lines as $line)
    {
        //at first trim all junk (file reads lines with their ending)
        //only trim from LEFT, since in special cases we need exttra space at the end of line
        // - simutrans program strings
        $line = trim($line, "\x00..\x1F");
        $line = mb_convert_encoding($line,"UTF-8",$encode);
        $line = str_replace("\t", ' ',ltrim ($line));

        //chek if the line contained anythign else but line ending
        if (trim($line) == "") continue;
        //now we are sure that line is at least 1 char long

        //check for object separator
        if ($line[0] == '-')
        {
            //echo "Object separator";
            //check wheather we read anyuthing
            if ($current_object->modified)
            {
                //object collected, take care of it!
                //if we got full object, save it
                if ($current_object->is_valid ())
                {
                    //save
                    //store object to the db (takes care of images)
                    //(second parameter says wether to store images or not)
                    if ($current_object->save_object_to_db ($dat_dir, $file_name, $store_images,$vb)) $obj_count++;
                    $found_count++;
                } else $current_object -> debug_html();


            } else echo "\n<!-- Empty object -->\n";

            //resets the object (star reading new one)
            $current_object = new simu_object ($target_version_id, $t_size);
            //do not process this line any more
            continue;
        }

        //now line is normal dat line (or comment)
        //let parser handle this line
        $current_object -> parse_line ($line);

    }
    //parsed whole dat
    //echo "<b>File:</b> <i>$dat_file_name</i> parsed and processed. <b>$obj_count of $found_count found object(s) imported.</b>";
    printf($LNG_OBJ_IMPORT[15],basename($dat_file_name),$obj_count,$found_count);
 
}




////////////////////////////////////////////////////////////////////////////////
//this function takes care of acquiring zip file unpacking it and
//calling functions to take care for the content
function import_zip ($version_id,$tmp_dir)
{
    //will get filled during call of import file
    global $versions_all,$language_all, $tempfilepfad, $LNG_OBJ_IMPORT;
    GLOBAL $ob_unmodified,$ob_updated,$ob_inserted;
    GLOBAL $sum_prob,$sum_img,$sum_tran,$sum_alles;
 
     //count of object with successful save
    $ob_unmodified=0; $ob_updated=0; $ob_inserted=0;
    $sum_prob  = 0;
    $sum_img   = 0;
    $sum_tran  = 0;
    $sum_alles = 0;
    // files from last import delete
	verzeichnis_del($tmp_dir);

    //note - system() will print messages to std out, so we need to enclose them
    echo "<p class='tight'>";
    //unzip the file to the given tmp directory
	
	if ( isset($_FILES['uploadfile']['name'])) {
		$tmp_file = $tmp_dir."/".basename($_FILES['uploadfile']['name']);
	}
	
if ( substr($tmp_file, strlen($tmp_file) - 3) == "zip" ) {
	
	
	if ( file_exists($tmp_file) ) { unlink($tmp_file); }

	if ( isset($_FILES['uploadfile']['name'])) {
		copy($_FILES['uploadfile']['tmp_name'], $tmp_file);
	} 

    echo "Extracting the zip file...<br>\n";

    /*
	 PhpConcept Library - Zip Module 2.8.2
	 --------------------------------------------------------------------------------
	 License GNU/LGPL - Vincent Blavet - August 2009
     http://www.phpconcept.net
	 --------------------------------------------------------------------------------
    */
	include_once ('include/pclzip/pclzip.lib.php');

	$archive = new PclZip($tmp_file);
    echo "Extracting the zip file...new ok<br>\n";

	$v_list = $archive->extract(PCLZIP_OPT_PATH, $tmp_dir);    
    echo "Extracting the zip file..extract ok.<br>\n";

    if ($v_list == 0)
    {
        //variable above holds return value of the system command, do not continue if else than 0
        echo "</p>".$LNG_OBJ_IMPORT[16]."<br>\n";
        echo "Error : ".$archive->errorInfo(true);
        return FALSE;
    }
    echo "</p>";

    echo   $LNG_OBJ_IMPORT[17]."<br>\n";
    printf ($LNG_OBJ_IMPORT[18]."<br>\n", $version_id, $versions_all[$version_id]);


    //these statements will browse the structure
    //and lunch dat processing for each data found...
    //function browsedir ($dir,&$filecount,&$files,$rexp=".*") {
    $file_count = 0;
    $files;
    //match is for reg exp, so we want ending with dat
    browsedir($tmp_dir,$file_count,$files,"#.*\\.dat\$#i",get_version_tile_size($version_id));

    // print_r($files);
    printf($LNG_OBJ_IMPORT[19]."<br>\n", $file_count);
	
    foreach ($files as $dat_file_to_process)
    {
        //parses the dat file, creates objects, rips images and stores all to the db
        process_dat_file($dat_file_to_process['filename'], $version_id,$dat_file_to_process['tile_size'],$tmp_dir);

    }
    echo "<h2>All .dat done. New inserted=$ob_inserted, updated=$ob_updated, unmodified=$ob_unmodified</h2>"; 
    echo "Laufzeit properties Summe=".$sum_prob."<br>";
    echo "Laufzeit images     Summe=".$sum_img."<br>";
    echo "Laufzeit translate  Summe=".$sum_tran."<br>";
    echo "Laufzeit objects    Summe=".$sum_alles."<br>";

    // search compat.tab
    $file_count = 0;
    browsedir($tmp_dir,$file_count,$files,"#^compat\\.tab\$#i");

    // print_r($files);
    echo "<hr>\n";
    echo "<h2>compat.tab files found = ". $file_count."</h2>";
    global $rowNo,$rowWithDataNo,$objectNo;
    $compat = array();
    $objectNo = 0;
    foreach ($files as $dat_file_to_process)
    {   $file_name = $dat_file_to_process['filename'];
        echo $LNG_OBJ_IMPORT[14].": <i>".path_html_process($file_name,$tmp_dir)."</i>"."<br>\n";
        $datfile_lines = file($file_name);
        if ($datfile_lines === false)  printf("<b>".$LNG_LOAD3[3]."</b>", $file_name);  
        else
        { $datfile_lines[] = "#";  // add comment line for error checking at the end
          $rowNo = 0;
          $rowWithDataNo = 0;
          foreach ($datfile_lines as $row) tr_parseRow(trim($row),$compat,"UTF-8");
        }
    }
    $compatNo = $objectNo;
    
    
    // search translate files
    $file_count = 0;
    browsedir($tmp_dir,$file_count,$files,"#^[a-z]{2,3}\\d?\\.tab\$#i");

    // print_r($files);
    echo "<h2>translate files found = ".$file_count."</h2>";

    foreach ($files as $dat_file_to_process)
    {   $path = $dat_file_to_process['filename'];
        $file_name = basename($path);
        $language = substr($file_name,0,-4);
        if (substr($language,-1) < '9') $language = substr($language,0,-1);
        echo "<hr>\n".$LNG_OBJ_IMPORT[14].": <i>".path_html_process($file_name,$tmp_dir)."</i>"."<br>\n";
        tr_parsetab($file_name,$path,$language,$version_id,3,$compatNo,$compat); 
    }

    // search for .txt files in languge folder
    if ($version_id > 300 or $version_id == 10 or $version_id == 102)
    { $file_count = 0;
      browsedir($tmp_dir,$file_count,$files,"#.*\\.txt\$#i");
      echo "<h2>translate text files found = ".$file_count."</h2>";
      foreach ($files as $dat_file_to_process)
      {  $path = $dat_file_to_process['filename'];
         $file_name = basename($path);
         $zip_path = explode('/',path_html_process($path,$tmp_dir));
         if ($file_name == "translate_users.txt") continue; 
         echo "Path=".path_html_process($path,$tmp_dir)."<br>";
         $language = ''; # find offset of language dir because there can be capselt in main dir
         $object_name = $file_name;
         foreach ($zip_path as $test_lang)
         { $low_lang = strtolower($test_lang);
           if(isset($language_all[$low_lang])) $language = $low_lang;
           elseif ($language != '' and $test_lang != $file_name)  $object_name = $test_lang.'#'.$object_name;
         }
         if (isset($language_all[$language]))
         { echo "<hr>\n".$LNG_OBJ_IMPORT[14].": <i>".path_html_process($object_name,$tmp_dir)."</i>"."<br>\n";
           tr_parsetab($object_name,$path,$language,$version_id,3,$compatNo,$compat); 
         }
      }

   }

    //finished
    echo "<hr />\n";

    //clear after myself
    // files from import delete
    verzeichnis_del($tmp_dir);

    echo $LNG_OBJ_IMPORT[20]."<br>\n";


} else {

//$tmp_file = "../data/tmp/".basename($_FILES['uploadfile']['name']);
	if ( isset($_FILES['uploadfile']['name'])) {
		copy($_FILES['uploadfile']['tmp_name'], $tmp_file);
	} 

    //    echo $tmp_file."<br />";
    //    echo file_exists($tmp_file)."<br />";


        process_dat_file($tmp_file, $version_id, get_version_tile_size($version_id),$tmp_dir);

    //finished
    //clear after myself
    echo "<hr />\n";
	
	unlink($tmp_file);
    
    echo $LNG_OBJ_IMPORT[20]."<br>\n";
}

}



////////////////////////////////////////////////////////////////////////////////
//this function will prepare the file import
//no argument needed, will directly use built in $_FILES
function import_file ()
{
    global $LNG_OBJ_IMPORT,$versions_all;

    //version id taken from post data
    $version_id = intval($_POST['version']);
    if (!isset($versions_all[$version_id]) or $version_id == 255) 
    { echo "<h1>".$LNG_OBJ_IMPORT[23]."</h1>\n";
      return FALSE;
    }
    //then check if user has rights to import to this version
    if (!user_access_check($version_id))
    { echo ("<p>".$LNG_OBJ_IMPORT[24]."</p>\n");
      return FALSE;
    }
    echo "<hr>\n".$LNG_OBJ_IMPORT[21]."<br>\n";

    //make room for file extraction
    echo $LNG_OBJ_IMPORT[22]."<br>\n";
    if (!is_dir(TMP_DIRECTORY) ) {mkdir (TMP_DIRECTORY, 0777); }
    $tmp_dir = TMP_DIRECTORY.$version_id;
    if (!is_dir($tmp_dir) ) {mkdir ($tmp_dir, 0777); }

    if ( isset($_FILES['uploadfile']['name']) ) 
    { //now we know that we have file and correct user
      //proceed to uploadign process
      echo "<p class='tight'><b>".$LNG_OBJ_IMPORT[25].":</b> ".$_FILES['uploadfile']['name'] . "<br />\n";
      echo "<b>".$LNG_OBJ_IMPORT[26].":</b> ".$_FILES['uploadfile']['size'] . " Byte</p>\n";
      //takes care of uploaded file and calls functions to process it
      import_zip ($version_id,$tmp_dir);
      echo "<b>".$LNG_OBJ_IMPORT[27]."</b><br>\n";
    } else echo "<h1>".$LNG_OBJ_IMPORT[23]."</h1>\n";
}

?>




