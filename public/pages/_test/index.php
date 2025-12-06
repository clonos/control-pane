<?php

trait One {
	public function test1()
	{
		echo $this->var.' 1';
	}
	public function test2()
	{
		echo $this->var.' 2';
	}
}
trait Two {
	public function test3()
	{
		echo $this->var.' 3';
	}
}

class ClonOS
{
	use One, Two;
	
	public $menu=null;
	public $var='var';
	
	function start()
	{
		$this->var='hello';
		echo 'start'.'<br>';
		
		$this->menu=new Menu();
		echo '<br>'.$this->var.'<br>';
		
		echo $this->menu->getVar().'<br>';
	}
	
	function test3()
	{
		echo 'hi';
	}
}

class Menu extends ClonOS
{
	function start()
	{
		$this->var='var1';
		echo 'Menu'.'<br>';
	}
	function getVar()
	{
		return $this->var;
	}
}


$clonos=new ClonOS();
/*
//echo $clonos->var.'<br>';
$clonos->start();
//echo $clonos->var.'<br>';
$clonos->menu->start();
echo $clonos->menu->getVar();
*/

/*
$m=new Menu();
echo '<pre>';
$m->start();
echo $m->getVar();

echo "\n\n";
var_dump($m);
*/

echo $clonos->test3();