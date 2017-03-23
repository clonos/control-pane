<h1>Настройки CBSD</h1>
<?php

$form=new Forms('cbsd-settings');
$res=$form->generate();

echo $res['html'];