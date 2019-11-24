<?PHP /* Routines for read and write translations */

/* wie werden die Texte gespeichert
normale Texte enthalten keine  x0A = LF oder 0D = CR da dies das Trennzeichen in der xx.tab ist
es können keine importiert werden und es werden keine exportiert
im editor eingegebene x0A und x0D werden entfernt
beim export werden x0A in \n umgesetzt weil historisch in der DB noch welche enthalten sein könnten

Detail Texte
hier gilt das geleiche wie für normale Texte

help_file
hier stehen die x0A in der Datenbank werden aber bei jeden speichern in \n umgesetzt
im editor für die Anzeige werden \n in x0A umgesetzt
beim Sav export wird nach \n umgesetzt 
im import werden x0A nach \n umgesetzt
beim help_text export wird \n umgesetzt nach x0A damit eine Angeleichung an website texte


web_site ,help_file, history_links, history_text
x0A werden vor dem speichern in \n umgesetzt
in der DB sollten kene x0A enthalten sein 
beim export werden x0A in \n umgesetzt
im editor für die Anzeige werden \n in x0A umgesetzt
im import sollten keine x0A enthalten sein

escaped wird (für die DB) nicht da mit mysqli_prepare geschrieben wird 
und die DB von x00 bis xFF alles enthalten kann

*/

function echo_table_start ($header="",$colum1=NULL,$colum2=NULL,$colum3=NULL)
{   GLOBAL $tab_start_str;
    $tab_start_str  = $header."\n";
    $tab_start_str .= '<table width="100%" cellspacing="0" cellpadding="2" border="1">'."\n";
    if ($colum1 != NULL) $tab_start_str .= "<tr><td><b>".$colum1."</b></td>";
    if ($colum2 != NULL) $tab_start_str .= "<td>".$colum2."</td>";
    if ($colum3 != NULL) $tab_start_str .= "<td>".$colum3."</td>\n";
}

function echo_table_line ($t1,$t2,$t3)
{   GLOBAL $tab_start_str; 
    if ($tab_start_str != "is set") echo $tab_start_str;
    echo '<tr><td style="text-align:left"><b> '. htmlentities ($t1, ENT_QUOTES, "UTF-8") . "</b></td><td>" . 
                        htmlentities ($t2, ENT_QUOTES, "UTF-8") . "</td><td>" .
                       '<span style="white-space: nowrap">' .$t3. '</span></td></tr>'."\n";
          //            htmlentities ($u,  ENT_QUOTES, "UTF-8") . "\n";
    $tab_start_str = "is set";
}                

function echo_table_end ()
{   GLOBAL $tab_start_str; 
    if ($tab_start_str == "is set")  echo "</table><br>\n";
}

/* pattern object-name for output to simutrans */
$tr_name_pattern = array();
$tr_name_pattern['t'] = "?";
$tr_name_pattern['d'] = "factory_?_details";
$tr_name_pattern['h'] = "history_?_text";
$tr_name_pattern['l'] = "history_?_link_url";


/* names for Database */
$tr_db_name = array();
$tr_db_name['t'] = "tr_text, suggestion";
$tr_db_name['d'] = "details_text, details_suggestion";
$tr_db_name['h'] = "history_text, history_suggestion";
$tr_db_name['l'] = "history_link_url, history_link_suggestion";

/* names for log */
$log_field_typ = array(); /* $col_typ */
$log_field_typ['t'] = "text";
$log_field_typ['d'] = "details";
$log_field_typ['h'] = "history_text";
$log_field_typ['l'] = "history_link";

$log_action_typ = array(); /* $tr_action */
$log_action_typ['c'] = "create";
$log_action_typ['i'] = "import";
$log_action_typ['m'] = "move";
$log_action_typ['t'] = "translate";
$log_action_typ['s'] = "suggestion";
$log_action_typ['a'] = "suggestion accept";
$log_action_typ['r'] = "suggestion reject";

function tr_read($obj_id,$version_id,$language,$col_typ)
{ global $tr_db_name,$language_all;
  if (!is_numeric($obj_id)     or $obj_id <= 0)           die("tr_read object_id error");
  if (!is_numeric($version_id) or $version_id < 0)        die("tr_read version_id error");
  if (!isset($language_all[$language]))                   die("tr_read language_id error");
  if (!isset($tr_db_name[$col_typ]))                      die("tr_read col_typ error");
  $sf = $tr_db_name[$col_typ];
  $tab_set = 'translations_'.$version_id;
  $reg = "SELECT $sf FROM $tab_set WHERE object_object_id=$obj_id AND language_language_id='$language'";
  //echo $reg;
  $err = db_query($reg);
  return(db_fetch_row($err));
}

