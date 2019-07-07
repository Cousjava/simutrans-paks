<?PHP
  require_once('./include/parameter.php');

  
  echo "<h2> <a href='https://forum.simutrans.com/index.php/board,47.0.html'>Forum Translator</a></h2>";
  echo "<h2> <a href='https://forum.simutrans.com/'>International Simutrans Forum</a></h2>";
  echo "<h2> <a href='https://www.simutrans-forum.de'>Deutsches Simutrans Forum</a></h2>";
  echo "<br><br>";
  echo "<a href='mailto:translator-contact@makie.de'>Mail an den Seitenbetreiber senden</a><br>";

  if (!isset($servermail)) die('$servermail nicht gesetzt');
  
  $daten = array();
  $daten['translatoradress'] = $servermail;

  // Manage Language Message
  if ( $_GET['lm'] == '1' ) {
    $lm_lang = $_GET['mlang'];
    $lm_set = $_GET['mset'];
  } elseif ( $_POST['lm'] == '1' ) {
    $lm_lang = $_POST['mlang'];
    $lm_set = $_POST['mset'];
  }

  // ----- Create the template object
  $v_template = new PclTemplate();

  // ----- Parse the template file
  $v_template->parseFile('tpl/contact.htm');
  // ----- Prepare data
  $v_att = array();

  $v_att['lang'] = $st;
  
  if ( $_GET['lm'] OR $_POST['lm'] ) {
    $v_att['page_title'] = $page_titel[$title].' '.$LNG_LANGUAGE[$lm_lang].' Set '.$versions_all[$lm_set];
    $v_att['lm']['value_lm_lang'] = $lm_lang;
    $v_att['lm']['value_lm_set'] = $lm_set;
  } else {
    $v_att['page_title'] = $page_titel[$title];
  }
  

  if ( $_POST['send'] ) {
    $send_ok = 0;
    if ( empty($_POST['Absender_name']) ) {
      $err_message = $LANG_CONTACT[7];
    } elseif ( empty($_POST['Absender_mail']) ) {
      $err_message = $LANG_CONTACT[8];
    } elseif ( empty($_POST['Absender_domain']) ) {
      $err_message = $LANG_CONTACT[9];
    } elseif ( empty($_POST['textarea']) ) {
      $err_message = $LANG_CONTACT[10];
    } elseif ( $_POST['textarea'] ) {

      $daten['useradress'] = $_POST['Absender_mail'].'@'.$_POST['Absender_domain'];
      $daten['userdomain'] = $_POST['Absender_domain'];
      $daten['usermailname'] = $_POST['Absender_mail'];
      $daten['username'] = $_POST['Absender_name'];
      $daten['message'] = $_POST['textarea'];

      unset($_POST);
      
      if ( $_POST['lm'] == '1' ) {
        // send to language manager
        $sql = "SELECT * FROM `lang_maintaint` WHERE `lang_id`='".$lm_lang."' AND `set_id`=".$lm_set.";";
        $query = db_query($sql);
        $row = db_fetch_array($query);
        if ( !is_null($row['data']) ) {  
          $mail_adress = db_one_field_query ("SELECT `email` FROM `users` WHERE `u_user_id`='".$row['data']."'");        
          send_mail($mail_adress, 0);
        }
        if ( !is_null($row['data1']) ) {  
          $mail_adress = db_one_field_query ("SELECT `email` FROM `users` WHERE `u_user_id`='".$row['data1']."'");        
          send_mail($mail_adress, 0);
        }
        if ( !is_null($row['data2']) ) { 
          $mail_adress = db_one_field_query ("SELECT `email` FROM `users` WHERE `u_user_id`='".$row['data2']."'");        
          send_mail($mail_adress, 0);
       }
      
      } else {
        for ( $x = 0; $x < count($contact_adr); $x++ ) {
          // send to admins
          send_mail($contact_adr[$x], 0);
        }
      }
      
      // send reponse to user
      send_mail($daten['translatoradress'], 1);

      $send_ok = 1;
    }

  } 

