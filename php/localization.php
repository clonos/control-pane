<?php

class Localization
{
	private $language='en';
	private $translate_arr=array();
	private $realpath='';

	function __construct($realpath_public)
	{
#$_COOKIE['lang']='ru';
		$this->realpath=$realpath_public;
		(isset($_COOKIE['lang'])) AND $this->language=$_COOKIE['lang'];
		(!array_key_exists($this->language, Config::$languages)) AND $this->language='en';
		include($realpath_public.'/lang/'.$this->language.'.php');
		$this->translate_arr=$lang;
		
		/*
		$n=1;
		foreach($lang as $eng=>$rus)
		{
			echo $n,' — ',$eng,"<br>";
			$n++;
		}
		exit;
		*/
		
		# Если нужно наполнить базу, то нужно убрать комментарий и запустить страницу
		# с параметром /?go=go
		/*
		if($_GET['go']=='go')
		{
			#var_dump($lang);
			#exit;
			
			$db=new Db('clonos');
			if(!$db->isConnected())
			{
				print_r(['error'=>true,'error_message'=>'db connection lost!']);
				exit;
			}

			foreach($lang as $eng=>$rus)
			{
				$dbres=$db->insert("insert into lang_en (text) values (?)",[[$eng, PDO::PARAM_STR]]);
				if($dbres['error'])
				{
					print_r(['error'=>true,'error_message'=>$dbres['info']]);
					exit;
				}
				
				$new_id=$dbres['lastID'];
				$dbres=$db->insert("insert into lang_other (en_id,text,lang) values (?,?,?)",[[$new_id],[$rus],['ru']]);
				if($dbres['error'])
				{
					print_r(['error'=>true,'error_message'=>$dbres['info']]);
					exit;
				}
				
			}
		}
		echo 'all ok';
		exit;
		*/
	}

	public function get_lang()
	{
		return $this->language;
	}
	
	public function get_path()
	{
		return $this->realpath;
	}

	public function translate($phrase)
	{
		return (isset($this->translate_arr[$phrase])) ? $this->translate_arr[$phrase] : $phrase;
	}
}


# для отключения ссылок в меню, чтобы можно было его перевести:
# $('ul.menu').on('click',function(event){return false;});
# доделать со временем
class Translate
{
	private $locale='';
	private $language='';
	private $realpath='';
	
	private $translated_file='';
	/*
	private $page_file_name='';
	private $dialog_file_name='';
	private $json_file_name='';
	*/
	
	function __construct($locale)
	{
		
		$this->locale=$locale;
		$this->language=$this->locale->get_lang();
		$this->realpath=$locale->get_path();
	}
	
