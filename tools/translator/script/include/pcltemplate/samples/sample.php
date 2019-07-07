<?php
// -----------------------------------------------------------------------------
// PhpConcept Library Template - sample.php
// -----------------------------------------------------------------------------
// Copyright - Vincent Blavet - November 2006
// http://www.phpconcept.net
// -----------------------------------------------------------------------------
// Overview :
//   See http://www.phpconcept.net for more information
// -----------------------------------------------------------------------------
// CVS : $Id$
// -----------------------------------------------------------------------------

  //require_once('../pcltrace.lib.php');
  //require_once('../pcltemplate-trace.class.php');
  require_once('../pcltemplate.class.php');
  
  //PclTraceOn(5);

  // ----- Create the template object
  $v_template = new PclTemplate();
  
  // ----- Parse the template file
  if ($v_template->parseFile('model.htm') != PCL_TEMPLATE_ERR_NO_ERROR) {
    echo "Error parsing file :<br>";
    echo nl2br($v_template->errorInfo());
    exit;
  }
  else {
    echo "No error parsing file :<br>";
    echo nl2br($v_template->errorInfo());
  }
 
  // ----- Prepare data
  $v_att = array();
  
  // Simple tokens
  $v_att['last_name'] = 'Blavet';
  $v_att['first_name'] = 'Vincent';
  
  // Array
  $v_att['my_list'] = array();
  $i=0;
  $v_att['my_list'][$i]['tok1'] = 'hello '.$i.' A';
  $v_att['my_list'][$i]['tok2'] = 'hello '.$i.' B';
  $i++;
  $v_att['my_list'][$i]['tok1'] = 'hello '.$i.' A';
  $v_att['my_list'][$i]['tok2'] = 'hello '.$i.' B';
  $i++;
  $v_att['my_list'][$i]['tok1'] = 'hello '.$i.' A';
  $v_att['my_list'][$i]['tok2'] = 'hello '.$i.' B';
  
  // Condition bloc with token inside
  $v_att['condition_1']['inside_condition'] = 'token inside condition';

  // Simple condition token 
  $v_att['condition_3'] = 'simple condition';

  // Include token
  $v_att['footer']['filename'] = 'model-footer.htm';
//  $v_att['footer']['values']['copyright'] = 'Copyright PhpConcept';
//  $v_att['footer']['values']['year'] = '2007';
   
  // ----- Generate result
  $v_result = $v_template->generate($v_att, 'string');
  if ($v_result === 0) {
    die("Error generating file :<br>".nl2br($v_template->errorInfo()));
  }
  
  // ----- Display result
  echo $v_result;
  
  echo "<br><br><br>Error report :<br>";
  echo nl2br($v_template->errorInfo());
  

  //PclTraceDisplayNew();
?>
