<?php
require_once("./include/parameter.php");
include ('./include/obj.php');
include ('./include/translations.php');
////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////////
/*
 * create line 80 utf8-chars in width (not including newline), starting with "#"
 * ending with "#\n" with $text in the center
 * equivalent to str_pad (but works on multibyte)
 */
function do_header_line($text) 
{
    if (mb_strlen($text,INTERNAL_ENCODING)>78) return "#".mb_substr($text,0,78,INTERNAL_ENCODING)."#\n";

    $len=78-mb_strlen($text, INTERNAL_ENCODING);
    if ($len%2==0) {
       $first=($len/2);
       $last=$first;
    } else {
       $first=($len-1)/2+1;
       $last=($len-1)/2;
    }
    $empty_line="#" . str_repeat(" ",$first) . $text . str_repeat(" ",$last) . "#\n";
    return $empty_line;
}


////////////////////////////////////////////////////////////////////////////////
// creates translation file header
function create_header($choice,$lang_id, $version_id, $target_encoding, $font1)
{   GLOBAL $versions_all,$language_all;
    //create empty output
    $res = "";

    //if we are doing base file, we will need to insert language name and font information
    if ($version_id == BASE_TEXTS_SET_ID or $version_id == EXTE_TEXTS_SET_ID ) {
        $res .= $language_all[$lang_id]."\n";
        $res .= "PROP_FONT_FILE\n" . $font1 . "\n";
        //second font is not used anymore
    }

    $res.= "################################################################################\n".
           "# DO NOT EDIT THIS FILE.                                                       #\n".
           "# USE   https://translator.simutrans.com    FOR YOUR CHANGES                   #\n".
           "# AND DOWNLOAD THIS FILE AGAIN WITH YOUR SUGGESTIONS                           #\n".
           "################################################################################\n".   
           "#                                                                              #\n";
    //different headding for basetexts
    if ($version_id == BASE_TEXTS_SET_ID) 
    $res .="#                        Simutrans Base Translation File                       #\n";
    else 
    $res .="#                Simutrans Scenario Specific Translation File                  #\n";
    if ($choice == 'suggestion')
    $res .="#       Warning: This file also contains unofficial suggestions                #\n";
    if ($choice == 'save-sugge')
    $res .="#       Warning: this File contain only suggestions                            #\n";
    if ($choice == 'save')
    $res .="#       Warning: this is a Savefile do not use it in Simutrans                 #\n";

    $res .= do_header_line("Scenario: $versions_all[$version_id]").
            do_header_line("Language: $lang_id $language_all[$lang_id]").
            do_header_line("Encoding: $target_encoding").
            do_header_line("Font: $font1").
            do_header_line("Date Created: ".date("j.m Y")).
           "#                                                                              #\n".
           "################################################################################\n";
    return $res;
}

////////////////////////////////////////////////////////////////////////////////
// this function will process the line, removing or replacing
// unwnated characters.
function line_preprocessor ($line,$target_encoding)
{   $l = str_replace("\n", '\n',$line);
    $l = str_replace("\r", '',$l);
    
    $tenc = mb_convert_encoding($l, $target_encoding, INTERNAL_ENCODING);

    $tt = mb_convert_encoding($tenc     ,INTERNAL_ENCODING, $target_encoding);
    if ($tt != $l) echo "Zeichen in ".$target_encoding." nicht darstellbar<br>".
       diff_string(htmlentities($tt, ENT_QUOTES,"UTF-8"),htmlentities($l, ENT_QUOTES,"UTF-8"))."<br>";

    return $tenc;
}