function tr_read_author($obj_id,$version_id,$language)
{ global $language_all;
  if (!is_numeric($obj_id)     or $obj_id <= 0)           die("tr_read_author object_id error");
  if (!is_numeric($version_id) or $version_id < 0)        die("tr_read_author version_id error");
  if (!isset($language_all[$language]))                   die("tr_read_author language_id error");
  $tab_set = 'translations_'.$version_id;
  $reg = "SELECT author_user_id,mod_date FROM $tab_set WHERE object_object_id=$obj_id AND language_language_id='$language'";
  //echo $reg;
  $err = db_query($reg);
  return(db_fetch_row($err));
}

function tr_test_empty($obj_id,$version_id,$language)
{ global $language_all,$tr_db_name;
  if (!is_numeric($obj_id)     or $obj_id <= 0)           die("tr_test_empty object_id error");
  if (!is_numeric($version_id) or $version_id < 0)        die("tr_test_empty version_id error");
  if (!isset($language_all[$language]))                   die("tr_test_empty language_id error");
  $tab_set = 'translations_'.$version_id;
  $reg = "SELECT * FROM $tab_set WHERE object_object_id=$obj_id AND language_language_id='$language'";
  //echo $reg;
  $err = db_query($reg);
  $d   = db_fetch_array($err);
  if ($d == false) echo $obj_id.'nicht gefunden<br>';
  $empty = true;
  foreach ($tr_db_name as $f)
  { $f_tab = explode(',',$f);
    foreach($f_tab as $f_name) if ($d[trim($f_name)] != '') $empty = false;
  }
  return($empty);
}

function tr_translate_text($version_id,$name,$lng='',$blank='&nbsp')
{ global $st,$language_all;
  if ($lng == '') $lng = $st;
  if (!isset($language_all[$lng]))                        die("tr_translate_text $lng language error");
  $transl = $name; 
  if (!is_numeric($version_id) or $version_id < 0)        die("tr_translate_text version_id error");
  if ($version_id == 255) return $transl;
  $tr_t = 'translations_'.$version_id;
  $q3 = db_query ("SELECT tr_text FROM $tr_t 
                   WHERE object_obj_name='".db_real_escape_string($name).
                      "' and language_language_id='".$lng."'");
  $good_t = db_fetch_object($q3);
  if ($good_t and $good_t->tr_text != "")
  { if ($blank == '') $transl = $good_t->tr_text;
    else
    { $t1 = explode('\n',$good_t->tr_text);
      $t2 = explode("\n",$t1[0]);
      $transl = str_replace(' ','&nbsp;',$t2[0].' ');
    }
  }
  return $transl;
}


