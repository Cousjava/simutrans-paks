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

  require_once('../pcltrace.lib.php');
  require_once('../pcltemplate-trace.class.php');
  //require_once('../pcltemplate.class.php');
  
  //PclTraceOn(5);

// ----- Create the template object
$v_template = new PclTemplate();

// ----- Parse the template file
if ($v_template->parseFile('model-inclusion.htm') != PCL_TEMPLATE_ERR_NO_ERROR) {
  echo "Error parsing file :<br>";
  echo nl2br($v_template->errorInfo());
  exit;
}

// ----- Prepare data
$v_att = array();

// ----- Set the values of the simple tokens
$v_att['page_name'] = 'Sample Inclusion';
$v_att['last_name'] = 'Blavet';
$v_att['first_name'] = 'Vincent';
  
// ----- Include token
// The token is not a single value but an array with :
// - The filename to include ('filename').
// - The values of the tokens for the included file ('values').
$v_att['footer']['filename'] = 'model-footer.htm';
$v_att['footer']['values']['copyright'] = 'Copyright PhpConcept';
$v_att['footer']['values']['author'] = 'vblavet';

// ----- Generate result in a string
$v_result = $v_template->generate($v_att, 'string');

// ----- Display result
echo $v_result;

  //PclTraceDisplayNew();
?>
