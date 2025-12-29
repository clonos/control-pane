<h1>Disks list:</h1>

<!-- https://angel-rs.github.io/css-color-filter-generator/ -->
<style>
	.dblock {
		border:1px solid black;
		border-radius:4px;
		padding:10px;
		margin-bottom:20px;
	}
	
	.dheader {
		position:absolute;
		margin-top:-20px;
		background-color:white;
		padding:0 10px;
	}
/*
	img.svg:hover {
		opacity:50%;
	}
*/

	#s-hdd, #s-hdd1 {
		width:60px;
		height:78px;
		display:block;
		background:0 0/60px 78px no-repeat url(/images/svg/hdd.svg);
	}
/*	
	.svg {
	  filter: brightness(0) saturate(100%) invert(72%) sepia(65%) saturate(5061%) hue-rotate(115deg) brightness(107%) contrast(93%);;
	}
*/
/*	
	.container {
		display:grid;
		grid-template-columns: minmax(200px, 50%)1fr;
		gap:4px;
	}
	.cell {
		border:1px solid red;
		min-width:200px;
	}
*/
	.container {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
	}

	.cell {
		flex: 1 1 48%;
		min-width: 450px;
		box-sizing: border-box;
		border: 1px solid #ccc;
		border-radius: 6px;
		padding: 10px;
	}

	.cell {
		word-wrap: break-word;
		overflow-wrap: break-word;
		word-break: break-all;
		display:flex;
		gap:10px;
		/*line-height:6px;*/
	}
	.cell .system {
		float:left;
		position:absolute;
	}
	.cell.empty {
		border-width:0;
	}
	.dinfo {
		flex-grow: 1;
	}
	.dinfo div:hover {
		background-color:rgba(0,0,0,5%);*/
	}
	.dinfo span {
		display:inline-block;
		width:100px;
	}
	
	
</style>

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
	<div class="cell">
		<!-- <span class="system">system</span> -->
		<img src="/images/svg/#type#.svg" alt="HDD" width="60px" height="78px" class="svg">
		<div class="dinfo">
			<div><span>disk:</span> #disk# (#model#)</div>
<!--			<div><span>raid:</span> #raid#</div> -->
			<div><span>parent:</span> #parent#</div>
			<div><span>businfo:</span> #businfo#</div>
			<div><span>rpm:</span> #rpm#</div>
			<div><span>perf / perf4k:</span> #perf# / #perf4k#</div>
			<div><span>capacity:</span> #capacity_human# (#capacity#)</div>
			<div><span>serial:</span> #ident#</div>
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
					$html.='<div class="cell empty"></div>';
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