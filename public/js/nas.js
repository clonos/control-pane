var nas={
	disks:[],
	disks_need:0,
	raidEngines:[],
	
	start:function()
	{
		this.checkTaskQueue();
		//this.getDisksList();
	},
	
	checkTaskQueue:function()
	{
		for(t in clonos.taskQueue)
		{
			if(t.substring(0,4)=='nas.')
			{
				var func=t.substring(4);
				if(typeof this[func]=='function')
				{
					this[func](clonos.taskQueue[t]);
					delete clonos.taskQueue[t];
				}
			}
		}
	},
	
	getDiskInfo:function(data)
	{
		$('#di-disk-name').html(data.disk);
		$('#tab-smart-cnt').html(data.html);
		var dinf=$('#disk-info .disk-list ul');
		$('li',dinf).removeClass('sel');
		$('li[data-disk="'+data.disk+'"]',dinf).addClass('sel');
	},
	
	bodyClick:function(target,elid)
	{
		var dblk=$(target).parents('div.dblock').get(0);
		if(typeof dblk!='undefined')
		{
			var cl=$(target).attr('class');
			switch(cl)
			{
				case 'diskOps':
					break;
				case 'diskInfo':
					$('#tab-smart-cnt').html('');
					$('#di-disk-name').html('&hellip;');
					
					var disk=$(target).parents('.cnt').attr('data-disk');
					if(isset(disk))
					{
						//var posts=[{'name':'disk','value':disk}];
						//clonos.loadData('diskInfoSmart',$.proxy(this.getDiskInfo,this),posts,true);
						this.getInfoSmart(disk);
					}
					clonos.dialogShow1('disk-info');
					break;
			}
			return;
		}
		
		var btn=$(target).data('btn');
		if(typeof btn=='string')
		{
			if(!$(target).hasClass('disabled'))
			{
				switch(btn)
				{
					case 'new-raid':
						var dlg=$(target).attr('data-dlg');
						if(typeof dlg=='string')
						{
							this.getRAIDsEngine();
							clonos.dialogShow1(dlg);
						}
						return;break;
					case 'next':
						this.nextPage();
						return;break;
					case 'prev':
						this.prevPage();
						return;break;
				}
			}
		}
		
		var reng=$(target).data('reng');
		if(typeof reng=='undefined') reng=$(target).parents('li').data('reng');
		if(typeof reng!='undefined')
		{
			this.selRAIDEngine(reng);
			return;
		}
		
		var cl=$(target).parents('div').attr('class');
		if(cl=='view')
		{
			var view=$(target).attr('data-view');
			if(typeof view!='undefined')
			{
				var vb=$('.vblock');
				for(i=0;i<vb.length;i++)
				{
					if($(vb[i]).attr('data-view')!=view)
					{
						$(vb[i]).hide();
					}else{
						$(vb[i]).show();
					}
				}
			}
		}
		
		//var dsort=$(target).data('sort');
		var dsort=$(target).data('sort');
		if(typeof dsort!='undefined' && dsort=='yes')
		{
			var sdir=$(target).attr('data-dir');
			sdir=(sdir=='asc')?'dsc':'asc';
			var par=$(target).parents('tr').get(0);
			$('th[data-dir]',par).attr('data-dir','unk');
			$(target).attr('data-dir',sdir);
			var table=$(target).parents('table');
			this.sort($(table).attr('id'),$(target).index(),sdir);
		}
		
		var frm=$(target).parents('form#disks-list');
		if(frm.length>0)
		{
			debugger;
		}

		if(typeof elid!='undefined')
		{
			switch(elid)
			{
				case 'tab-smart':
				case 'tab-info':
					clonos.tabClick(elid);
					return;break;
			}
		}
		
		if(typeof target.nodeName!='undefined')
		{
			if(target.nodeName='LI')
			{
				var disk=$(target).attr('data-disk');
				if(typeof disk==='string')
				{
					this.getInfoSmart(disk);
				}
			}
		}
		
	},
	formClick:function(target,elid)
	{
		this.updCountDisksInfo();
	},

	getDisksList:function()
	{
		clonos.loadData('getDisksList',$.proxy(function(data){
			debugger;
		},this));
	},
	
	updCountDisksInfo:function()
	{
		var reng=$('.raids-types .sel').data('reng');
		var need=this.raidEngines[reng].need;
		var rname=this.raidEngines[reng].name;
		$('#disks-need').html(need);
		var checked=$('form#disks-list input[type=checkbox]:checked').length;
		var tpl_few=$('template#create-few').html();
		var tpl_many=$('template#create-many').html();
		var tpl_success=$('template#create-success').html();
		var add=' '+tpl_success;	//' (можно создавать RAID)';
		var tclass='green';
		if(rname=='zpool_raid0')
		{
			if(checked<1){add=' '+tpl_few;tclass='red';}
		}else{
			if(checked<need){add=' '+tpl_few;tclass='red';}
			if(checked>need){add=' '+tpl_many;tclass='red';}
		}
		$('#disks-sel').html('<span class="'+tclass+'">'+checked+add+'</span>');
		
		$('.raids-types li').removeClass("ravail");
		this.raidEngines.forEach((e,index)=>{
			if(checked==e.need || (e.name=='zpool_raid0' && checked>0))
			{
				var el=$('.raids-types li[data-reng="'+index+'"]');
				if($('span.avail',el).length>0) $(el).addClass("ravail");
			}
		});
		if(checked<1)
		{
			$('.buttons .button[data-btn="next"]').addClass('disabled');
			$('.buttons .button[data-btn="prev"]').addClass('disabled');
			$('.buttons .button[data-btn="create"]').addClass('disabled');
		}else{
			$('.buttons .button[data-btn="next"]').removeClass('disabled');
			$('.buttons .button[data-btn="prev"]').removeClass('disabled');
			$('.buttons .button[data-btn="create"]').removeClass('disabled');
		}
	},
	
	getRAIDsEngine:function()
	{
		clonos.loadData('getRAIDsEngine',$.proxy(function(data){
			this.raidEngines=data.engines;
			this.disks=data.disks;
			var ul=$('.new-raid .raids-types ul');
			$(ul).empty();
			var tpl=$('template#tpl-engine').html();
			var html='';
			data.engines.forEach((e,index)=>{
				var sel='';
				var avail='notavail';
				if(e.avail) avail='avail';
				html=tpl;
				
				html=html.replaceAll('#sel#',sel);
				html=html.replaceAll('#index#',index);
				html=html.replaceAll('#avail#',avail);
				html=html.replaceAll('#ename#',e.name);
				html=html.replaceAll('#raid_level#',e.raid_level);
				$(ul).append(html);
			});
			this.selRAIDEngine(0);
			
			var tpl=$('template#tpl-disks').html();
			$('#disks-list').empty();
			var lastType='';
			this.disks.forEach((d,index)=>{
				html=tpl;
				if(d.raid=='unraid'){
					if(d.type!=lastType){
						if(lastType!='')$('#disks-list').append('<hr />');
						lastType=d.type;
					}
					d.num=index;
					for(key in d){
						html=html.replaceAll('#'+key+'#',d[key]);
					}
					$('#disks-list').append(html);
				}
			});
			/*
			annot:"annot-zpool_raid1"
			avail:false
			name:"zpool_raid1"
			need:2
			raid_level:"RAID 1 (mirror)"
			*/
			
		},this));
	},
	selRAIDEngine:function(num)
	{
		$('.new-raid .raids-types li.sel').removeClass('sel');
		$('.new-raid .raids-types li').eq(num).addClass('sel');
		var e=this.raidEngines[num];
		$('#notavail').removeClass('hide');
		$('#avail').removeClass('hide');
		if(!e.avail){
			$('#notavail-rname').html(e.raid_level);
			$('#notavail').show();
			$('#avail').hide();
			$('.buttons .button[data-btn="create"]').addClass('disabled');
			$('.buttons .pbuttons').addClass('onepage');
			return;
		}else{
			$('#notavail').hide();
			$('#avail').show();
			$('.buttons .button[data-btn="create"]').removeClass('disabled');
			$('.buttons .pbuttons').removeClass('onepage');
		}
		var annot=$('template#'+e.annot).html();
		var html='<p>no data: '+e.annot+'</p>';
		if(typeof annot!='undefined') html=annot;
		$('.new-raid .description').html(html);
		$('.new-raid #raid-name').html(e.raid_level);
		this.updCountDisksInfo();
	},
	
	getInfoSmart:function(disk)
	{
		var posts=[{'name':'disk','value':disk}];
		clonos.loadData('diskInfoSmart',$.proxy(this.getDiskInfo,this),posts,true);
	},
	
	makeDiskInfoList:function(data)
	{
		this.disks=data.disks;
		this.makeDisksView();
		var cnt=$('#disk-info .disk-list ul');
		if(cnt.length>0)
		{
			cnt=cnt[0];
			var sel='';
			$(cnt).html('');
			this.disks.forEach((d,index)=>{
				//if(index==0){sel=' class="sel"';}else{sel=''}
				//'+sel+'
				$(cnt).append('<li data-disk="'+d.disk+'">Disk '+(index+1)+': '+d.disk+' ('+d.type+')</li>');
			});
		}
		this.addSortDirections();
	},
	
	makeDisksView:function()
	{
		var blk=null;
		var html='';
		var view='div.vblock[data-view="disks-block"]';
		if(this.disks.length<1)return;
		var tplDBlockOuter=$('template#tplDBlockOuter').html();
		var tplDBlockInner=$('template#tplDBlockInner').html();
		this.disks.forEach((d,index)=>{
			var txt='div[data-raid="'+d.raid+'"] .container';
			blk=$(txt);
			if(blk.length<1){
				html=tplDBlockOuter.replaceAll('#raid#',d.raid);
				$(view).append(html);
				blk=$(txt);
			}
			html=tplDBlockInner;
			for(key in d){
				if(key=='type')
				{
					d[key]=d[key].toLowerCase();
					d['typeUC']=d[key].toUpperCase();
				}
				switch(d['type'])
				{
					case 'md':
						d['type']='ram';
						break;
				}
				html=html.replaceAll('#'+key+'#',d[key]);
			}
			html=html.replaceAll('#typeUC#',d['typeUC']);
			$(blk).append(html);
		});
		$(view).show();
		
		this.makeTableView();
	},
	
	makeTableView:function()
	{
		var table=$('#disklist');
		var html='';
		var tr='';
		var view='div.vblock[data-view="disks-table"]';
		var tplTr=$('template#tplDTableTr').html();
		if(table.length>0 && tplTr.length>0)
		{
			this.disks.forEach((d,index)=>{
				tr=tplTr;
				for(key in d){
					//if(key=='type')d[key]=d[key].toLowerCase();
					tr=tr.replaceAll('#'+key+'#',d[key]);
				}
				$('tbody',table).append(tr);
			});
		}
		//$(view).show();
	},
	
	makeRAIDsList:function(data)
	{
		this.makePages();
		this.disks=data.disks;
		this.raids=data.raids;
		var view=$('div[data-view="raids"]');
		var block=$('template#tplRBlock').html();
		var html='';
		var tr='';
		var unraid=0;
		var tplTr=$('template#tplRTableTr').html();
		this.disks.forEach((d,index)=>{
			if(d.raid!='unraid')
			{
				var raid=$('div[data-raid="'+d.raid+'"]');
				var tpl=block;
				var tr=tplTr;
				
				this.raids.forEach((r,index)=>{
					if(r.name==d.raid){
						for(rkey in r){
							tpl=tpl.replaceAll('#'+rkey+'#',r[rkey]);
						}
					}
				});
				
				for(key in d){
					tpl=tpl.replaceAll('#'+key+'#',d[key]);
					tr=tr.replaceAll('#'+key+'#',d[key]);
				}
				if(raid.length==0){
					$(view).append(tpl);
					$('div[data-raid="'+d.raid+'"] tbody').html(tr);
				}else{
					$('tbody',raid).append(tr);
				}
				var tag=$('div[data-raid="'+d.raid+'"] strong[data-val="dcount"]');
				var dcount=parseInt($(tag).text());
				$(tag).text(++dcount);
			}else{
				var tr=tplTr;
				for(key in d){
					tr=tr.replaceAll('#'+key+'#',d[key]);
				}
				$('div[data-view="unraid"] tbody').append(tr);
				unraid++;
			}
		});
		/*
		var blocks=$('div.dblock div.dheader');
		if(blocks.length>0){
			blocks.each((index,b)=>{
				$(b).text('RAID '+(index+1));
			});
		}
		*/
		if(unraid>0) $('div.vblock[data-view="unraid"]').show();
		this.addSortDirections();
	},
	
	makePages:function()
	{
		this.pages=[];
		this.currentPage=0;
		this.pagesCount=0;
		var pages=$('div.pages[data-page]');
		if(pages.length==1)
		{
			$('.buttons .pbuttons').removeClass('first');
			$('.buttons .pbuttons').addClass('onepage');
		}
		if(pages.length>0)
		{
			this.pagesCount=pages.length;
			for(p in pages){
				this.pages[p]=pages[p];
			}
		}
	},
	nextPage:function()
	{
		if((this.currentPage+1)<this.pagesCount)
		{
			$(this.pages[this.currentPage]).hide();
			this.currentPage++;
			$(this.pages[this.currentPage]).show();
			$(this.pages[this.currentPage]).removeClass('hide');
		}
		if(this.currentPage>0 && this.currentPage<this.pagesCount)
		{
			$('.buttons .pbuttons').removeClass('first');
		}
		if(this.currentPage==this.pagesCount-1)
		{
			$('.buttons .pbuttons').removeClass('first').addClass('last');
		}
	},
	prevPage:function()
	{
		if(this.currentPage>0)
		{
			$(this.pages[this.currentPage]).hide();
			this.currentPage--;
			$(this.pages[this.currentPage]).show();
			$(this.pages[this.currentPage]).removeClass('hide');
		}
		if(this.currentPage>0 && this.currentPage<this.pagesCount)
		{
			$('.buttons .pbuttons').removeClass('last');
		}
		if(this.currentPage==0)
		{
			$('.buttons .pbuttons').removeClass('last').addClass('first');
		}
	},
	
	sort:function(tableId,column,direction)
	{
		const tbody = $('#'+tableId+' tbody').get(0);
		const rows = Array.from(tbody.rows);

		rows.sort((a, b) => {
			const cellA = a.cells[column].textContent.trim();
			const cellB = b.cells[column].textContent.trim();
			if(direction=='asc'){
				return isNaN(cellA) ? cellA.localeCompare(cellB) : cellA - cellB;
			}else{
				return isNaN(cellA) ? cellB.localeCompare(cellA) : cellB - cellA;
			}
		});

		for(n in rows){
			$(tbody).append(rows[n]);
		}
	},
	addSortDirections:function()
	{
		var attr='';
		var ths=$('.tsimple th[data-sort="yes"]')
		for(t=0;t<ths.length;t++){
			attr=$(ths[t]).attr('data-dir');
			if(typeof attr=='undefined') $(ths[t]).attr('data-dir','unk');
		}
	},
}

$(window).on('load',function(){nas.start();});