function send_mail($mailadr, $reponse) {

  global $daten, $LANG_CONTACT;

  //Betreff in der Mail  
  $email_betreff = 'Message from SimuTranslator';  

  //$textarea = mb_ereg_replace ('\n', '<br>', $daten['message'], "p");
  $textarea = $daten['message'];

  if ( $reponse == 1 ) {
    // Empfaenger-Email
    $email_to = $daten['useradress'];  
    // Absender-Email
    $email_from_mail = 'no replay <'.$mailadr.'>';   
    
    //send text: this is a copy, do not reply, the text you put in:
    $response_note = $LANG_CONTACT[12].'<br>';
    $response_note .= $LANG_CONTACT[11].'<br><br>';

    $emailbody = $response_note.' '.$textarea;

  } else {
    // Empfaenger-Email
    $email_to = $mailadr;  
    //Absender-Email
    $email_from_mail = $daten['useradress'];   
    //Absender-Name
    $email_from_name = $daten['username'];  
    
    $emailbody = '
      Name = '.$email_from_name.'
      Email = '.$email_from_mail.'  
      '.$textarea;

  }
  

  $header='From:'.$email_from_mail."\r\n";
  $header .= 'Reply-To: '.$email_from_mail."\r\n"; 
  $header .= 'Bcc: '."\n"; 
  $header .= "MIME-Version: 1.0\r\n";          
  $header .= 'X-Mailer: PHP/' . phpversion(). "\r\n";          
  //$header .= "X-Sender-IP: $REMOTE_ADDR\n"; 
  $header .= 'Content-Type: text/plain; charset=utf-8'; 
  mail($email_to,$email_betreff,$emailbody,$header);
        
}

  $v_att['bez_sender_name'] = $LANG_CONTACT[1];
  $v_att['bez_sender_mail'] = $LANG_CONTACT[2];
  $v_att['button_send'] = $LANG_CONTACT[3];
  $v_att['button_reset'] = $LANG_CONTACT[4];

  $v_att['sender_name'] = '';
  $v_att['sender_adress'] = '';
  $v_att['sender_domain'] = '';
  $v_att['textarea'] = '';
  
  if ( $send_ok == 0 ) {
      $v_att['err_message'] = $err_message;
  
      $v_att['sender_name'] = $daten['username'];
      $v_att['sender_adress'] = $daten['usermailname'];
      $v_att['sender_domain'] = $daten['userdomain'];
      $v_att['textarea'] = $daten['message'];
  } elseif ( $send_ok == 1 ) {
      $v_att['submit_ok'] =  $LANG_CONTACT[5];

  } 

  // info text 
  $v_att['info_box']['filename'] = './tpl/info_box.htm';
  $v_att['info_box']['values']['message_0'] = $LANG_CONTACT[11];

  // ----- Generate result in a string
  $v_result = $v_template->generate($v_att, 'string');

  echo $v_result;

  
  
  /* alte contact.php aus /script
  	$title=$LANG_CONTACT[0]." ".$LANG_CONTACT[6];

function send_mail_to_admins($mailadr) {

	GLOBAL $LANG_CONTACTS;

	$email_to = $mailadr;  

//Absender-Email  
	$email_from_mail = $_POST['Absender_mail']."@".$_POST['Absender_domain'];
	$email_from_mail = filter_var($email_from_mail, FILTER_SANITIZE_EMAIL);
   
//Absender-Name
	$email_from_name = $_POST['Absender_name'];   
//Betreff in der Mail  
	$email_betreff = "Message from SimuTranslator";  

 	$textarea = mb_ereg_replace ('\n', '<br>', $_POST['textarea'], "p");

	$emailbody = "
	Name = $email_from_name<br>
	Email = $email_from_mail<br>  
	<br><br>
	$textarea";

	$header="From:$email_from_name<$email_from_mail>\n";
	$header .= "Reply-To: $email_from_mail\n"; 
	$header .= "Bcc: $email_to_bcc\n"; 
	$header .= "X-Mailer: PHP/" . phpversion(). "\n";          
//$header .= "X-Sender-IP: $REMOTE_ADDR\n"; 
	$header .= "Content-Type: text/html"; 
	mail($email_to,$email_betreff,$emailbody,$header);
        
	echo $LANG_CONTACT[5];

}


    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    //page start
if ( $_POST['send'] && !empty($_POST['Absender_name']) && !empty($_POST['Absender_mail']) && !empty($_POST['Absender_domain']) && !empty($_POST['textarea']) ) {
	for ( $x = 0; count($contact_adr); $x++ ) {
		send_mail_to_admins($contact_adr[$x]);
	}
}

if ( $_POST['send'] && empty($_POST['Absender_name']) ) {
	echo '<p><font color="Red">'.$LANG_CONTACT[7].'</font></p>';
}
if ( $_POST['send'] && empty($_POST['Absender_mail']) ) {
	echo '<p><font color="Red">'.$LANG_CONTACT[8].'</font></p>';
}
if ( $_POST['send'] && empty($_POST['Absender_domain']) ) {
	echo '<p><font color="Red">'.$LANG_CONTACT[9].'</font></p>';
}
if ( $_POST['send'] && empty($_POST['textarea']) ) {
	echo '<p><font color="Red">'.$LANG_CONTACT[10].'</font></p>';
}

  info_box ( $LANG_CONTACT[11] );

*/

?>