function tr_update($obj_name,$new_text,$version,$language,$tr_funk,$col_typ,$tr_action) 
/* update a new translatet text in table translations */
/* if $version == "take_from_obj_id" then the $obj_name has to contain the object_id  
/*           tr_funk == 1 = overwrite   2 = insert if empty 3 = not empty insert in suggestion 4 = overwrite suggestions 5 = suggestion_accept 6 = suggestion_reject */
/* return               1 = ok written  2 = insert in empty 3 = not empty insert in suggestion 4 = writen suggestions*/
/*                     11 = upd silent 12 = no change      13 = no change in suggestion    16 = suggestion is already empty              */
/*                                     22 = not empty      23 = suggestion is full to   25 = suggestion is empty        */
/*                      30 = object id is wrong or missing                                                     */
/*                      31 = not found                                                         */
/*                      32 = update failure not written sql error                              */
/*                      33 = Inconsistent Database: Object Name differs internally                */
/*                           database object_id points to false translation                        */
/*                      34 = database object_id points to no translation                       */
/*                      35 = update failure not written     37 = row double                     */
{    global $st_dbi,$tr_db_name,$language_all,$log_field_typ,$log_action_typ;

     if (!isset($language_all[$language]))                   die("tr_update language_id error");
     if (!isset($log_action_typ[$tr_action]))                die("tr_update tr_action error");
     if (!isset($tr_db_name[$col_typ]))                      die("tr_update col_typ error");
     $sf = $tr_db_name[$col_typ];

     /* read object table */               
     if ($version !== "take_from_obj_id") $obj_id = ob_read_by_name($version,$obj_name);
     else                                 $obj_id = $obj_name;
     if ($obj_id == -33) return (33); /* not found */
     if ($obj_id == 0) return (31); /* not found */
     if (!is_numeric($obj_id) or $obj_id <= 0) return (30);
     $obj_st = ob_read($obj_id);
     if ($obj_st === NULL) return (31); /* not found */
     if ($obj_st->object_id != $obj_id) return (30);
     $obj_name = $obj_st->obj_name;
     $obj_typ  = $obj_st->obj;
     $version  = $obj_st->version_version_id;

     $translation_exists = 0; /* read translations table */
     $tab_set = 'translations_'.$version;
     $obj_st = mysqli_prepare($st_dbi, "SELECT translation_id, object_obj_name, ".$sf." FROM ".$tab_set. 
                                      " WHERE object_object_id=? AND language_language_id=?");
     if ($obj_st === false) echo "db_error".mysqli_error($st_dbi);
          mysqli_stmt_bind_param   ($obj_st,'is',$obj_id,$language);
     if (!mysqli_stmt_execute      ($obj_st)) exit(mysqli_stmt_error($obj_st));
          mysqli_stmt_bind_result  ($obj_st, $trans_id,$trans_obj,$trans_txt,$trans_sugg);
     if  (mysqli_stmt_fetch        ($obj_st)) $translation_exists = 1;
          mysqli_stmt_close        ($obj_st);

     if ($obj_typ == 'web_site' or $obj_typ == 'help_file' or $col_typ =='l' or  $col_typ == 'h') 
          $new_text = str_replace("\n", '\n',$new_text); 
     else $new_text = str_replace("\n", ''  ,$new_text);
     $new_text = str_replace("\r", '',$new_text);
     $new_text = str_replace("\0", '',$new_text);
     if ($trans_obj != $obj_name)   
     { echo "\ntr_obj_name$obj_id#".htmlentities($trans_obj,ENT_QUOTES,"UTF-8").
                     "#<br>\nobj_name#".htmlentities($obj_name,ENT_QUOTES,"UTF-8")."<br>\n";
       $obj_st = mysqli_prepare($st_dbi, "UPDATE ".$tab_set." SET object_obj_name=? WHERE translation_id=?");
       if ($obj_st === false) echo "Korr_error".mysqli_error($st_dbi);
            mysqli_stmt_bind_param   ($obj_st,'si',$obj_name,$trans_id);
       if (!mysqli_stmt_execute      ($obj_st)) echo "Korr obj_name:".(mysqli_stmt_error($obj_st)); 
       $c = mysqli_stmt_affected_rows($obj_st); if ($c != 1) echo "Korr obj_name failed nr".$c;
            mysqli_stmt_close        ($obj_st);
       return (33);
     }
     if ($translation_exists == 0)  return (34);
     if ($trans_txt ==  $new_text and $tr_funk < 4)  return (12);
     if ($trans_sugg == $new_text and ($tr_funk == 3 or $tr_funk == 4))  return (13);
     if ($trans_sugg == "" and $tr_funk == 5)  return (25);
     if ($trans_sugg == "" and $tr_funk == 6)  return (16);
     $ta = str_replace("\n", '',$trans_txt);
     $ta = str_replace("\r", '',$ta);
     $ta = str_replace("\0", '',$ta);
     $ta = str_replace('\n', '',$ta);
     $tn = str_replace('\n', '',$new_text); 
     if ($ta == $tn and $tr_funk < 4) $tr_funk = 11; // correct silent 
 //  if ($ta != "")    echo "<br>sind die gleich? +".$ta."+".$tn."+<br>";
     switch ($tr_funk)
     {  case 1 : 
        case 11: $trans_txt =  $new_text;
                 break;
        case 2 : if ($trans_txt == "") $trans_txt = $new_text;
                 else return(22);
                 break;
        case 3 : if ($trans_txt == "") {$trans_txt =  $new_text; $tr_funk = 2;}
                 elseif ($trans_sugg =="") $trans_sugg = $new_text;
                 else return(23);
                 break;
        case 4 : $trans_sugg =  $new_text;
                 break;
        case 5 : $trans_txt = $trans_sugg;
        case 6 : $trans_sugg = "";
                 break;
     }
     if ($trans_txt == $trans_sugg) $trans_sugg = "";
     //mod_date=null
     $user = getenv('REMOTE_ADDR');
     if (isset($_SESSION['userId'])) $user = $_SESSION['userId'];
     $mod_date = date("Y-m-d");
     $sf = str_replace(',','=?,',$sf); /* tr_text=?, suggestion=? */
     $obj_st = mysqli_prepare($st_dbi, "UPDATE ".$tab_set." SET ".$sf."=?, mod_date=?, author_user_id=?  WHERE translation_id=?");
     if ($obj_st === false) echo "db_error".mysqli_error($st_dbi);
          mysqli_stmt_bind_param   ($obj_st,'ssssi',$trans_txt,$trans_sugg,$mod_date,$user,$trans_id);
     if (!mysqli_stmt_execute      ($obj_st)) {echo (mysqli_stmt_error($obj_st)); $tr_funk = 32;};
     $c = mysqli_stmt_affected_rows($obj_st); if ($c != 1) $tr_funk = 35 + $c;
          mysqli_stmt_close        ($obj_st);
     if ($tr_funk < 10)
     {
       // update log
       $t = date("Y-m-d H:i:s", time());
       $log_o_name = str_replace("|", '!',$obj_name);
       // Date and Time | User | Object type | Object name | Language | Message | Object Id
       $data = $t."|".$user."|".$obj_typ."|".$log_o_name."|".$language."|".
       $log_field_typ[$col_typ]." ".$log_action_typ[$tr_action]."|".$obj_id."\n";
       write_log($version, $data);
      $t = date("Y-m-d H:i:s", time());
      if ( isset($_SESSION['userId']) and $tr_action != 'i') {
        $query="UPDATE `users` SET `user_points`=`user_points`+'1',`last_edit`='".$t."' WHERE `u_user_id`='".$user."'";
        $result = db_query($query);
      }
      // web_site write Text in file
      if ($obj_typ == 'web_site' and $col_typ =='t') web_file_write($version,$obj_name,$language,$trans_txt);
     }
     return($tr_funk);
}

