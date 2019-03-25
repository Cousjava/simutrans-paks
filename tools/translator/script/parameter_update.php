<?php

$version_id    = 20;
$tile_size     = 0;
$calc_file = '../data/set_'.$version_id.'_calc.txt';
$dat_dir   = '../pak128german_dev';
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

function path_html_process($p)
{ global $version_id;
  return htmlentities ($p, ENT_QUOTES, "UTF-8");
}

////////////////////////////////////////////////////////////////////////////////
//this function creat a list of all dat files in the tree startin at dir
//(inherited from original ST)
function browsedir1 ($dir,&$filecount,&$files,$rexp,$tile_size)
{
  if ($dp = @opendir($dir)) {
    $dn=0;
    $ds=array();
    $st_ignore = false;
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
      browsedir1($ds[$i],$filecount,$files,$rexp,$tile_size);
    }
  }
}

function browsedir ($dir,&$filecount,&$files,$rexp,$tile_size=0)
{ echo 'beginne zu lesen'.$dir.'<br>';
  $filecount=0;
  $files=array();
  browsedir1 ($dir,$filecount,$files,$rexp,$tile_size);
  echo 'ende<br>';
}



////////////////////////////////////////////////////////////////////////////////
//this function takes care of one dat file, processing it
//storing the information and other taks....
//for perfromance tweaks, requires also target version id and the tile size (to save queries)
function process_dat_file ($dat_file_name, $target_version_id, $t_size)
{   GLOBAL $LNG_OBJ_IMPORT,$calc_tab,$ob_updated,$ob_unmodified;

    echo "<hr>\n".$LNG_OBJ_IMPORT[14]." <b>".path_html_process($dat_file_name)."</b><br>\n";

    //count of object with successful save
    $obj_count = 0;
    //count of all found valid objects
    $found_count = 1;
    $obj_name = '';
    $need_update=0;

    //extracts a directory from the path
    //(used later for iamge referencing)
    $path_inf = pathinfo($dat_file_name);
    $dat_dir  = $path_inf["dirname"];

    //reads a content of datfiles to an array of lines
    //dat files are usually rather small (at most few tens of KB)
    //array file ( string filename [, int use_include_path [, resource context]] )
    ini_set("auto_detect_line_endings", true);
    $dl = file($dat_file_name);

 
    //go through all lines
    for ($i = 0; $i < count($dl); $i++)
    { $z = rtrim($dl[$i]);
      if (substr($z,0,1) == '-')
      { $obj_name = '';
        $found_count++;
      }
      if ($z == '#	Letzte Bearbeitung: $Date$ - $Author$') $z = '';
      if ($z == '#_EO_') $z = '';
      $dle = explode('=',$z);
      if (count($dle) == 2)
      { $dl0 = strtolower($dle[0]);
        $dl1 = trim($dle[1]);
        if ($dl0 == 'name')
        { $obj_name = $dl1;
          echo 'name='.$obj_name."<br>\n";
          $obj_count++;
        }
        if ($obj_name != '')
        { if (isset($calc_tab[$obj_name.'>'.$dl0]))
          { echo 'gefunden:'.$dl0.'='.$dl1;
            $w = $calc_tab[$obj_name.'>'.$dl0];
            unset($calc_tab[$obj_name.'>'.$dl0]);
            if ($w ==  $dl1) 
            { echo " Wert ist OK<br>";
              $ob_unmodified++;
            }
            else
            { echo " Wert geändert von ".$dl1." in ".$w."<br>";
              $z = $dl0.'='.$w;
              $ob_updated++;
            }
          }
        }
      }
      if (strlen($z) > 0) $z = $z . "\r\n";
      if ($dl[$i] != $z) { $dl[$i] = $z; $need_update++; }
    }
    if ($need_update > 0)
    { $fp=fopen($dat_file_name,"wb");
      fwrite($fp,implode($dl));
      fclose($fp);
      echo "File :".$dat_file_name." geschrieben <br>\n";
    }
    //parsed whole dat
    echo "<b>File:</b> <i>$dat_file_name</i> processed. <b>$obj_count of $found_count found.</b>\n";
}

echo 'lesen Calc_File'.$calc_file.'<br>';
$calc_lines = file($calc_file);

$calc_tab = array();

foreach ($calc_lines as $cl) 
{ $cl = trim($cl);
  $clt = explode('=',$cl);
  $calc_tab[$clt[0]] = $clt[1];
}

echo 'lesen Inhaltsverzeichnis <br>';


    //will get filled during call of import file
    global $version_id, $LNG_OBJ_IMPORT;
    $ob_unmodified=0; $ob_updated=0;
     
    //these statements will browse the structure
    //and lunch dat processing for each data found...
    //function browsedir ($dir,&$filecount,&$files,$rexp=".*") {
    $file_count = 0;
    $files;
    //match is for reg exp, so we want ending with dat
    browsedir($dat_dir,$file_count,$files,"#.*\\.dat\$#i",$tile_size);

    // print_r($files);
    printf($LNG_OBJ_IMPORT[19]."<br>\n", $file_count);
	
    foreach ($files as $dat_file_to_process)
    { process_dat_file($dat_file_to_process['filename'], $version_id,$dat_file_to_process['tile_size']);
    }
    echo "<h2>All .dat done. updated=$ob_updated, unmodified=$ob_unmodified</h2>"; 


    echo $LNG_OBJ_IMPORT[20]."<br>\n";

foreach ($calc_tab as $cn => $cw)
{ echo "nicht gefunden: ".$cn."=".$cw."<br>\n";
}


?>
