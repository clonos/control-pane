<?php

trait tcHelpers {
	
	function addHelperGroup($mode)
	{
		$module=$this->url_hash;
		if(isset($this->form)){
			$form=$this->form;
		} else { 
			$form=array();
		}
		if(isset($form['db_path']) && !empty($form['db_path']))	{
			$db_path=$form['db_path'];
			if(!file_exists($db_path)){
				$res=CBSD::run('make_tmp_helper module=%s', array($module));
				if($res['retval']==0){
					$db_path=$res['message'];
				} else {
					return array('error'=>true,'errorMessage'=>'Error on open temporary form file!');
				}
			}
		}else{
			$res=CBSD::run('make_tmp_helper module=%s', array($module));
			if($res['retval']==0) $db_path=$res['message'];
		}
		CBSD::run('forms inter=0 module=%s formfile=%s group=add', array($module, $db_path));
		$html=(new Forms('',$module,$db_path))->generate();

		return array('db_path'=>$db_path,'html'=>$html);
	}

	function deleteHelperGroup($mode){
		$module=$this->url_hash;
		if(isset($this->form)){
			$form=$this->form;
		} else {
			$form=array();
		}
		if(!isset($form['db_path']) || empty($form['db_path'])) return;

		if(!file_exists($form['db_path'])) return array('error'=>true,'errorMessage'=>'Error on open temporary form file!');

		$index=$form['index'];
		$index=str_replace('ind-','',$index);

		$db_path=$form['db_path'];
		$res=CBSD::run(
			'forms inter=0 module=%s formfile=%s group=del index=%s',
			array($module, $db_path, $index)
		);
		$html=(new Forms('',$module,$db_path))->generate();

		return array('db_path'=>$db_path,'html'=>$html);
	}

}