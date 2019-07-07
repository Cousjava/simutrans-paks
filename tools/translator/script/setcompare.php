<?php
require_once("./include/parameter.php");
require_once("./include/quotes.inc.php");
include('./include/select.php');

    /*
        SimuTranslator
        This page listet untranslated set text and translated text from other sets

        Frank Penz
        2009
    */

function other_translate($obj_id, $obj_name, $obj_type, $lang) {
        $return_value = array();
        
        $sql = sprintf ("SELECT `object_id`,`obj_name`, `version_version_id`, `obj` FROM `objects`".
             "where `object_id`<>%s and `obj_name`='%s';",
              $obj_id, 
              quote_smart($obj_name)
              );
       $query = db_query($sql);
       
       while ($row=db_fetch_array($query)) {
           $tab_set = 'translations_'.$row['version_version_id'];
                 $query2= "SELECT * FROM `".$tab_set."` WHERE ".
                  "`object_object_id`='".$row['object_id']."' AND ".
                  "`language_language_id`='".$lang."' AND (tr_text<>'' OR (tr_text IS NOT NULL))";
                  $trans = db_query($query2); 
            if ( db_num_rows($trans) > 0 ) {
                  $row2=db_fetch_array($trans);  
                  if ( $row2['tr_text'] != '' ) {
                     $return_value[] = $row['version_version_id'].'|'.$row['object_id'].'|'.$row2['tr_text']; 
                  }
                  db_free_result($trans);

            }
            

       }
       db_free_result($query);
        
        return $return_value;
   //$sql = "SELECT * FROM ".$searchtable." WHERE ".$suche." ".$awv." ".$awl." ".$t2." ".$awsub." ORDER BY ".$sort." ASC;";
    
    }
    
function update_text($update)
{
   GLOBAL $version_auswahl;
   
   $tab_set = 'translations_'.$version_auswahl;
//echo "<p>".count($update)."<br />";
          for ( $x = 0; $x < count($update); $x++ ) {
             $text_update = quote_smart( html_entity_decode($update[$x][2], ENT_QUOTES, "UTF-8") );
               $query="UPDATE `".$tab_set."` ".
               "SET `tr_text`='".$text_update."' ".
               "WHERE ".
               "`language_language_id`='".$update[$x][1]."' AND ".
               "`object_object_id`=".$update[$x][0].";";
       
   //    echo $query."<br />";
            $result = db_query($query);      
    // Logeintrag
   $t = date("Y-m-d H:i:s", time());
   $message = 'translate';
   // Date and Time | User | Object type | Object name | Language | Message | Object Id
   $data = $t."|".$_SESSION['userId']."|".$update[$x][3]."|".$update[$x][0]."|".$update[$x][1]."|".$message."|".$update[$x][0]."\n";
   write_log($version_auswahl, $data);
     }
//echo "</p>";
}

////////////////////////////////////////////////////////////////////////////
//accessible to anyone

  //header, please no output before this (sends header information)
  //establishes the connection to the db, include dblib
  $title = 'Setcompare';
  require_once ("tpl_script/header.php");

    // ----- Create the template object
    $v_template = new PclTemplate();
    // ----- Parse the template file   
    $v_template->parseFile('tpl/setcompare.htm');  
    // ----- Prepare data
    $v_att = array();

    //prints page title
    $v_att['page_title'] = $page_titel[$title];

  $language        = select_box_read_language();
  $version_auswahl = select_box_read_version();
  $obj_auswahl     = select_box_read_obj($version_auswahl);
  $obj_sub_auswahl = select_box_read_sub_obj($version_auswahl,$obj_auswahl);

  select_box_all($version_auswahl,$obj_auswahl,$obj_sub_auswahl,'no'); // no list objekts button
  $v_att['lang_form_9'] = $LNG_FORM[9];
  select_box("select_language",$language_all,$language,'',-1,$LNG_FORM[43]);

  $v_att['button_submit'] = $LNG_FORM[45]; 


 
    ////////////////////////////////////////////////////////////////////////////
   $update = array();
   $x = 0;
   while(list($key,$value) = each($_POST))
   { if (substr($key,0,7)=="object_")
     {
       $obje=substr($key,7);
       $obj=explode("_", $obje);
       $update[$x][0] = $obj[0];
       $update[$x][1] = $obj[1];
       $update[$x][2] = $_POST['object_'.$obje];
       $update[$x][3] = $obj[2];
       $x++;
     }
   } 

$submit_sug = 0;   
if ( isset($_POST['txtaccept']) ) {
   update_text($update);
   $submit_sug = 1;   
} 