function tr_update_obj_id($obj_name,$version,$obj_id)
{  global $language_all,$tr_unmodified, $tr_updated, $tr_deleted, $tr_inserted;
   $tab_set = 'translations_'.$version;
   $copy_lang = array();
   foreach ($language_all as $lang_id => $lang_name) $copy_lang[$lang_id] = $lang_id;
   
   $query="SELECT object_object_id, object_obj_name, object_version_version_id, language_language_id, translation_id ".
         " FROM ".$tab_set." WHERE object_obj_name='".db_real_escape_string($obj_name)."' AND object_version_version_id=".$version;
   $result = db_query($query);
   while ($tr_a = mysqli_fetch_row($result))
   { if ($copy_lang[$tr_a[3]] ==  $tr_a[3])
     { $copy_lang[$tr_a[3]] = ""; // check out language
       if ($tr_a[1] != $obj_name or $tr_a[2] != $version) echo "database error false object in translations ".$obj_name."=".$tr_a[1]."<br>"; 
       if ($tr_a[0] != $obj_id)
       { db_query("UPDATE ".$tab_set." SET object_object_id=".$obj_id." WHERE translation_id=".$tr_a[4]);  
         $tr_updated += db_affected_rows();
       }
       else {$tr_unmodified++; }
     }
     else
     { db_query("DELETE FROM $tab_set WHERE translation_id=".$tr_a[4]);  
       $tr_deleted += db_affected_rows();
       echo "database error duplicate object in translations ".$version.$obj_name.$tr_a[3]."<br>"; 
     }
   } 
   mysqli_free_result($result);
   foreach ($copy_lang as $lang_id)
   { if ($lang_id != "") 
      {  db_query("INSERT INTO ".$tab_set." (object_object_id, language_language_id, object_obj_name, object_version_version_id) ".
	"VALUES (".$obj_id.", '".$lang_id."', '".db_real_escape_string($obj_name)."', ".$version.")");
        $tr_inserted += db_affected_rows();
      }
    }
}