////////////////////////////////////////////////////////////////////////////////
function generate_translation_text ($choice,$lang_id, $version_id, $target_encoding, $font1)
{
   GLOBAL $tabpfad,$tr_db_name,$tr_name_pattern;   

   $res = "";

   if ( $version_id == EXTE_TEXTS_SET_ID and $choice != 'save-sugge' and $choice != 'save' ) 
   {  $tab = 'translations_'.BASE_TEXTS_SET_ID;
      $query= "SELECT * FROM `objects` o JOIN `".$tab."` t ON (o.object_id=t.object_object_id) WHERE t.language_language_id='".$lang_id."' AND o.version_version_id=".BASE_TEXTS_SET_ID." ORDER BY obj, obj_name COLLATE utf8_unicode_ci";

      $result = db_query($query);

      $obj= "";
      while($row=db_fetch_object($result))
      {  if ( $res == '' ) 
         { $res=create_header($choice,$lang_id, $version_id, $target_encoding, $font1);
           $res.=str_repeat('#'.str_pad('Simutrans Base Texts',79,'_',STR_PAD_BOTH)."\n",2);
         }
         //add a header at the begining of new section (depending on object type
         if ($obj!=$row->obj)
         { $obj=$row->obj;
           $res.=str_repeat('#'.str_pad($row->obj,79,'_',STR_PAD_BOTH)."\n",2);
         }

         //check if translation is not empty (do not print empty one)
         if( $row->tr_text != "" and ($row->obj_name != $row->tr_text or $choice == 'save'))
         { $res .= $row->obj_name."\n"; //print object name
           $res .= line_preprocessor ($row->tr_text,$target_encoding)."\n"; //print translation
         }
      }
      db_free_result($result);

      $res.=str_repeat('#'.str_pad('Simutrans Extended Texts',79,'_',STR_PAD_BOTH)."\n",2);
   }

   $tab = 'translations_'.$version_id;
   //select the objects
   //order by object type (way, builing...) and than by name
   $query= "SELECT * FROM `objects` o JOIN `".$tab."` t ON (o.object_id=t.object_object_id) WHERE t.language_language_id='".$lang_id."' AND o.version_version_id=".$version_id."  ORDER BY obj, type, obj_name COLLATE utf8_unicode_ci ";
  
   $result = db_query($query);

   $obj= "";
   while($row=db_fetch_array($result))
   { $obj_name = $row['obj_name'];
     foreach ($tr_db_name as $k => $f)
     { $f_tab = explode(',',$f);
       $tr_text = $row[trim($f_tab[0])];
       $suggest = $row[trim($f_tab[1])];
       if ($choice == 'suggestion' and $suggest != '') $tr_text = $suggest;
       if ($choice == 'save-sugge')                    $tr_text = $suggest;
       if ($choice != 'save-sugge' and $choice != 'save' and $k != 't' and $k != 'd' ) continue;
       if ($choice != 'save-sugge' and $choice != 'save' and $row['obj'] == 'web_site') continue;
       //check if translation is not empty (do not print empty one)
       if( $tr_text != "" and ($obj_name != $tr_text or $choice == 'save'))    // translator header not in scenario files
       { if ( $version_id < 300 and $res == '' )  $res=create_header($choice,$lang_id, $version_id, $target_encoding, $font1);
         //add a header at the begining of new section (depending on object type
         if ($row['type'] != '') $objnew = $row['obj'].' - '.$row['type'];
         else                    $objnew = $row['obj'];
         if ($obj != $objnew)
         { $obj = $objnew;
           if ( $version_id < 300 ) $res.=str_repeat('#'.str_pad($obj,79,'_',STR_PAD_BOTH)."\n",2);
         }

         // not print object name by object scenario_textfile
         if ( $obj != 'scenario_textfile' ) 
         {  //print object name
            $res.=str_replace('?',$obj_name,$tr_name_pattern[$k])."\n";
         }

         if ( strpos($tr_text, '&nbsp;') == 0 )  $tr_text = str_replace('&nbsp;', ' ', $tr_text);

         //print translation
         $tenc = line_preprocessor ($tr_text,$target_encoding);

         //on other hand, if text is vehicle or factory name, it cannot be too long
         //we need to check for wierd cases when such text is too long and trim it.
         if ($k == 't' and ($obj == 'vehicle' or $obj == 'factory') and $version_id != 200)
         {  if ( mb_strlen($tenc, $target_encoding) > VEH_NAME_LEN)
            { $tenc = mb_substr ($tenc, 0, VEH_NAME_LEN, $target_encoding) . "\n";
              echo "Vehicle Name to Long shorted: ".$obj_name."<br>\n";
            }
         }
 
         // scenario textfile to subfolder
         if ( $version_id >= 300 and $obj == 'scenario_textfile' ) 
         {  if ( !is_dir($tabpfad.$version_id."/".$lang_id ) )  mkdir($tabpfad.$version_id."/".$lang_id, 0775); 
            if ( strpos($obj_name, '#') != false )  
            {  $d = explode( '#', $obj_name);
               $filename = $tabpfad.$version_id."/".$lang_id."/".$d[0]."/".$d[1];
               if ( !is_dir($tabpfad.$version_id."/".$lang_id."/".$d[0] ) )  mkdir($tabpfad.$version_id."/".$lang_id."/".$d[0], 0775); 
            } else  $filename = $tabpfad.$version_id."/".$lang_id."/".$obj_name;
            // save scenario textfile to lang subfolder
            $prefix = ($target_encoding == "UTF-8")?"\xC2\xA7":"";
            $fp=fopen($filename,"wb");
            fwrite($fp, $prefix . $tenc);
            fclose($fp); 
         }

         //for any other object, just append the line to the output
         else $res.=$tenc."\n";
       }
     }
   }
   db_free_result($result);

   return $res;
}


