<?php
require_once('../pcltemplate.class.php');
  
// ----- Create the template object
$v_template = new PclTemplate();

// ----- Parse the template file
$v_template->parseFile('model-list.htm');

// ----- Prepare data
$v_att = array();

// ----- Set the values of the simple tokens
$v_att['page_name'] = 'First Generated Page';
$v_att['user_name'] = 'Vincent Blavet';

// ----- Set the values of the list tokens
$v_att['users'][0]['first_name'] = 'Vincent';
$v_att['users'][0]['last_name'] = 'Blavet';
$v_att['users'][1]['first_name'] = 'Pierre';
$v_att['users'][1]['last_name'] = 'Martin';

// ----- Generate result in a string
$v_result = $v_template->generate($v_att, 'string');

// ----- Display result
echo $v_result;
?>
