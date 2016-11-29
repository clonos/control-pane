var clonos={
	jsonLoad:false,
	
	start:function()
	{
		this.addEvents();
		
		var r, res, args=[];
		var hash=window.location.hash;
		hash=hash.replace(new RegExp(/^#/),'');
		var rx=new RegExp(/([^\/]+)/g);
		if(res=hash.match(rx))
		{
			for(r in res)
			{
				var r1=res[r].split('-');
				if(r1.length==2) args[args.length]={'var':r1[0],'val':r1[1]};
			}
			this.route(args);
		}
	},
	
	addEvents:function()
	{
		$('#lng-sel').bind('change',function(){document.cookie="lang="+$(this).val()+";path=/;";location.reload();});
		$('#content').bind('click',$.proxy(this.tableClick,this));
	},
	
	translate:function(phrase)
	{
		if(typeof this.lang!='undefined')
		{
			if(typeof this.lang[phrase]!='undefined')
				return this.lang[phrase];
		}
		return phrase;
	},
	
	route:function(args)
	{
		if(typeof args=='undefined') return;
		//alert(args.length);
	},
	
	loadData:function()
	{
		if(!this.jsonLoad) return;
		var path=location.pathname;
		var file='/json.php';	//'/pages'+path+'a.json.php';
		this.loadDataJson(file,$.proxy(this.onLoadData,this),{'path':path});
	},
	loadDataJson:function(file,return_func,arr)	//mode,
	{
		var posts=arr;
/*
		var posts={'mode':mode,'project':this.project,'jail':this.jail,'module':this.module};
		if(typeof arr=='object')
		{
			posts['form_data']={};
			for(n=0,nl=arr.length;n<nl;n++)
				posts['form_data'][arr[n]['name']]=arr[n]['value'];
		}
*/
		$.post(file,posts,
			$.proxy(function(data){return_func(data);},this));	
	},
	onLoadData:function(data)
	{
		try{
			var data=$.parseJSON(data);
		}catch(e){alert(e.message);return;}
		
		if(data.error)
		{
			var t=$('tbody.error td.error_message');
			if(typeof(data.error_message)!='undefined' && data.error_message!='') t.html(data.error_message);
			$(t).parents('table').addClass('error');
		}else{
			if(typeof data.func!='undefined')
			{
				this[data.func](data);
				return;
			}
			for(id in data) $('#'+id).html(data[id]);
		}
	},
	
	fillTable:function(data)
	{
		if(typeof data.id!='undefined')
		{
			$('#'+data.id+' tbody').html(data.html);
		}
	},
	
	enableWait:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		$(icon).removeClass('icon-play');
		$(icon).removeClass('icon-stop');
		$(icon).addClass('icon-spin6 animate-spin');
	},
	enablePlay:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		if(icon[0]) icon[0].className='icon-play';
	},
	enableStop:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		if(icon[0]) icon[0].className='icon-stop';
	},
	enableRip:function(id)
	{
		var icon_cnt=$('tr.id-'+id).find('span.icon-cnt');
		if(typeof icon_cnt!='undefined')
		{
			var icon=$(icon_cnt).find('span');
			if(typeof icon!='undefined')
				icon[0].className='icon-emo-cry';
		}
	},
	
	jailStart:function(obj)
	{
		if(!obj) return;
//		if(this.currentPage=='services') return this.serviceStart(obj);
//		var id=this.getJailId(obj);
//		if(id<0) return;
		var icon_cnt=$(obj).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		op='';
		if($(icon).hasClass('icon-play')) op='jstart';
		if($(icon).hasClass('icon-stop')) op='jstop';
		this.enableWait(id);
		
		var op_status=(op=='jstart'?1:0);
		
		if(op!='')
		{
			this.tasks.add({'operation':op,'jail_id':id});
			this.tasks.start();
		}
	},
	
	
	tasks:
	{
		context:null,
		tasks:{},
		interval:null,
		checkTasks:false,
		
		init:function(context)
		{
			this.context=context;
		},
		
		add:function(vars)
		{
			if(typeof vars['status']=='undefined') vars['status']=-1;
			if(typeof vars['jail_id']!='undefined')
				this.tasks[vars['jail_id']]=vars;
			if(typeof vars['modules_id']!='undefined')
				this.tasks['mod_ops']=vars;
			if(typeof vars['service_id']!='undefined')
				this.tasks[vars['service_id']]=vars;
			if(typeof vars['projects_id']!='undefined')
			{
				this.tasks['proj_ops']='projDelete';
				this.tasks[vars['projects_id']]=vars;
			}
		},
		
		start:function()
		{
			if(this.checkTasks) return;
			this.checkTasks=true;
			
			if($.isEmptyObject(this.tasks))
			{
				clearInterval(this.interval);
				this.interval=null;
				this.checkTasks=false;
				return;
			}
			
			var vars=JSON.stringify(this.tasks);
			this.context.loadData('getTasksStatus',$.proxy(this.update,this),[{'name':'jsonObj','value':vars}]);
		},
		
		update:function(data)
		{
			try{
				var data=$.parseJSON(data);
			}catch(e){alert(e.message);return;}
			
			if(typeof data['mod_ops']!='undefined')
			{
				var key='mod_ops';
				this.tasks[key]=data[key];
				var d=data[key];
				
				if(d.status==2)
				{
					//this.context.onTaskEnd(this.tasks[key],key);
					this.context.modulesUpdate(data);
					delete this.tasks[key];
					this.context.waitScreenHide();
				}
				if(d.status<2) this.context.waitScreenShow();
				
				this.checkTasks=false;
				if(this.interval===null)
				{
					this.interval=setInterval($.proxy(this.start,this),1000);
				}
				return;
				
			}
			
			if(typeof data['proj_ops']!='undefined')
			{
				if(data['proj_ops']=='projDelete')
				{
					if(typeof data.projects!='undefined')
						this.context.projectsList=data.projects;
					this.context.showProjectsList();
					return;
				}
			}
			
			for(key in data)
			{
				if(key>0)
				{
					$('tr.id-'+key+' .jstatus').html(data[key].txt_status);
					var errmsg=$('tr.id-'+key+' .errmsg');
					if(typeof data[key].errmsg!='undefined')
					{
						$(errmsg).html('<span class="label">Error:</span>'+data[key].errmsg);	//'+this.translate('Error')+'
						this.tasks[key].errmsg=data[key].errmsg;
					}
					this.tasks[key].operation=data[key].operation;
					this.tasks[key].task_id=data[key].task_id;
					this.tasks[key].status=data[key].status;
					
					if(data[key].status==2)
					{
						this.context.onTaskEnd(this.tasks[key],key);
						delete this.tasks[key];
					}
				}else{
					if(typeof data[-1].jails!='undefined')
					{
						this.context.jailsList=data[-1].jails;
						this.context.showJailsList();
					}
				}
			}
			
			this.checkTasks=false;
			
			if(this.interval===null)
			{
				this.interval=setInterval($.proxy(this.start,this),1000);
			}

		},
	},
	
	tableClick:function(event)
	{
		debugger;
		var target=event.target;
/*  		if(target.id=='main_chkbox')
		{
			this.mainChkBoxClick(event);
			return;
		} */
		var td=$(target).closest('td');
		td=td[0];
		var tr=$(target).closest('tr');
/* 		if(target.tagName=='SPAN')
		{
			var cl=target.className;
			if(cl && cl.indexOf('install')>=0)
			{
				var res=cl.match(new RegExp(/helper-(\w+)/));
				if(res)
				{
					this.installHelper(res[1]);
					return;
				}
			}
			
			if(cl && cl.indexOf('default')>=0)
			{
				var res=cl.match(new RegExp(/val-(.*)/));
				if(res)
				{
					this.fillHelperDefault(target,res[1]);
					return;
				}
			}
		}
 */
/* 		if(target.tagName=='INPUT')
		{
			var cl=target.className;
			if(cl=='') return;
			if(cl=='save-helper-values') this.saveHelperValues();
			if(cl=='clear-helper') this.clearHelperForm(target);
			
			return;
		}
		
		if(typeof td!='undefined') this.selItem(tr);
		
		if(typeof tr[0]=='undefined') return;
		var cl=tr[0].className;
		if(!$(tr).hasClass('link')) return;
		var rx=new RegExp(/id-(\d+)/);
		if(res=cl.match(rx))
		{
			var id=res[1];
		} */
	//debugger;
		if(!td || typeof td.className=='undefined') return false;
		var tdc=td.className;
		tdc=tdc.replace(' ','-');
		
		switch(tdc)
		{
			case 'ops':
				this.jailStart(tr);
				return;break;
/* 			case 'sett-proj':
				this.lastProjectId=id;
				this.editMode='edit-proj';
				this.projSettings(id);
				return;break;
			case 'sett':
				this.lastJailId=id;
				this.editMode='edit';
				this.getJailSettings(tr);
				return;break;
			case 'jstatus':
				return;break;
			case 'info':
				this.loadData('getForm',$.proxy(this.loadForm,this));
				return;break;
			case 'mod-info':
				alert('show info about module!');
				return;break;
			case 'user-info':
				this.editMode='user-edit';
				var n;
				data=null;
				for(n=0,nl=this.usersList.length;n<nl;n++)
					if(this.usersList[n].id==id) {data=this.usersList[n];break;}
				if(data==null) return;
				var obj_cnt=this.settWinOpen('users');
				var form=$('form',obj_cnt);
				$('#window-content h1').html(this.translate('User edit'));
				$('input[name="login"]',form).val(data.login).attr('disabled','disabled');
				$('input[name="fullname"]',form).val(data.gecos);
				return;break;
 */		}
		
/*		if($(td).hasClass('chbx'))
		{
			// tr.link.hover.id-1
			if(this.currentPage=='project')
				if(id>0) this.selectedProjects[id]=$(td).children('input[type="checkbox"]').prop('checked');
			if(this.currentPage=='jails')
				if(id>0) this.selectedJails[id]=$(td).children('input[type="checkbox"]').prop('checked');
			if(this.currentPage=='modules')
				if(id>0) this.selectedModules[id]=$(td).children('input[type="checkbox"]').prop('checked');
			return;
		}
		
		switch(this.currentPage)
		{
			case 'project':
				location.hash='#prj-'+id;
				break;
			case 'jails':
				location.hash='#prj-'+this.project+'/jail-'+id;
				break;
			case 'modules':
				location.hash='#prj-'+this.project+'/jail-'+this.jail+'/module-'+id;
				break;
			case 'log':
				var hid=$('td.sp-id',tr).html();
				location.hash='#prj-'+this.project+'/jail-'+this.jail+'/log-'+hid;
				break;
			case 'helpers':
				var hid=$('td .sp-id',tr).html();
				location.hash='#prj-'+this.project+'/jail-'+this.jail+'/helpers-'+hid;
				break;
		}
		*/
	},
}

$(window).load(function(){clonos.start();});
$(window).unload(function(){});	/* эта функция заставляет FireFox запускать JS-функции при нажатии кнопки «Назад»
http://stackoverflow.com/questions/2638292/after-travelling-back-in-firefox-history-javascript-wont-run */
$(document).ready(function(){clonos.loadData();});