function generate_help_files ($lang_id, $setid, $setpfad, $show )
{   
   global $htmlpfad, $htmlexpfad, $LNG_WRAP, $LANG_NAMEN;

    //detect output encoding
    $err = db_query ("SELECT lng_coding, lang_code2, font1 FROM `languages` WHERE `language_id`='".$lang_id."';");
    $row = db_fetch_array($err);
    $target_encoding = $row['lng_coding'];
    $m = $lang_id;      
    if ( strlen($lang_id) > 2 ) $m = $row['lang_code2'];

    if ( $setid == HELP_BASE__SET_ID )         // help files Simutrans standard
    { $html_folder = $htmlpfad.$lang_id;
      $page_title = 'Simutrans InGame Help Online';  
    } elseif ( $setid == HELP_EXTEN_SET_ID )   // help files Simutrans extended
    { $html_folder = $htmlexpfad.$lang_id;  
      $page_title = 'Simutrans Extended InGame Help Online';  
    }
    if ( !is_dir($setpfad ) )    mkdir($setpfad, 0775); 
    if ( !is_dir($setpfad.$m ) ) mkdir($setpfad.$m, 0775); 
    if ( !is_dir($html_folder) ) mkdir($html_folder, 0775); 
    unlink ($html_folder.'/general.html'); 
    
    $htext  = '';
 
    //prefix - � for UTF-8 files
    //UTF "�" C2 A7
    // $prefix = ($target_encoding == "UTF-8")?"\xC2\xA7":"";
    $prefix = '';

    $tab = 'translations_'.$setid;
    $query= "SELECT * FROM `objects` o JOIN `".$tab."` t ON (o.object_id=t.object_object_id) WHERE t.language_language_id='".$lang_id."' ORDER BY obj_name ";
    $result = db_query($query);

    while($row=db_fetch_object($result)) 
    { 
      $help_file = $row->obj_name;
      if ($row->tr_text == '')
      { $htext .= str_replace('.txt', '', $help_file)." --- \n";
        continue;
      }

      $fp=fopen($setpfad.$m.'/'.$help_file,"wb");

      if ( $show == 1 )  print_line ("<p class='tight'>".$LNG_WRAP[1]." " . $help_file .'-'.$target_encoding. "</p>");
      $filetext = line_preprocessor ($row->tr_text,$target_encoding);
      $filetext = trim(str_replace('\n', "\n", $filetext))."\n"; 
      // alle Linux Linebreaks durch Windows Linebreaks ersetzen
//    $filetext = str_replace("\n", "\r\n", $filetext);

      fwrite($fp, $prefix .$filetext);
      fclose($fp); 

      $htext .= generate_htmlhelp_files ($html_folder, $row->obj_name,$page_title, $filetext, $target_encoding, $setid,$lang_id);
   }
   db_free_result($result);

   write_helpindex_files($setid,$lang_id,$html_folder,$page_title, $htext  );

}

// export help files to html files
function generate_htmlhelp_head($file,$setid,$lang_id,$code,$page_title,$imgdata,$htext)
{ global $LANG_NAMEN;
  if ($file =='index')
  {   $text22 = tr_translate_text(200,'$LNG_WRAP[22]',$lang_id,'');
      $text23 = tr_translate_text(200,'$LNG_WRAP[23]',$lang_id,'');
      $head = $text23.'<p>'.$LANG_NAMEN[$lang_id]." -> Links -> ";
    $foot = '</p><p>'.$text22.' <a href="https://makie.de/translator/script/directions.php?vers='.
            $setid.'&lang='.
            $lang_id.'" target="_blank">SimuTranslator</a></p>';
  } else
  { $head = '<h1 class="pagename">'.$page_title."</h1>\n<hr>\n";
    $foot = '
<hr>

<object data="index.html" style="width:100%; height:400;"><a href="index.html">Index</a>
    ';
  }
  return sprintf('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=%s" />
    <meta name="keywords" content="Simutrans,Help,Simutrans Help">
     <link href="../style_helpfile.css" type="text/css" rel="StyleSheet" />
    <title>%s</title>
</head>
<body>

%s

%s
%s

%s

</body>
</html>'
   ,$code,$page_title,$head,$imgdata,$htext,$foot); 
}

