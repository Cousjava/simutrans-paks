<?php
require_once('../pcltemplate.class.php');
  
  // ----- Create the template object
  $v_template = new PclTemplate();
  
  // ----- Prepare a template string
  $v_str = '
    <document name="--[doc_name]--" size="--[doc_size]--">
      <city name="--[city]--"/>
    </document>
  ';
  
  // ----- Change the delimiters
  $v_template->changeDelimiters('--[',']--');

  // ----- Parse the template file
  $v_template->parseString($v_str);
 
  // ----- Prepare data
  $v_att = array();
  $v_att['doc_name'] = 'document.txt';
  $v_att['doc_size'] = '12';
  $v_att['city'] = 'Paris';
  
  // ----- Generate result
  $v_result = $v_template->generate($v_att, 'string');

  // ----- Display result 
  echo '<pre>';
  echo htmlentities($v_result);
  echo '</pre>';
?>
