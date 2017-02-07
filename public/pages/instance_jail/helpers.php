<?php

$hash=preg_replace('/^#/','',$this->_vars['hash']);

$form=new Forms('php','local');
$res=$form->generate();

$html=$res['html'];





echo json_encode(array('html'=>$html,'func'=>'fillTab'));