if ((isset($_POST['txt_s']) and $_POST['txt_s'] == $LNG_FORM[45]) || $submit_sug == 1 ) {

  if ($language == 255 or $version_auswahl == 255)
  {
         // not language selected 
         $v_att['value_search_table']['error_msg']['message_t'] = $LNG_LOAD2[4];

  } else
  {
  // Objekte suchen
   // search string
      $searchtable = '`objects` o'; 
      $sort = 'o.version_version_id, o.obj, o.obj_name';
   // Versionsauswahl   
      $awv = " o.version_version_id=".$version_auswahl; 
   // Objektauswahl    
      if ( $obj_auswahl != 255 ) { 
         $awv .= " AND o.obj='".$obj_auswahl."'"; 
         $t = " - ".$obj_auswahl;
      } else $t ='';

      $awl = " AND t.language_language_id='".$language."' AND (t.tr_text='' OR (t.tr_text IS NULL)) "; 
      $searchtable .= " JOIN `translations_".$version_auswahl."` t ON (o.object_id=t.object_object_id)"; 
          
      $translations_text = 0;
   // Objekt sub auswahl 
      $awsub = subobject_querry($searchtable,$obj_auswahl,$obj_sub_auswahl); 
 

      $sql = "SELECT * FROM ".$searchtable." WHERE ".$awv.$awl.$awsub." ORDER BY ".$sort." ASC;";

      //echo '<p>'.$sql.'</p>';
      $v_att['value_search_table']['bez_set'] = $LNG_EDIT[1];
      $v_att['value_search_table']['value_set'] = $versions_all[$version_auswahl].$t;

      $search = db_query($sql); 
       
      if ( db_num_rows($search) == 0 ) {   
         $v_att['value_search_table']['error_msg']['message_t'] = $LNG_EDIT[22];
      } 
       
   $line_number = 0;
    
   if ( $search ) 
   {  while ($sr=db_fetch_array($search)) {

         $style = (($line_number % 2) == 0)?"bg_trans":"bg_grey";

         $other_text_array = other_translate($sr['object_id'], $sr['obj_name'], $sr['obj'], $sr['language_language_id']); 
         
         $other_text = '0';
         for ($x = 0; $x < count($other_text_array); $x++ ) {
              $r = explode("|", $other_text_array[$x]);
             
              if ( in_array($sr['language_language_id'] ,$_SESSION['edit_lang']) ) {
                 $v_att['value_search_table']['objects_lines'][$line_number]['checkbox']['obj_id'] = $sr['object_id'];
                $v_att['value_search_table']['objects_lines'][$line_number]['checkbox']['object'] = $sr['obj'];
                $v_att['value_search_table']['objects_lines'][$line_number]['checkbox']['text_lng'] = $sr['language_language_id'];
                $v_att['value_search_table']['objects_lines'][$line_number]['checkbox']['value'] = htmlentities($r[2], ENT_QUOTES, "UTF-8");
             } else {
                $checkbox = ''; //<input type="radio" value="text" name="object">wert
             }
 
            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['style'] = $style;
            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['obj_id'] = $r[1];
            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['set_id'] = $r[0];
            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['object'] = $sr['obj'];
            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['text_lang'] = $sr['language_language_id'];
            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['set_name'] = $versions_all[$r[0]];

            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['checkbox'] = $r[1];  
            $v_att['value_search_table']['objects_lines'][$line_number]['set_lines'][$x]['obj_text'] = htmlentities($r[2], ENT_QUOTES, "UTF-8");
            
            $other_text = '1';
         }
         
       if ( $other_text == '1' ) { 
 
          $v_att['value_search_table']['objects_lines'][$line_number]['style'] = $style;
          $v_att['value_search_table']['objects_lines'][$line_number]['obj_id'] = $sr['object_id'];
          $v_att['value_search_table']['objects_lines'][$line_number]['text_lang'] = $sr['language_language_id'];       
          $v_att['value_search_table']['objects_lines'][$line_number]['version_id'] = $sr['version_version_id'];       
          $v_att['value_search_table']['objects_lines'][$line_number]['object'] = $sr['obj'];       
          $v_att['value_search_table']['objects_lines'][$line_number]['obj_name'] = $sr['obj_name'];       
          $v_att['value_search_table']['objects_lines'][$line_number]['browser_lang'] = $st;       

            $line_number++;
       }
            
     }
       db_free_result($search);
        //echo "</table>";

           if ( in_array($version_auswahl, $_SESSION['set_enabled']) && $line_number != 0 ) {
              $v_att['value_search_table']['value_accept_button']['bez_accept'] = $LNG_FORM[50];
           }
           
           if ( $line_number == 0 ) {
              $v_att['value_search_table']['error_msg']['message_t'] = $LNG_EDIT[22];
           }
    }
  }
}

  $v_result = $v_template->generate($v_att, 'string');
  echo $v_result;
  unset($v_att);     

    ////////////////////////////////////////////////////////////////////////////

    //footer, nothing ater this (closes the page)
   include_once ("tpl_script/footer.php");
?>