function tr_parseRow($row,&$object,$encode) 
{     global $rowNo,$rowWithDataNo,$objectNo,$LNG_LOAD3,$tr_name_pattern;
      $rowNo++;

      $row = mb_convert_encoding($row,"UTF-8",$encode);

      if (mb_strlen($row) == 0) { printf($LNG_LOAD3[14]."<br />\n", $rowNo); return; }//empty row

      if ($row[0] == '#' )  
      { if ($rowWithDataNo % 2 == 1) 
         { // <b>BAD ERROR: entry is not complet at row: %s, maybe there is a line missing befor this<br />
           printf($LNG_LOAD3[21],$rowNo-1);
           $rowWithDataNo++;
         }
      } else
      {
         $rowWithDataNo++;
         if ($rowWithDataNo % 2 == 1) //even means object name %=modulus
         //object
         {  $objectNo++;
            if (mb_strlen($row) > 255) echo "<b>ERROR:</b> Too long (" . (mb_strlen($row)-1) . " chars) OBJECT NAME at row $rowNo: $row, object skipped!<br />"; //too long row lenght
            foreach ($tr_name_pattern as $tkey => $tv)
            { if ($tv == '?')
              { $object[$objectNo]['name'] = $row;
                $object[$objectNo]['col_typ'] = $tkey;
              } else
              { $ts = explode('?', $tv);
                $ts0 = strlen($ts[0]);
                $ts1 = strlen($ts[1]);
                if (substr($row,0,$ts0) == $ts[0] and
                    substr($row,0-$ts1) == $ts[1])
                { $object[$objectNo]['name'] = substr($row,$ts0,strlen($row)-$ts0-$ts1);
                  $object[$objectNo]['col_typ'] = $tkey;
                  // echo "details found: ".$row." -> ".$object[$objectNo]['name']." -> ".$tkey."<br>";
                }
              }
            }
         } else
         //object description
         {  $object[$objectNo]['descr'] = $row;
         }
      }
}

