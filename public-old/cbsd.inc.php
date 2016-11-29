<?php
define('CBSD_CMD','env NOCOLOR=1 /usr/local/bin/sudo /usr/local/bin/cbsd ');

$workdir=getenv('WORKDIR');
$document_root=getenv('DOCUMENT_ROOT');
$server_name=getenv('SERVER_NAME');
$server_host=getenv('HTTP_HOST');

$url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$url_path=parse_url($url, PHP_URL_PATH);

$client_ip=getIP();

if (empty($workdir)) $workdir="/usr/jails";

$rp=realpath('');
$tplfile=$rp.'/jailtpl.jconf';
$vmtplfile=$rp.'/vmtpl.jconf';

if (!isset($workdir)) {
	echo "No such workdir. Please set in nginx: fastcgi_param WORKDIR /path/to/cbsdworkdir;";
	exit(0);
}

function getIP()
{
	//if($_SERVER['HTTP_CLIENT_IP']) { $ip_address=$_SERVER['HTTP_CLIENT_IP']; }
//	elseif($_SERVER['http_x_forwarded_for']) { $ip_address=$_SERVER['http_x_forwarded_for']; }
//	else { $ip_address=$_SERVER['remote_addr']; }

//	if($ip_address=="unknown")$ip_address="0.0.0.0";
	$ip_address=$_SERVER['REMOTE_ADDR'];
	return($ip_address);
}

function translate($phrase)
{
        global $rp;
        $lang=getLang();
        $file=getLangFilePath($lang);
        if(!file_exists($file)) $file=getLangFilePath('en');;
        if(!file_exists($file)) return;
        require($file);

        if(isset($lang[$phrase]))
                echo $lang[$phrase];
        else
                echo $phrase;
}

function etranslate($phrase)
{
        global $rp;
        $lang=getLang();
        $file=getLangFilePath($lang);
        if(!file_exists($file)) $file=getLangFilePath('en');;
        if(!file_exists($file)) return;
        require($file);

        if(isset($lang[$phrase]))
                return $lang[$phrase];
        else
                return $phrase;
}

function get_translate($phrase)
{
        $lang=getLang();
        $file=getLangFilePath($lang);
        if(!file_exists($file)) $file=getLangFilePath('en');;
        require($file);

        if(isset($lang[$phrase]))
                return $lang[$phrase];
        else
                return $phrase;
}

function getLang()
{
        if(isset($_COOKIE['lang']))
                $lang=$_COOKIE['lang'];
        if(empty($lang)) {
            $lang='en';
        }
        return $lang;
}

function getLangFilePath($lang)
{
        global $rp;
        return $rp.'/lang/'.$lang.'.php';
}

function getJailTemplate($formvalues)
{
//	global $jname, $host_hostname, $astart, $ip4_addr, $workdir;
//	global $mount_devfs, $allow_mount, $allow_devfs, $allow_nullfs;
//	global $mkhostsfile, $devfs_ruleset, $ver, $baserw, $mount_src, $mount_obj, $mount_kernel;
//	global $mount_ports, $astart, $vnet, $applytpl, $mdsize, $floatresolv;
//	global $pkg_bootstrap, $user_pw_root, $interface, $sysrc_enable;
//	global $tplfile, $with_img_helpers, $runasap;
    global $tplfile, $workdir;

//    echo '<pre>',$formvalues['jname'];
//    echo '<pre>',print_r($formvalues, true);

	$file=file_get_contents($tplfile);

	if(!empty($file))
	{
		$file=str_replace('#jname#',$formvalues['jname'],$file);
		$file=str_replace('#host_hostname#',$formvalues['host_hostname'],$file);
		$file=str_replace('#ip4_addr#',$formvalues['ip4_addr'],$file);
		$file=str_replace('#interface#',$formvalues['interface'],$file);
		$file=str_replace('#baserw#',(isset($formvalues['baserw']) ? $formvalues['baserw'] : 0),$file);
		$file=str_replace('#mount_ports#',(isset($formvalues['mount_ports']) ? $formvalues['mount_ports'] : 0),$file);
		$file=str_replace('#astart#',(isset($formvalues['astart']) ? $formvalues['astart'] : 0),$file);
		$file=str_replace('#user_pw_root#',$formvalues['user_pw_root'],$file);
		$file=str_replace('#sysrc_enable#',$formvalues['sysrc_enable'],$file);

		$file=str_replace('#workdir#',$workdir,$file);
		
		$file=str_replace('#mount_devfs#',(isset($formvalues['mount_devfs']) ? $formvalues['mount_devfs'] : 1),$file);
		$file=str_replace('#allow_mount#',(isset($formvalues['allow_mount']) ? $formvalues['allow_mount'] : 1),$file);
		$file=str_replace('#allow_devfs#',(isset($formvalues['allow_devfs']) ? $formvalues['allow_devfs'] : 1),$file);
		$file=str_replace('#allow_nullfs#',(isset($formvalues['allow_nullfs']) ? $formvalues['allow_nullfs'] : 1),$file);

		$file=str_replace('#mkhostsfile#',(isset($formvalues['mkhostsfile']) ? $formvalues['mkhostsfile'] : 1),$file);
		$file=str_replace('#devfs_ruleset#',(isset($formvalues['devfs_ruleset']) ? $formvalues['devfs_ruleset'] : 4),$file);
		$file=str_replace('#ver#',(isset($formvalues['ver']) ? $formvalues['ver'] : 'native'),$file);

		$file=str_replace('#mount_obj#',(isset($formvalues['mount_obj']) ? $formvalues['mount_obj'] : 0),$file);
		$file=str_replace('#mount_kernel#',(isset($formvalues['mount_kernel']) ? $formvalues['mount_kernel'] : 0),$file);

		$file=str_replace('#mount_src#',(isset($formvalues['mount_src']) ? $formvalues['mount_src'] : 0),$file);
		$file=str_replace('#vnet#',(isset($formvalues['vnet']) ? $formvalues['vnet'] : 0),$file);
		$file=str_replace('#applytpl#',(isset($formvalues['applytpl']) ? $formvalues['applytpl'] : 1),$file);
		$file=str_replace('#mdsize#',(isset($formvalues['mdsize']) ? $formvalues['mdsize'] : 0),$file);
		$file=str_replace('#floatresolv#',(isset($formvalues['floatresolv']) ? $formvalues['floatresolv'] : 1),$file);

		$file=str_replace('#pkg_bootstrap#',(isset($formvalues['pkg_bootstrap']) ? $formvalues['pkg_bootstrap'] : 1),$file);

		$file=str_replace('#runasap#',(isset($formvalues['runasap']) ? $formvalues['runasap'] : ''),$file);
                $file=str_replace('#with_img_helpers#',(isset($formvalues['with_img_helpers']) ? $formvalues['with_img_helpers'] : ''),$file);
	}
	return $file;
}


