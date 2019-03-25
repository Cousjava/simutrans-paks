<?php
require_once('../pcltrace.lib.php');
PcltraceOn(5);
require_once('../pcltemplate-trace.class.php');
  
// ----- Create the template object
$v_template = new PclTemplate();

// ----- Parse the template file
$v_template->parseFile('model-condition.htm');

// ----- Prepare data
$v_att = array();

// ----- Set the values of the simple tokens
$v_att['page_name'] = 'First Generated Page';
$v_att['user_name'] = 'Vincent Blavet';

// ----- Set the values of the simple token condition
$v_att['web_site'] = 'www.phpconcept.net';

// ----- Set the values of the bloc condition
$v_att['email_block']['email'] = 'vincent@phpconcept.net';
$v_att['email_block']['alternate'] = 'support@phpconcept.net';

// ----- Generate result in a string
$v_result = $v_template->generate($v_att, 'string');

// ----- Display result
echo $v_result;
PclTraceDisplayNew();
?>