	public function translate($path,$page,$file_name)
	{
		$translate_cache='_translate.cache';
		$backup_dir='back';
		switch($path)
		{
			case 'pages':
				$full_path=$this->realpath.$path.DIRECTORY_SEPARATOR.$page.DIRECTORY_SEPARATOR;
				$translate_cache_path=$full_path.$translate_cache.DIRECTORY_SEPARATOR;
				$backup_path=$full_path.$backup_dir.DIRECTORY_SEPARATOR;
				$this->translated_file=$translate_cache_path.$this->language.'.index.php';
				//echo $this->translated_file;exit;
				break;
			case 'dialogs':
				$full_path=$this->realpath.$path.DIRECTORY_SEPARATOR;
				$translate_cache_path=$full_path.$translate_cache.DIRECTORY_SEPARATOR;
				$backup_path=$full_path.$backup_dir.DIRECTORY_SEPARATOR;
				$this->translated_file=$translate_cache_path.$this->language.'.'.$file_name;
				break;
			default:
				$path='system';
		}
		
		if(!is_dir($full_path)) return;
		
		if(!is_dir($translate_cache_path))
		{
			mkdir($translate_cache_path);
			$ttxt="This files is cache of translated pages, do not translate it!\n".
				"If you are a translator, please read the documentation on the translation on the website.\n".
				"Thank you.";
			file_put_contents($translate_cache_path.'!dont.touch.files',$ttxt);
		}
		
		$file=$full_path.$file_name;

		$mtime_translate=0;
		$mtime_orig=filemtime($file);
		if(file_exists($this->translated_file))
			$mtime_translate=filemtime($this->translated_file);
		# $mtime_db_update — дата последней модификации перевода в БД
		
		if($mtime_orig<$mtime_translate)
		{
			return ['message'=>'translate from cache'];
		}
		
		# при сохранении в БД перевода на язык пользователя, нужно удалять файл кэша
		# или во временный файл сохранять даты обновления разных языков, а уже там проверять дату

		$db=new Db('clonos');
		if(!$db->isConnected())	return array('error'=>true,'error_message'=>'db connection lost!');

		if(file_exists($file))
		{
			$is_changed=false;
			$txt=file_get_contents($file);
			preg_match_all('#<translate([^>]*)>(.*)</translate>#U',$txt,$res,PREG_SET_ORDER);
			//echo '<pre>';var_dump($res);exit;
			
			$ids_arr=[];
			
			foreach($res as $item)
			{
				$id=-1;
				//$text='';
				$update=false;
				$params=[];
				$tag=$item[0];
				$attrs=$item[1];
				$text=$item[2];
				
				if($attrs!='')
				{
					// если у тэга есть ID, то проверяем текст и формируем шаблон
					preg_match_all('#((id)="?([\d]+)"?|update)#',$attrs,$params,PREG_SET_ORDER);

					if(is_array($params))	// && $this->language!='en'
					{
						// ИЗМЕНИТЬ КОММЕНТАРИЙ! --- если у пользователя язык интерфейса не английский, то переводим
						// обрабатываем оригинальный текст и создаём шаблоны разных языков
						foreach($params as $p)
						{
							if(isset($p[2]) && $p[2]=='id')
							{
								if(is_numeric($p[3]))
								{
									$id=$p[3];
									$dbres=$db->selectOne("select text from lang_en where id=? and type in (?,?)",[
										[$id,PDO::PARAM_INT],
										[$path,PDO::PARAM_STR],
										['system',PDO::PARAM_STR]
									]);
									//var_dump($dbres);
									if(!empty($dbres))
									{
										if($text!=$dbres['text'])
										{
											// если оригинальный текст изменился, то обновляем его в базе
											# временно отключил, пока наполняется основная база. Потом нужно вернуть обратно
											/*
											$dbres1=$db->update('update lang_en set text=? where id=? and type=?',[[$text,PDO::PARAM_STR],[$id,PDO::PARAM_INT],[$path,PDO::PARAM_STR]]);
											if(isset($dbres1['rowCount']))
											{
												if($dbres1['rowCount']>0)
												{
													$is_changed=true;
												}
											}
											*/
										}
										
									}
									$ids_arr[]=$id;
								}
							}
							//print_r($ids_arr);exit;
/*							
							if($p[0]=='update' && $id>0)
							{
								echo "\tupdate id: ",$id,"\n";
							}
*/
						}
						
					}else{
						
					}
					//print_r($params);
				}else{
					

/*
delete FROM "lang_en";
VACUUM;
UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = 'lang_en'
*/
					$dbres=$db->selectOne("select id from lang_en where text=? and type in (?,?)",[
						[$text,PDO::PARAM_STR],
						[$path,PDO::PARAM_STR],
						['system',PDO::PARAM_STR]
					]);
					if(isset($dbres['error']) && $dbres['error'])
					{
						echo 'error db: ',$dbres['info'];
						exit;
					}
					//echo $text;
					//var_dump($dbres);
					//echo '<br>';
					
					if(is_numeric($dbres['id']))
					{
						// если фраза есть в базе, то вписываем её ID в тэг
						$new_text='<translate id="'.$dbres['id'].'">'.$text."</translate>";
						$txt=str_replace($tag,$new_text,$txt);
						$is_changed=true;
					}else{
						if($dbres===false)
						{
							// если фразы нет в базе, то добавляем её туда и вписываем новый ID в тэг
							$dbres=$db->insert("insert into lang_en (text,type) values (?,?)",[[$text, PDO::PARAM_STR],[$path, PDO::PARAM_STR]]);
							
							if($dbres['error'])
								return array('error'=>true,'error_message'=>$dbres['info']);
							
							$new_text='<translate id="'.$dbres['lastID'].'">'.$text."</translate>";
							$txt=str_replace($tag,$new_text,$txt);
							$is_changed=true;
							//echo $txt;
							$ids_arr[]=$dbres['lastID'];
						}
					}
					
				}
			}
			
			// бэкапим предыдущий файл и сохраняем видоизменённый
			if($is_changed)
			{
				if(!is_dir($backup_path))
				{
					mkdir($backup_path);
				}

				//rename($full_path.$file_name,$full_path.
				//	$backup_dir.DIRECTORY_SEPARATOR.time().'.'.$file_name);
				rename($full_path.$file_name,$backup_path.time().'.'.$file_name);
				file_put_contents($full_path.$file_name,$txt);
				#echo 'save';
				#echo $txt;
				#exit;
			}
			
			// переводим на другие языки
			$ids_txt=join(',',$ids_arr);
			$sql="select en_id,text from lang_other where lang=? and en_id in ({$ids_txt})";
			$res=$db->select($sql,[[$this->language,PDO::PARAM_STR]]);
//			$res=$db->select("select en_id,text from lang_other where lang=? and en_id in (?)",[[$this->language,PDO::PARAM_STR],[[$ids_arr]]]);
			if($res['error'])
			{
				echo 'db error';
				exit;
			}
			
			foreach($res as $item)
			{
				$pat='#<translate id="'.$item['en_id'].'"[^>]*>(.*)</translate>#U';
				$txtChg='<span id="trlt-'.$item['en_id'].'">'.$item['text'].'</span>';
				$txt=preg_replace($pat,$txtChg,$txt);	//'<span id="trlt-'.$item['en_id'].'">'.	//.'</span>'
			}
			
			//$txt=preg_replace('#(<translate([^>]*)>|</translate>)#','',$txt);
			$txt=preg_replace('#<translate id="([\d]+)">#U','<span id="trlt-$1">',$txt);
			$txt=str_replace('</translate>','</span>',$txt);
			
			$txt=preg_replace("#title='<span.+>([^\']+)</span>'#U","$1",$txt);
			
			$txt=preg_replace("#(<option[^>]+>)<span[^>]+>(.*)</span>#U","$1$2",$txt);
			
			# чистим кнопки от лишних тэгов
			$txt=preg_replace('#<input type="button" value="(<span[^>]+)>(.*?)</span>"#U','<input type="button" value="$2"',$txt);
			//echo $txt;
			//exit;
			
			//$this->tanslated_file=$translate_cache_path.$this->language.'.index.php';
			file_put_contents($this->translated_file,$txt);
			#echo $txt;
			#exit;

			//var_dump($res);
			//echo $ids_txt;
			
			
			
			
			return ['message'=>'translated to: '.$this->language];
		}else{
			return ['error'=>true,'message'=>'no file...'];
		}

		/*
			<translate([^>]*)>(.*)</translate>
		*/
	}
	
	public function get_translated_filename()
	{
		return $this->translated_file;
	}
}