function getVmTemplate($formvalues)
{
    global $vmtplfile, $workdir;

	$file=file_get_contents($vmtplfile);

	sscanf($formvalues['bcreate_vm_os_type'],"%s %s",$vm_os_type,$nop);

	if(!empty($file))
	{
		$file=str_replace('#jname#',$formvalues['jname'],$file);
		$file=str_replace('#host_hostname#',$formvalues['host_hostname'],$file);
		$file=str_replace('#ip4_addr#',$formvalues['ip4_addr'],$file);
		$file=str_replace('#interface#',$formvalues['interface'],$file);
		$file=str_replace('#astart#',(isset($formvalues['astart']) ? $formvalues['astart'] : 0),$file);
		$file=str_replace('#user_pw_root#',$formvalues['user_pw_root'],$file);
		$file=str_replace('#sysrc_enable#',$formvalues['sysrc_enable'],$file);

		$file=str_replace('#workdir#',$workdir,$file);
		$file=str_replace('#ver#',(isset($formvalues['ver']) ? $formvalues['ver'] : 'native'),$file);
		$file=str_replace('#arch#',(isset($formvalues['arch']) ? $formvalues['arch'] : 'native'),$file);
		$file=str_replace('#pkg_bootstrap#',(isset($formvalues['pkg_bootstrap']) ? $formvalues['pkg_bootstrap'] : 1),$file);

		$file=str_replace('#runasap#',(isset($formvalues['runasap']) ? $formvalues['runasap'] : ''),$file);
		$file=str_replace('#with_img_helpers#',(isset($formvalues['with_img_helpers']) ? $formvalues['with_img_helpers'] : ''),$file);

		$file=str_replace('#vm_size#',$formvalues['vm_size'],$file);
		$file=str_replace('#vm_cpus#',$formvalues['vm_cpus'],$file);
		$file=str_replace('#vm_ram#',$formvalues['vm_ram'],$file);

		$file=str_replace('#bcreate_vm_os_type#',$vm_os_type,$file);
		$file=str_replace('#vm_efi#',$formvalues['vm_efi'],$file);
		$file=str_replace('#bcreate_vm_os_profile#',$formvalues['bcreate_vm_os_profile'],$file);
		$file=str_replace('#vm_guestfs#',$formvalues['vm_guestfs'],$file);
		
	}
	return $file;
}



function human_filesize($bytes, $decimals = 2) {
	$sz = 'BKMGTP';
	$factor = floor((strlen($bytes) - 1) / 3);
	return sprintf("%.{$decimals}f ", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

// $status=check_vmonline("f10a");
// if status=1 - vm online
function check_vmonline($vm) {
	$vmmdir="/dev/vmm";

	if (!file_exists($vmmdir)) return 0;

	if ($handle = opendir($vmmdir)) {
		while (false !== ($entry = readdir($handle))) {

			if ($entry[0]==".") continue;

			if ( "${vm}" == "${entry}" ) {
				return 1;
			}
		}
		closedir($handle);
	}

	return 0;
}

//=============================================================================
// exec command
function cmd($cmd) {
//    $objResponse=new xajaxResponse();

    $descriptorspec = array(
			    0 => array('pipe','r'),
			    1 => array('pipe','w'),
			    2 => array('pipe','r')
    );
    
    $process = proc_open(trim($cmd),$descriptorspec,$pipes,null,null);

    $error=false;
    $error_message='';
    $message='';

    if (is_resource($process)) {
	$buf=stream_get_contents($pipes[1]);
        $buf0=stream_get_contents($pipes[0]);
        $buf1=stream_get_contents($pipes[2]);
        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $return_value = proc_close($process);
        if($return_value!=0) {
	    $error=true;
            $error_message=$buf;
        }else{
    	    $message=trim($buf);
        }

        return array('cmd'=>$cmd,'retval'=>$return_value,'message'=>$message,'error'=>$error,'error_message'=>$error_message);
    }
}

?>
