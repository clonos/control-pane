<h1>ClonOS Settings</h1>
<?php

$form=new Forms('cbsd-settings');
$res=$form->generate();

echo $res['html'];