function generate_htmlhelp_files ($html_folder, $help_file, $page_title, $htext, $code, $setid,$lang_id)
{  global   $imagepfad;

   // images on page

   if ( file_exists($html_folder.'/img/'.$help_file.'.png') ) 
   {   $tf = './img/';
       $ta = $html_folder.'/img/';
   } else 
   {   $tf = '../../img/'.$setid.'/';
       $ta = $imagepfad.$setid.'/';
   }    
   $imgfile = $help_file.'.png';
   $timgfile = $help_file.'_t.jpg-';
        
   $imgdata = '&nbsp;';
   if ( file_exists($ta.$timgfile) )
   { $imgdata = '<div class="imagefile"><a href="'.$tf.$imgfile.'" target="_blank"><img src="'.$timgfile.'" alt=""></a></div>';
   } elseif ( file_exists($ta.$imgfile) ) 
   { $imgdata = '<div class="timagefile"><img src="'.$tf.$imgfile.'" alt="" ></div>';
   } 
   
   $htext = str_replace('.txt', '.html', $htext);
   $name = str_replace('.txt', '', $help_file);
  
   $output = generate_htmlhelp_head($name,$setid,$lang_id,$code,$page_title,$imgdata,html_format($htext));

   $htmlhelp_file = $name .'.html';

   $fp=fopen($html_folder.'/'.$htmlhelp_file,"wb");
   fwrite($fp, $output);
   fclose($fp); 
   return '<a href="./'.$htmlhelp_file.'" target="_top">'.$name."</a> --- \n";
}

function write_helpindex_files ($setid,$lang_id,$html_folder,$page_title, $htext)
{
  $output = generate_htmlhelp_head("index",$setid,$lang_id,"utf-8",$page_title,'',$htext);

  $filet = $html_folder.'/index.html';

  //echo $filet."<br />";
  $fp=fopen($filet,"wb");
  fwrite($fp, $output);
  fclose($fp); 

  if ( !file_exists($html_folder.'/general.html') ) 
  { //missing general.html then copy index.html to general.html
    copy ($html_folder.'/index.html', $html_folder.'/general.html');
  } 
}

   /*   
    //finds users translating this language.
    $query="SELECT `translator_user_id` FROM `translate` t JOIN `users` u ON ( t.translator_user_id = u.u_user_id ) ". 
        "WHERE u.state = 'active' AND t.lng_tr_language_id='".$lang_id."'";
       $user_info_querry="SELECT * FROM `users` WHERE `u_user_id`='".$l->translator_user_id."'";
      $user_info_result = db_query($user_info_querry);
      $a=db_fetch_object($user_info_result);
      $res.= "$a->real_name - $l->translator_user_id"));
      //$res.=  do_header_line("$a->email; $a->note; Texts: $texts_translated");
*/


function generate_userlist ($setid,$tabpfad)
{
    $output = "################################################################################\n".
              "# User list: all authors and contributors to Simutrans translation             #\n".
              "# https://translator.simutrans.com                                             #\n".
              "# DO NOT EDIT THIS FILE. It will be created automatically                      #\n".
              "################################################################################\n";
    $version_q = "SELECT u_user_id,real_name,note FROM `users`";
    $version_r = db_query($version_q);
    
    //result should contain one row with one filed  
    // $name = (db_fetch_row($version_r));  
    while ($row = db_fetch_row($version_r))
    { if ( $row[0] !== 'test' ) 
      { $n = $row[0];
        if ($row[1] != '') $n = str_pad($n,15,' ').' - '.$row[1];
        if ($row[2] != '') $n = str_pad($n,45,' ').' note: '.trim($row[2]);
        $output .= $n."\n"; 
      }    
    }
    db_free_result($version_r);
    
    $filet = $tabpfad.'_translate_users.txt';    
          
    //echo $filet."<br />";
    $fp=fopen($filet,"wb");
    fwrite($fp, $output);
    fclose($fp); 
}

?>
