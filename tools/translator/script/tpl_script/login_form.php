<?php  $msg = "";  if ( isset($_GET['msg']) )  { $t = $_GET['msg'];    if (strpos($t,'login_ok')  !== false) $msg = $LNG_LOGIN[8];    if (strpos($t,'notfound')  !== false) $msg = $LNG_LOGIN[9];    if (strpos($t,'not_aktiv') !== false) $msg = $LNG_LOGIN[9]."<br>".$LNG_LOGIN[13];    if (strpos($t,'_pass')     !== false) $msg = $LNG_LOGIN[10];  }  // ----- Create the template object  $v_template = new PclTemplate();  // ----- Parse the template file  $v_template->parseFile('tpl/login_form.htm');  // ----- Prepare data  $v_att = array();  $v_att['page_title'] =  $page_titel[$title];  if ($msg != '')   $v_att['message'] = $msg;  $v_att['titel_user'] = $LNG_LOGIN[15];  $v_att['titel_pass'] = $LNG_LOGIN[16];  $v_att['submit_button'] = $LNG_LOGIN[17];  $v_att['login_0'] = $LNG_LOGIN[0];  $v_att['login_1'] = $LNG_LOGIN[1];  $v_att['login_2'] = $LNG_LOGIN[2];  $v_result = $v_template->generate($v_att, 'string');  echo $v_result;         $message[0] = $LNG_LOGIN[3]." <a href='main.php?lang=de&page=contact'>".$LANG_CONTACT[6]."</a> ".$LNG_LOGIN[4];  $message[1] = $LNG_LOGIN[5]." <a href='main.php'>".$LNG_LOGIN[6]."</a> ".$LNG_LOGIN[7];  info_box ($message[0], $message[1], '', '', '', 'width600');   ?> 