// if $file_name = "screen" $path contains the text-lines
// if $file_name contains file_name -> $path contains the real path+file_name 
function tr_parsetab($file_name,$path,$language,$version,$tr_funk,$compatNo,$compat)
{  global $LNG_LOAD3,$count_unmodified,$rowNo,$rowWithDataNo,$objectNo;
    $object = array();
    $not_inserted = array(); //array to collect faild inserts
    $rowsChanged =0;
    $count_unmodified = 0;
    $rowNo = 0;
    $rowWithDataNo = 0;
    $objectNo = 0;

    if ($file_name == "screen") $datfile_lines = $path;
    else
    {  if (!is_file($path))          { printf("<b>".$LNG_LOAD3[3]." no File</b>", $file_name); return;  }
       ini_set("auto_detect_line_endings", true);
       $datfile_lines = file($path);
       if ($datfile_lines === false) { printf("<b>".$LNG_LOAD3[3]." empty File</b>", $file_name); return;  }
    } 
    
    db_query("START TRANSACTION");
    //this caused some problems with multibyte texts
    //but we need to get rid of at least new line at the begining (even this may occur!)
    //p - posix mode - treat linebreak as normal charcter
    //preserves tabs and spaces - IMPORTANT FOR BASE FILES
    foreach ($datfile_lines as &$row) $row = trim($row, "\x00\n\r");
    unset($row); // need for PHP Foreach Pass by Reference: Last Element Duplicating? (Bug?)
    //need to preserve whitspace at the end of the line but strip any linebreaks

    // check encoding
    if (preg_match("#^[a-z]{2,3}\$#i",$language) !=1) {echo "<h2>".$language."= Sprach ID darf nur 2 oder 3 Zeichen bestehen</h2>\n"; return;}
    $lang = mysqli_fetch_array(db_query("SELECT language_name, font1,lng_coding FROM languages WHERE language_id='".$language."'"));
    if ($lang === null) {echo "<h2>".$language."= ist keine im Translator gültige Sprache </h2>\n"; return;}

    $encode = $lang[2];
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
    $encode = mb_detect_encoding(implode($datfile_lines),"UTF-8,".$encode.",ISO-8859-1,ISO-8859-2,Windows-1252",true);
    if ($encode == false) { echo "<h2>".$language.$LNG_LOAD3[5]."</h2>\n"; return; } // [5]= encoding nicht erkannt
    
    echo $lang[0].' -> '.$LNG_LOAD3[8] . $language."=".$encode. "<br>";              // [8] encoding assumed=

    if (substr($file_name,-4) != ".txt") 
    { $datfile_lines[0] = str_replace($lang[0],         '#',$datfile_lines[0]); // language "Deutsch" or "English"
      $datfile_lines[1] = str_replace("PROP_FONT_FILE", '#',$datfile_lines[1]); 
      $datfile_lines[2] = str_replace($lang[1],         '#',$datfile_lines[2]); // font from languages
      $datfile_lines[]  = "#";   // add comment line for error checking et the end
      foreach ($datfile_lines as $row) tr_parseRow($row,$object,$encode);
    }
    else
    { $objectNo++;
      $object[$objectNo]['name'] = basename($file_name);
      $object[$objectNo]['col_typ'] = 't';
      $object[$objectNo]['descr'] = implode('\n',$datfile_lines);
    } 
 
   ///////////////////////////////////////////////////////////////////////////
    //printing objects with descriptions and saving to database
    echo "<h2>".$LNG_LOAD3[4]."</h2>\n";

    echo_table_start("",$LNG_LOAD3[6],$LNG_LOAD3[7],$LNG_LOAD3[15]);
    
    for ($o_i = 1; isset($object[$o_i]['name']) ; $o_i++)
    {  $t = $object[$o_i]['name'];
       $u = $object[$o_i]['descr'];
       $c = $object[$o_i]['col_typ'];
       $tr_ok = tr_update($t,$u,$version,$language,$tr_funk,$c,'i'); 
       $e = $LNG_LOAD3[9];                   // updated
       if ($tr_ok == 3) $e = $LNG_LOAD3[11]; // written_as_suggestion
       if ($tr_ok < 10) 
       { echo_table_line ($t,$u,$e);
         $rowsChanged++;
       }
       elseif ($tr_ok < 20) $count_unmodified++; 
       else $not_inserted[] = array ($t,$u,$tr_ok,$c);
    
    }    
    echo_table_end();

    //now a try error = object not found -> convert with compat.tab in new object names
    if ($compatNo > 0)  // <h3>change according to compat.tab</h3>from old objectname<td>to new object
    { echo_table_start($LNG_LOAD3[16],$LNG_LOAD3[17],$LNG_LOAD3[18],$LNG_LOAD3[15]);
      foreach ($not_inserted as &$line)
      { $hit = 0;
        if ($line[2] == 31)
          { $line_name = $line[0];
            for ($compat_i =1; $compat_i <= $compatNo; $compat_i++)
            {  if ($line_name == $compat[$compat_i]['name']) 
               { if ($hit == 0) $hit = $compat_i;
                 else echo "<tr><td>error: double hit in compat.tab line:".$hit." and line:".$compat_i."<br></td></tr>";
               }
            }
            if ($hit > 0) 
              { $a = $line[0];
                $t = $compat[$hit]['descr']; /* is the new object name from compat.tab */
                $u = $line[1];
                $c = $line[3];
                $tr_ok = tr_update($t,$u,$version,$language,$tr_funk,$c,'m');
                if ($tr_ok <  10)
                { $e = $LNG_LOAD3[10];                  // successful saved
                  if ($tr_ok == 3) $e = $LNG_LOAD3[11]; // written_as_suggestion
                  $rowsChanged++;
                  $line[2] = $tr_ok;
                }  
                elseif ($tr_ok <  20) 
                { $e = $LNG_LOAD3[20];                  // text already present
                  $count_unmodified++;
                  $line[2] = $tr_ok;
                } else $e = $LNG_LOAD3[$tr_ok];         // all errors
                echo_table_line ($a,$t,$e);
              } 
          }    
      }
      echo_table_end();
    }
 
    printf($LNG_LOAD3[12],$rowsChanged);      // <h3>Translation texts really imported: %d </h3>
    // <h3> Numbers of texts already contained in the database: %d : unchanged texts are not listed. </ h3>
    if ($count_unmodified > 0) printf($LNG_LOAD3[19],$count_unmodified); 

    if ($rowsChanged > 0)
    {  // Update user uploads translate texts
       $t = date("Y-m-d H:i:s", time());
       db_query("UPDATE `users` SET `user_points_upload`=`user_points_upload`+'1',`last_edit`='".$t."' WHERE `u_user_id`='".$_SESSION['userId']."'");
    }
       
    //now a little help for the translators - collected list of failed inserts
    $h = sprintf("<h3>".$LNG_LOAD3[13] , $version).$language." (" . count($not_inserted) . ")</h3>";
    echo_table_start($h,$LNG_LOAD3[6],$LNG_LOAD3[7],$LNG_LOAD3[15]);
    foreach ($not_inserted as $line)
    { if ($line[2] > 19) echo_table_line ($line[0],$line[1],$LNG_LOAD3[$line[2]]);
    }  
    echo_table_end();
    db_query("COMMIT");
}

?>
