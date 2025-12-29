<?php
$this->useDialogs([
	'disk-info',
]);
?>
<h1>Disks list:</h1>
<!--
<script>document.write(JSON.stringify({"test":{"a":1,"b":2}}))</script>
-->
<!-- https://angel-rs.github.io/css-color-filter-generator/ -->

<!--
<div class="container">
	<div class="cell">
		<img src="/images/svg/hdd.svg" alt="HDD" width="60px" height="78px" class="svg">
		<div class="dinfo">
			<div>name: ada0</div>
			<div>system: false</div>
		</div>
	</div>
	<div class="cell">zxcv</div>
	<div class="cell">fgdgfh</div>
	<div class="cell">wert</div>
	<div class="cell">sdfgs</div>
	<div class="cell">cvbnc</div>
</div>

<span id="s-hdd"></span>


<img src="/images/svg/hdd.svg" alt="HDD" width="60px" height="78px" class="svg">
<img src="/images/svg/nvme.svg" alt="NVME" width="60px" height="78px" class="svg">
<img src="/images/svg/ssd.svg" alt="SSD" width="60px" height="78px" class="svg">
<img src="/images/svg/ram.svg" alt="RAM DISK" width="60px" height="78px" class="svg">
-->
<?php
$patt='
	<div class="cnt"><!--{"info":{"disk":"#disk#"}}-->
		<div class="cell">
			<!-- <span class="system">system</span> -->
			<img src="/images/svg/#type#.svg" alt="HDD" width="60px" height="78px" class="svg">
			<div class="dinfo">
				<div><span>disk:</span> #disk# (#model#)</div>
				<div><span>parent:</span> #parent#</div>
				<div><span>businfo:</span> #businfo#</div>
				<div><span>rpm:</span> #rpm#</div>
				<div><span>perf / perf4k:</span> #perf# / #perf4k#</div>
				<div><span>capacity:</span> #capacity_human# (#capacity#)</div>
				<div><span>serial:</span> #ident#</div>
			</div>
		</div>
		<div class="ops">
			<span class="chbx"><input type="checkbox" /></span>
			<span class="diskOps">operations</span>
			<span class="diskInfo">info</span>
		</div>
	</div>
';


$filename='/var/db/cixnas/api/disks.json';
$html='';
if(file_exists($filename))
{
	$txt=file_get_contents($filename);
	try{
		$arr=json_decode($txt,true);
		//echo '<pre>';print_r($arr);exit;
		
		$raids=[];
		$dcount=count($arr);
		
		foreach($arr as $num=>$disk)
		{
			$raids[$disk['raid']][]=$disk;
		}
		
		//echo '<pre>';print_r($raids);exit;

/*		
		foreach($arr as $num=>$disk)
		{
			$pattern=$patt;
			foreach($disk as $key=>$val)
			{
				if($key=='type')
				{
					$val=strtolower($val);
					if($val=='md')$val='ram';
				}
				$pattern=str_replace('#'.$key.'#',$val,$pattern);
			}
			$html.=$pattern;
		}
*/

		foreach($raids as $key=>$raid)
		{
			$html.='<div class="dblock"><div class="dheader">RAID: <strong>'.$key.'</strong></div>';
			$html.='<div class="container">';
			foreach($raid as $num=>$disk)
			{
				$pattern=$patt;
				foreach($disk as $key=>$val)
				{
					if($key=='type')
					{
						$val=strtolower($val);
						if($val=='md')$val='ram';
					}
					$pattern=str_replace('#'.$key.'#',$val,$pattern);
				}
				$html.=$pattern;
				if(count($raid)%2==1)
				{
					$html.='<div class="cnt empty"></div>';
				}
			}

			$html.='</div>';
			$html.='</div>';
		}

	}catch(Exception $e){
		echo $e->getMessage();
	}
	
	
	//echo '<div class="container">';
	echo $html;
	//echo '</div>';
	
}


/*
	[disk] => ada0
	[sys] => 
	[raid] => unraid
	[type] => HDD
	[parent] => ahci0
	[businfo] => SATA 3.3, 6.0 Gb/s (current: 6.0 Gb/s)
	[rpm] => 7200
	[perf] => 0
	[perf4k] => 242
	[model] => TOSHIBA MG07ACA14TE
	[ident] => 31M0A0CEF94G
	[capacity] => 14000519643136
	[capacity_human] => 12 Tb
*/
