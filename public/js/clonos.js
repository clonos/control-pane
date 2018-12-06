var clonos={
	
	tmp_jail_info:{},
	manual_close_menu:false,
	lastX:0,
	oldHash:'',
	commands:
	{
		'jstart':{stat:['Not launched','Starting','Launched'],cmd:'jailStart'},
		'jstop':{stat:['Launched','Stopping','Stopped'],cmd:'jailStop'},
		'jcreate':{stat:['Create','Creating','Created'],cmd:'jailAdd'},
		'jremove':{stat:['Remove','Removing','Removed'],cmd:'jailRemove'},
		'jrestart':{stat:['Restart','Restarting','Restarted'],cmd:'jailRestart'},
		'jclone':{stat:['Clone','Cloning','Cloned'],cmd:'jailClone'},
		'jexport':{stat:['Export','Exporting','Exported'],cmd:'jailExport'},
		'jrename':{stat:['Rename','Renaming','Renamed'],cmd:'jailRename'},
		'bstart':{stat:['Not launched','Starting','Launched'],cmd:'bhyveStart'},
		'bstop':{stat:['Launched','Stopping','Stopped'],cmd:'bhyveStop'},
		'brestart':{stat:['Restart','Restarting','Restarted'],cmd:'bhyveRestart'},
		'bcreate':{stat:['Create','Creating','Created'],cmd:'bhyveAdd'},
		'bremove':{stat:['Remove','Removing','Removed'],cmd:'bhyveRemove'},
		'bclone':{stat:['Clone','Cloning','Cloned'],cmd:'bhyveClone'},
		'brename':{stat:['Rename','Renaming','Renamed'],cmd:'bhyveRename'},
		'vm_obtain':{stat:['Create','Creating','Created'],cmd:'bhyveObtain'},
		'srcup':{stat:['Update','Updating','Updated'],cmd:'srcUpdate'},
		'world':{stat:['Compile','Compiling','Compiled'],cmd:'basesCompile'},
		'repo':{stat:['Fetch','Fetching','Fetched'],cmd:'repoCompile'},
		'removesrc':{stat:['Remove','Removing','Removed'],cmd:'srcRemove'},
		'removebase':{stat:['Remove','Removing','Removed'],cmd:'baseRemove'},
		'imgremove':{stat:['Remove','Removing','Removed'],cmd:'imageRemove'},
	},
	
	start:function()
	{
		this.addEvents();
		
		var r, res, args=[];
		var hash=window.location.hash;
		hash=hash.replace(new RegExp(/^#/),'');
		var rx=new RegExp(/([^\/]+)/g);
		if(res=hash.match(rx))
		{
			/*
			for(r in res)
			{
				var r1=res[r].split('-');
				if(r1.length==2) args[args.length]={'var':r1[0],'val':r1[1]};
			}
			*/
			//this.route(res);
		}
	},
	route:function(args)
	{
		if(typeof args=='undefined') return;
		//this.onHashChange();
	},
	onHashChange:function(event)
	{
		var hash=location.hash;
		if(hash=='')
		{
			$('#tab2').hide().html('');
			$('#tab1').show();
		}else{
			$('#tab1').hide();
			$('#tab2').show();
		}
		this.loadData('getJsonPage',$.proxy(this.onLoadData,this));
	},
	
	addEvents:function()
	{
		$(window).on('hashchange',$.proxy(this.onHashChange,this));
		$('#lng-sel').on('change',$.proxy(this.setLang,this));	//function(){document.cookie="lang="+$(this).val()+";path=/;";location.reload();});
		$('#content').on('click',$.proxy(this.bodyClick,this));
		$('#login').on('click',$.proxy(this.loginAction,this));
		$('.closer').on('click',$.proxy(this.closerClick,this));
		$(window).on('keypress',$.proxy(this.dialogCloseByKey,this))
			.on('resize',$.proxy(this.onResize,this));
		$('div.menu').on("touchstart",$.proxy(this.onTouchStart,this))
			.on("touchend",$.proxy(this.onTouchEnd,this));
		
		this.tasks.init(this);
		this.wsconnect();
	},
	
	onResize:function()
	{
		if(this.manual_close_menu) return;
		var wdt=$(window).width();
		if(wdt<800) $('body').addClass('gadget'); else $('body').removeClass('gadget');
		//setTimeout(graphs.onResize,500);
	},
	closerClick:function(event)
	{
		$('body').toggleClass('gadget');
		this.manual_close_menu=true;
	},
	onTouchStart:function(event)
	{
		var target=event.target;
		
		if(typeof target.nodeName!='undefined')
		{
			if(target.nodeName!='DIV') return;
		}
		
		$('.closer').css({'backgroundColor':'silver'});
		setTimeout(function(){$('.closer').css({'backgroundColor':''})},100);
		
		var t=event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
		this.lastX=t.clientX;
		//event.stopPropagation();
		event.preventDefault();
	},
	onTouchEnd:function(event)
	{
		var target=event.target;
		if(typeof target.nodeName!='undefined')
		{
			var cl=target.className;
			if(typeof cl!='undefined' && cl=='menu') return;
		}
		
		if(typeof target.className!='undefined')
		{
			if(target.className=='closer')
			{
				$('body').toggleClass('gadget');
				this.manual_close_menu=true;
				return;
			}
		}
		
		if(target.nodeName!='DIV') return;

		var t=event.originalEvent.touches[0] || event.originalEvent.changedTouches[0];
		var curX=t.clientX;
		if(curX<this.lastX)
		{
			$('body').addClass('gadget');
			this.manual_close_menu=true;
		}else{
			$('body').removeClass('gadget');
			this.manual_close_menu=true;
		}
		//event.stopPropagation();
		event.preventDefault();
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
	
	getTrIdsForCheck:function(table_id)
	{
		var ids=[];
		var els=$('#'+table_id+' tr');
		if(els.length<1) return ids;
		for(n=0,nl=els.length;n<nl;n++)
		{
			var id=$(els[n]).attr('id');
			if(typeof id!='undefined') ids[n]=id;
		}
		return ids;
	},
	
	dialogOpen:function(event)
	{
		var tg=event.target;
		var cl=$(tg).attr('class');
		var res=new RegExp(/id:([^ ]+)/);
		if(res=cl.match(res))
		{
			var id=res[1];
			if(id=='jail-settings')
			{
				this.getFreeJname();	// Берём с сервера свободное имя клетки
				this.trids=this.getTrIdsForCheck('jailslist');
			}
			if(id=='bhyve-new')
			{
				this.trids=this.getTrIdsForCheck('bhyveslist');
				this.updateBhyveISO();
				this.updateBhyveOSProfile();
				if(typeof this.vm_packages_new_min_id!='undefined')
					$('#bhyveSettings select[name="vm_packages"]').val(this.vm_packages_new_min_id).change();
			}
			if(id=='bhyve-obtain')
			{
				if(typeof this.vm_packages_obtain_min_id!='undefined')
					$('#bhyveObtSettings select[name="vm_packages"]').val(this.vm_packages_obtain_min_id).change();
			}
			this.dialogShow1(id);
		}
	},
	dialogShow:function(jname,type)
	{
		if(typeof jname=='undefined') return;
		$('#vnc-iframe').attr('src','/vnc.php?jname='+jname);
		
		this.dialogShow1('vnc',type);
		$('#vnc-iframe').get(0).focus();
		$('#vnc-iframe').on('blur',function(){$('#vnc-iframe').get(0).focus();});
	},
	dialogShow1:function(id,mode)
	{
		var dlg=$('dialog#'+id);
		
		if(mode=='edit')
		{
			$(dlg).removeClass('new').addClass('edit');
			$(dlg).prop('mode','edit');
		}else{
			$(dlg).removeClass('edit').addClass('new');
			$(dlg).prop('mode','new');
		}
		$('dialog#'+id+'.edit .edit-disable, dialog#'+id+'.new .new-disable').prop('disabled',true);
		$('dialog#'+id+'.edit .new-disable, dialog#'+id+'.new .edit-disable').prop('disabled',false);
		
		if($('span.close-but',dlg).length==0)
			$('h1',dlg).before('<span class="close-but">×</span>');
		/*
		var wd=$(dlg).width();
		var hg=$(dlg).height();
		var mt=hg/2;
		var ml=wd/2;
		*/
		var res=$(dlg).get(0).showModal;
		if(typeof res=='function')
		{
			$('dialog#'+id).css('display','block').get(0).showModal();
			$('dialog#'+id).on('close',$.proxy(this.dialogClose,this));
		}else{
			var bkg=$('div#backdrop').get(0);
			if(typeof bkg=='undefined')
			{
				$('dialog#'+id).before('<div id="backdrop"></div>');
			}
			/*
			$('dialog#'+id).css({
				'display':'block',
				'top':'50%',
				'margin-top':'-'+mt+'px',
				'left':'50%',
				'margin-left':'-'+ml+'px',
				'position':'fixed',
				'z-index':'100000',
			});
			*/
			this.dialogSetPosition(dlg);
			$('div#backdrop').css('display','block');
		}
		$(dlg).find('input[type=text],textarea').filter(':visible:first').focus();
	},
	dialogSetPosition:function(dialog)
	{
		var wd=$(dialog).width();
		var hg=$(dialog).height();
		var mt=hg/2;
		var ml=wd/2;

		$(dialog).css({
			'display':'block',
			'top':'50%',
			'margin-top':'-'+mt+'px',
			'left':'50%',
			'margin-left':'-'+ml+'px',
			'position':'fixed',
			'z-index':'100000',
		});
	},
	dialogFullscreen:function(btn)
	{
		
		var dialog=$(btn).parents('dialog');
		$(dialog).toggleClass('fullscreen');
		if(!$(dialog).hasClass('fullscreen'))
			this.dialogSetPosition(dialog);
	},
	dialogClose:function()
	{
		var dialogs=$('dialog');
		for(var n=0,nl=dialogs.length;n<nl;n++)
		{
			var dialog=dialogs[n];
			if(typeof $(dialog).get(0).showModal=='function')
			{
				if($(dialog).attr('open')!='undefined' &&
					$(dialog).attr('open')=='open')
						$(dialog).get(0).close();
			}else{
				$('div#backdrop').css('display','none');
			}
			$(dialog).css('display','none');
			if($('form',dialog).length>0) $('form',dialog).get(0).reset();	// Очищаем форму, после нажатия на CANCEL
		}
	},
	dialogCloseByKey:function(event)
	{
		var target=event.target;
		if(target.nodeName=='INPUT')
		{
			target.setCustomValidity('');
			target.checkValidity();
		}
		
		if(window.showModal=='function') return;
		if(event.keyCode==27) this.dialogClose();
		
		if(event.keyCode==13) this.checkInputComplete(target);
	},
	checkInputComplete:function(target)
	{
		if(target.nodeName=='INPUT')
		{
			if(target.name=='password')
			{
				var par=$(target).closest('#loginData');
				if(par.length==1)
				{
					this.loginGo();
				}
			}
		}
	},
	dialogSubmit:function(id)
	{
		var n,nl;
		var repDisplaying=false;
		if(typeof id=='undefined' || id=='') return;
		var mode=$('dialog#'+id).prop('mode');
		var inps=$('dialog#'+id+' input:invalid');
		if(inps.length>0)
		{
			for(n=0,nl=inps.length;n<nl;n++)
			{
				var inp=$(inps[n]).get(0);
				if(inp.validity.patternMismatch || inp.validity.valueMissing)
				{
					var elname=$(inp).attr('name');
					if(typeof err_messages=='object' && typeof err_messages[elname]!='undefined')
					{
						inp.setCustomValidity(err_messages[elname]);
					}else{
						inp.setCustomValidity('Error!');
					}
					if(!repDisplaying)
					{
						inp.reportValidity();
						repDisplaying=true;
					}
				}else{
					inp.setCustomValidity('');
				}
			}
		}else{
			if(id=='jail-rename')
			{
				var inp=$('form#jailRenameSettings input[name="jname"]');
				var jid=$(inp).val();
				if(this.isJnameExists('jailslist',jid))
				{
					inp.get(0).setCustomValidity(this.translate('This name is already exists!'));
					inp.get(0).reportValidity();
					return;
				}
				var posts=$('form#jailRenameSettings').serializeArray();
				posts.push({'name':'oldJail','value':this.renamedOldName});
				this.loadData('jailRename',$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='jail-clone')
			{
				var inp=$('form#jailCloneSettings input[name="jname"]');
				var jid=$(inp).val();
				if(this.isJnameExists('jailslist',jid))
				{
					inp.get(0).setCustomValidity(this.translate('This name is already exists!'));
					inp.get(0).reportValidity();
					return;
				}
				var posts=$('form#jailCloneSettings').serializeArray();
				posts.push({'name':'oldJail','value':this.clonedOldName});
				this.loadData('jailClone',$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='jail-settings')
			{
				var jid=$('form#jailSettings input[name="jname"]').val();
				this.trids=this.getTrIdsForCheck('jailslist');	// !!!
				if(this.trids.length>0)
				{
					if(mode!='edit' && this.trids.indexOf(jid)!=-1)
					{
						var inp=$('form#jailSettings input[name="jname"]').get(0);
						inp.setCustomValidity(this.translate('This name is already exists!'));
						inp.reportValidity();
						return;
					}
					var pass1=$('form#jailSettings input[name="user_pw_root"]').val();
					var pass2=$('form#jailSettings input[name="user_pw_root_1"]').val();
					if(pass1!=pass2)
					{
						var inp=$('form#jailSettings input[name="user_pw_root"]').get(0);
						inp.setCustomValidity(this.translate('Passwords must match!'));
						inp.reportValidity();
						return;
					}
				}
				this.tmp_jail_info[jid]={};
				this.tmp_jail_info[jid]['runasap']=$('#astart-id:checked').length>0?1:0;
				var posts=$('form#jailSettings').serializeArray();
				if(mode=='edit')
					posts.push({'name':'jname','value':jid});
				var jmode=(mode=='edit'?'jailEdit':'jailAdd');
				this.loadData(jmode,$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='bhyve-rename')
			{
				var inp=$('form#bhyveRenameSettings input[name="jname"]');
				var jid=$(inp).val();
				if(this.isJnameExists('bhyveslist',jid))
				{
					inp.get(0).setCustomValidity(this.translate('This name is already exists!'));
					inp.get(0).reportValidity();
					return;
				}
				var posts=$('form#bhyveRenameSettings').serializeArray();
				posts.push({'name':'oldJail','value':this.renamedOldName});
				this.loadData('bhyveRename',$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='bhyve-new' && $('form#bhyveSettings').length>0)
			{
				this.storageBhyveOSProfile();
				var jid=$('form#bhyveSettings input[name="vm_name"]').val();
				if(typeof this.trids!='undefined' && this.trids.length>0)
				{
					if(mode!='edit' && this.trids.indexOf(jid)!=-1)
					{
						var inp=$('form#bhyveSettings input[name="vm_name"]').get(0);
						inp.setCustomValidity(this.translate('This name is already exists!'));
						inp.reportValidity();
						return;
					}
					var port=$('form#bhyveSettings input[name="vm_vnc_port"]').val();
					if(port!=0 && (port<1025 || port >65534))
					{
						var inp=$('form#bhyveSettings input[name="vm_vnc_port"]').get(0);
						inp.setCustomValidity(this.translate('VNC Port must be in interval: 0,1025—65534!'));
						inp.reportValidity();
						return;
					}
				}
				this.tmp_jail_info[jid]={};
				this.tmp_jail_info[jid]['runasap']=0;	// исправить на реальные данные!
				var posts=$('form#bhyveSettings').serializeArray();
				if(mode=='edit') posts.push({'name':'jname','value':jid});
				var bmode=(mode=='edit'?'bhyveEdit':'bhyveAdd');
				this.loadData(bmode,$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='bhyve-obtain' && $('form#bhyveObtSettings').length>0)
			{
				var jid=$('form#bhyveObtSettings input[name="vm_name"]').val();
				this.tmp_jail_info[jid]={};
				this.tmp_jail_info[jid]['runasap']=0;	// исправить на реальные данные!
				var posts=$('form#bhyveObtSettings').serializeArray();
				this.loadData('bhyveObtain',$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='bhyve-clone')
			{
				var inp=$('form#bhyveCloneSettings input[name="vm_name"]');
				var jid=$(inp).val();
				if(this.isJnameExists('bhyveslist',jid))
				{
					inp.get(0).setCustomValidity(this.translate('This name is already exists!'));
					inp.get(0).reportValidity();
					return;
				}
				var vm_ram=$('#bhyveslist tr#'+this.clonedOldName+' .vm_ram').html();
				var vm_cpus=$('#bhyveslist tr#'+this.clonedOldName+' .vm_cpus').html();
				var vm_os_type=$('#bhyveslist tr#'+this.clonedOldName+' .vm_os_type').html();
				var posts=$('form#bhyveCloneSettings').serializeArray();
				posts.push({'name':'oldBhyve','value':this.clonedOldName});
				posts.push({'name':'vm_ram','value':vm_ram});
				posts.push({'name':'vm_cpus','value':vm_cpus});
				posts.push({'name':'vm_os_type','value':vm_os_type});
				this.loadData('bhyveClone',$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='authkey')
			{
				var posts=$('form#authkeySettings').serializeArray();
				this.loadData('authkeyAdd',$.proxy(this.onAuthkeyAdd,this),posts);
			}
			if(id=='vpnet')
			{
				var posts=$('form#vpnetSettings').serializeArray();
				this.loadData('vpnetAdd',$.proxy(this.onVpnetAdd,this),posts);
			}
			if(id=='srcget')
			{
				//this.srcVerAdd();
				var inp=$('form#srcSettings input[name="version"]');
				var id=$(inp).val();
				this.dialogClose();
				this.srcUpdate(id);
			}
			if(id=='basescompile')
			{
				var posts=$('form#basesSettings').serializeArray();
				this.loadData('basesCompile',$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='getrepo')
			{
				var posts=$('form#repoSettings').serializeArray();
				this.loadData('repoCompile',$.proxy(this.onJailAdd,this),posts);
			}
			if(id=='helpers-add')
			{
				var posts=$('form#helpersAddSettings').serializeArray();
				this.loadData('helpersAdd',$.proxy(this.onHelpersAdd,this),posts);
			}
			if(id=='users-new')
			{
				var pass1=$('form#userSettings input[name="password"]').val();
				var pass2=$('form#userSettings input[name="password1"]').val();
				if(pass1!=pass2)
				{
					var inp=$('form#userSettings input[name="password"]').get(0);
					inp.setCustomValidity(this.translate('Passwords must match!'));
					inp.reportValidity();
					return;
				}

				var fmode=(mode=='edit')?'usersEdit':'usersAdd';
				var posts=$('form#userSettings').serializeArray();
				if(mode=='edit') posts.push({'name':'user_id','value':this.lastEditedUser});
				this.loadData(fmode,$.proxy(this.onUsersAdd,this),posts);
			}
			if(id=='vm_packages-new')
			{
				var fmode=(mode=='edit')?'vmTemplateEdit':'vmTemplateAdd';
				var posts=$('form#templateSettings').serializeArray();
				if(mode=='edit') posts.push({'name':'template_id','value':this.lastEditedVmTemplate});
				this.loadData(fmode,$.proxy(this.onVmTemplateAdd,this),posts);
			}
			if(id=='image-import')
			{
				var fmode='imageImport';
				var posts=$('form#imageImportSettings').serializeArray();
				this.loadData(fmode,$.proxy(this.onImageImportStart,this),posts);
			}

		}
	},
	fillFormDataOnChange:function(data)
	{
		if(typeof data.form!='undefined')
		{
			if(typeof data.form['jname']!='undefined')
			{
				var jname=data.form['jname'];
				delete(data.form['jname']);
				for(k in data.form)
				{
					var v=data.form[k];
					$('tr#'+jname+' td.'+k).html(v);
				}
			}
		}
	},
	isJnameExists:function(table,jname)
	{
		var trs=$('#'+table+' #'+jname);
		return trs.length>0;
	},
	onJailAdd:function(data)
	{
		if(typeof data!='undefined' && !data.error)
		{
			if(typeof data.mode!='undefined')
			{
				switch(data.mode)
				{
					case 'jailEdit':
					case 'bhyveEdit':
						this.dialogClose();
						this.fillFormDataOnChange(data);
						return;break;
					case 'jailAdd': 	this.dialogClose();return;
						var table='jailslist';
						var operation='jcreate';
						break;
					case 'jailClone':
					case 'bhyveRename':
					case 'jailRename':	this.dialogClose();return;
						var table='jailslist';
						var operation='jclone';
						break;
					case 'bhyveClone':	this.dialogClose();return;
						var table='bhyveslist';
						var operation='bclone';
						break;
					case 'bhyveAdd':	this.dialogClose();return
						var table='bhyveslist';
						var operation='bcreate';
						break;
					case 'bhyveObtain':	this.dialogClose();return;
						var table='bhyveslist';
						var operation='vm_obtain';
						break;
					case 'basesCompile':	this.dialogClose();return;
						var table='baseslist';
						var operation='world';
						break;
					case 'repoCompile':	this.dialogClose();return;
						var table='baseslist';
						var operation='repo';
						break;
				}
				
				var mode='new';
				if(['basesCompile','repoCompile'].indexOf(data.mode)!=-1)
				{
					var trn=$('table#'+table+' tbody tr#'+this.dotEscape(data.jail_id));
					if(trn.length>0) mode='update';
				}
				var injected=false;
				var n,nl;
				if(data.html!='undefined' && mode=='new')	// && $('table#'+table).length<1
				{
					var trs=$('table#'+table+' tbody tr');
					for(n=0,nl=trs.length;n<nl;n++)
					{
						var tr=trs[n];
						var tid=$(tr).attr('id');
						if(data.jail_id<tid)
						{
							$(data.html).insertBefore(tr);
							injected=true;
							break;
						}
					}
					if(!injected)	//	Вставляем запись в конец таблицы
					{
						$(data.html).insertAfter(tr);
					}
				}
				if(mode=='update')
				{
					var tr=trn;
					$(tr).addClass('busy');
					$('.ops .icon-cnt span',tr).addClass('icon-spin6 animate-spin');
					$('.jstatus',tr).html(data.txt_status);
				}
				this.dialogClose();
				this.enableWait(data.jail_id);
				//this.tasks.add({'operation':operation,'jail_id':data.jail_id,'task_id':data.taskId});
				//this.tasks.start();
				// 01.03.17
				
			}
		}
	},
	onHelpersAdd:function(data)
	{
		this.dialogClose();
		
		this.loadData('getJsonPage',$.proxy(this.onLoadData,this));
	},
	onAuthkeyAdd:function(data)
	{
		if(typeof data!='undefined' && !data.error)
		{
			var injected=false;
			var n,nl;
			if(data.html!='undefined')
			{
				var trs=$('table#authkeyslist tbody tr');
				for(n=0,nl=trs.length;n<nl;n++)
				{
					var tr=trs[n];
					var keyname=$('.keyname',tr).html();
					if(data.keyname<keyname)
					{
						$(data.html).insertBefore(tr);
						injected=true;
						break;
					}
				}
				if(!injected)	//	Вставляем запись в конец таблицы
				{
					$(data.html).insertAfter(tr);
				}
			}

			this.dialogClose();
		}
	},
	onVpnetAdd:function(data)
	{
		if(typeof data!='undefined' && !data.error)
		{
			var injected=false;
			var n,nl;
			if(data.html!='undefined')
			{
				var trs=$('table#vpnetslist tbody tr');
				for(n=0,nl=trs.length;n<nl;n++)
				{
					var tr=trs[n];
					var netname=$('.netname',tr).html();
					if(data.netname<netname)
					{
						$(data.html).insertBefore(tr);
						injected=true;
						break;
					}
				}
				if(!injected)	//	Вставляем запись в конец таблицы
				{
					$(data.html).insertAfter(tr);
				}
			}

			this.dialogClose();
		}
	},
	srcVerAdd:function()
	{return;
		var n,nl;
		var posts=$('form#srcSettings').serializeArray();
		var version=$('form#srcSettings input[name="version"]').val();
		var html=src_table_pattern;
		var stable=(parseInt(version)>parseFloat(version))?'stable':'release';
		if(typeof version!='undefined')
		{
			var arr={
				'nth-num':'nth0',
				'ver':version,
				'ver1':stable,
				'node':'local',
				'rev':'—',
				'date':'—',
				'updtitle':this.translate('Update'),
				'deltitle':this.translate('Delete'),
				'maintenance':' busy',
			};
			for(key in arr)
				html=html.replace(new RegExp('#'+key+'#','g'),arr[key]);
			var trs=$('#srcslist tr');
			var injected=false;
			for(n=0,nl=trs.length;n<nl;n++)
			{
				this.dialogClose();
				var tr=trs[n];
				var tbl_ver=parseFloat($('td.version',tr).html());
				if(version<tbl_ver)
				{
					$(html).insertBefore(tr);
					this.srcUpdate('src'+version);
					injected=true;
					break;
				}
			}
			if(!injected)
			{
				//$(html).insertAfter(tr);
				if(trs.length==0)
				{
					$('table#srcslist tbody').append(html);
				}else{
					$(html).insertAfter(tr);
				}
				this.srcUpdate('src'+version);
			}
		}
	},
	
	updateBhyveOSProfile:function()
	{
		if(localStorage)
		{
			var pos=localStorage['vm_os_profile_pos'];
			if(typeof pos!='undefined')
				$('#bhyveSettings select[name="vm_os_profile"]').val(pos);
		}
		
		//if(localStorage) db_path=localStorage[var_name];
	},
	storageBhyveOSProfile:function()
	{
		if(localStorage)
		{
			var pos=$('#bhyveSettings select[name="vm_os_profile"]').val();
			localStorage['vm_os_profile_pos']=pos;
		}
	},
	updateBhyveISO:function()
	{
		this.loadData('updateBhyveISO',$.proxy(this.onUpdateBhyveISO,this));
	},
	onUpdateBhyveISO:function(data)
	{
		if(typeof data.iso_list!='undefined')
		{
			$('dialog #bhyveSettings select[name="vm_iso_image"]').html(data.iso_list);
		}
	},
	getFreeJname:function()
	{
		this.loadData('freejname',$.proxy(this.onGetFreeJname,this));
	},
	onGetFreeJname:function(data)
	{
		$('dialog#jail-settings input[name="jname"]').val(data.freejname);
		$('dialog#jail-settings input[name="host_hostname"]').val(data.freejname+'.my.domain');
	},
	
	onUsersAdd:function(data)
	{
		if(typeof data.error!='undefined')
		{
			if(data.error)
			{
				if(data.errorType=='user-exists')
				{
					var inp=$('form#userSettings input[name="username"]').get(0);
					inp.setCustomValidity(this.translate('This name is already exists!'));
					inp.reportValidity();
					return;
				}
			}else{
				if(typeof data.res!='undefined')
				{
					var res=data.res;
					if(res.error)
					{
						alert('SQL error: ' + res.info[2]);
						return;
					}
				}
				this.dialogClose();
				
				this.wssReload();
				this.dataReload();
			}
		}
	},
	onVmTemplateAdd:function(data)
	{
		this.dialogClose();
		this.wssReload();
		this.dataReload();
	},
	
	loadData:function(mode,return_func,arr,spinner)
	{
		if(spinner!==false) $('.spinner').show();
		var path='/json.php';
		var db_path=this.getDbPath();
		var posts={'mode':mode,'path':location.pathname,'hash':window.location.hash,'db_path':db_path};
		if(typeof arr=='object')
		{
			posts['form_data']={};
			for(n=0,nl=arr.length;n<nl;n++)
				posts['form_data'][arr[n]['name']]=arr[n]['value'];
		}
		$.post(path,posts,
			$.proxy(function(data){this.onLoadDataAuthorize(return_func,data);$('.spinner').hide();},this)	//return_func(data)
		);
	},
	onLoadDataAuthorize:function(return_func,data)
	{
		try{
			var data=JSON.parse(data);
		}catch(e){this.debug(e.message,data);return;}
		
		if(data==null) return;
		
		if(typeof data['unregistered_user']!='undefined')
		{
			this.loginFadeIn();
			return;
		}

		if(typeof data.error!='undefined')
		{
			if(data.error && typeof(data.error_message)!='undefined')
			{
				this.notify(data.error_message,'error');
				return;
			}
		}
		
		return_func(data);
	},
	
/* 	loadData1:function()
	{
		if(!this.jsonLoad) return;
		var file='/json.php';	//'/pages'+path+'a.json.php';
		this.loadDataJson(file,$.proxy(this.onLoadData,this),{'path':location.pathname,'mode':'getJsonPage'});
	},
	loadDataJson:function(file,return_func,arr)	//mode,
	{
		var posts=arr;
/-*
		var posts={'mode':mode,'project':this.project,'jail':this.jail,'module':this.module};
		if(typeof arr=='object')
		{
			posts['form_data']={};
			for(n=0,nl=arr.length;n<nl;n++)
				posts['form_data'][arr[n]['name']]=arr[n]['value'];
		}
*-/
		$.post(file,posts,
			$.proxy(function(data){return_func(data);},this));	
	}, */
	onLoadData:function(data)
	{
		if(data.error)
		{
			var t=$('tbody.error td.error_message');
			if(typeof(data.error_message)!='undefined' && data.error_message!='') t.html(data.error_message);
			$(t).parents('table').addClass('error');
		}else{
			if(isset(data.template)) this.template=data.template;
			if(isset(data.protected)) this.tpl_protected=data.protected;
			if(typeof data.func!='undefined')
			{
				this[data.func](data);
				return;
			}
			for(id in data) $('#'+id).html(data[id]);
			
			var razd=location.pathname;
			if(['/overview/'].indexOf(razd)!=-1)
			{
				this.createGraphs();
			}
		}
	},
	
	fillTable:function(data)
	{
		if(typeof data.id!='undefined')
		{
//			$('#'+data.id+' thead').html(data.thead);
			$('#'+data.id+' tbody').html(data.tbody);
		}
		if(typeof data.tasks!='undefined')
		{
			if(data.tasks!=null)
			{
				for(var t in data.tasks)
				{
					var task=data.tasks[t];
					var status=task.status;
					if(typeof task.task_cmd!='undefined')
					{
						var txt_status=task.txt_status;
						//this.tasks.add({'operation':task.task_cmd,'jail_id':t,'status':status,'task_id':task.task_id,'txt_status':txt_status});
						$('tr#'+this.dotEscape(t)+' .jstatus').html(this.translate(txt_status));
						this.enableWait(t);
					}
				}
				
				//this.tasks.context=this;
				//this.tasks.start();
				// 01.03.17
			}
		}
		
		if(typeof data.helpers_list!='undefined')
		{
			$('dialog#helpers-add div.window-content').html(data.helpers_list);
		}
		// ---
		var arr=[];
		var trs=$('table#'+data.id+' tbody tr');
		if(trs.length>1)
		{
			for(n=0;n<trs.length;n++) arr[n]=$(trs[n]).attr('id');
			var res=this.alphanumSort(arr,true);
		}
		//debugger;
		// ---

		// Если мы в нужной таблице, то рисуем графики
		if(['bhyveslist','jailslist'].indexOf(data.id)!=-1)
		{
			this.createGraphs();
		}
	},
	
	fillTab:function(data)
	{
		if(typeof data.html!='undefined')
		{
			$('#tab1').hide();
			$('#tab2').html(data.html);
		}
	},
	
	enableWait:function(id,empty)
	{
		if(typeof empty=='undefined') empty=false;
		$('#'+this.dotEscape(id)).addClass('busy');
		var icon_cnt=$('tr#'+this.dotEscape(id)).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		if(!empty)
		{
			$(icon).removeClass('icon-play');
			$(icon).removeClass('icon-stop');
		}
		$(icon).addClass('icon-spin6 animate-spin');
	},
	enablePlay:function(id)
	{
		var icon_cnt=$('tr#'+this.dotEscape(id)).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		if(icon[0]) icon[0].className='icon-play';
	},
	enableStop:function(id)
	{
		var icon_cnt=$('tr#'+this.dotEscape(id)).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		if(icon[0]) icon[0].className='icon-stop';
	},
	enableRip:function(id)
	{
		var icon_cnt=$('tr#'+this.dotEscape(id)).find('span.icon-cnt');
		if(typeof icon_cnt!='undefined' && icon_cnt.length>0)
		{
			var icon=$(icon_cnt).find('span');
			if(typeof icon!='undefined')
				icon[0].className='icon-emo-cry';
		}
	},
	enableClear:function(id)
	{
		var icon_cnt=$('tr#'+this.dotEscape(id)).find('span.icon-cnt');
		if(typeof icon_cnt!='undefined' && icon_cnt.length>0)
		{
			var icon=$(icon_cnt).find('span');
			if(typeof icon!='undefined')
				icon[0].className='';
		}
	},
	
	jailStart:function(obj,opt)
	{
		if(typeof opt=='undefined') opt='jail';
		if(!obj) return;
		var id=this.getJailId(obj);
		
		var op1='jstart';var op2='jstop';
		if(opt=='bhyve') {op1='bstart';op2='bstop';}
		
		var icon_cnt=$(obj).find('span.icon-cnt');
		var icon=$(icon_cnt).find('span');
		op='';
		if($(icon).hasClass('icon-play')) op=op1;	//'jstart';
		if($(icon).hasClass('icon-stop')) op=op2;	//'jstop';
		this.enableWait(id);
		
//		var op_status=(op==op1?1:0);	//'jstart'
		
		if(op!='')
		{
			//this.tasks.add({'operation':op,'jail_id':id});
			//this.tasks.start();
			var posts=[{'name':'operation','value':op},{'name':'jname','value':id}];
			if(typeof this.commands[op]!='undefined')
			{
				this.loadData(this.commands[op]['cmd'],$.proxy(this.onJailStart,this),posts,false);
				$('tr#'+id+' .jstatus').html(this.translate(this.commands[op]['stat'][1]));
			}
		}
	},
	onJailStart:function(){},
	
	jailRestart:function(id,opt)
	{
		if(typeof opt=='undefined') opt='jail';
		var op='jrestart';
		var txt='jail';
		if(opt=='bhyve'){op='brestart';txt='virtual machine';}
		var c=confirm(this.translate('You want to restart selected '+txt+'! Are you sure?'));
		if(!c) return;
		this.enableWait(id);
		var posts=[{'name':'operation','value':op},{'name':'jname','value':id}];
		if(typeof this.commands[op]!='undefined')
		{
			this.loadData(this.commands[op]['cmd'],$.proxy(this.onJailStart,this),posts,false);
			$('tr#'+id+' .jstatus').html(this.translate(this.commands[op]['stat'][1]));
		}

		//this.tasks.add({'operation':op,'jail_id':id});	//'jrestart'
		//this.tasks.start();
	},
	jailRemove:function(id,opt)
	{
		if(typeof opt=='undefined') opt='jail';
		var op='jremove';
		var txt='jail';
		if(opt=='bhyve'){op='bremove';txt='virtual machine';}
		var name=$('#'+id+' td.jname').html();
		if(this.removeConfirm(id,'You want to delete selected '+txt+': «'+name+'»! Are you sure?')===false) return;
		//var c=confirm(this.translate('You want to delete selected '+txt+'! Are you sure?'));
		//if(!c) return;
		this.enableWait(id);
		// ---
		var posts=[{'name':'operation','value':op},{'name':'jname','value':id}];
		if(typeof this.commands[op]!='undefined')
		{
			this.loadData(this.commands[op]['cmd'],$.proxy(this.onJailStart,this),posts,false);
			$('tr#'+id+' .jstatus').html(this.translate(this.commands[op]['stat'][1]));
		}
		// ---
		//this.tasks.add({'operation':op,'jail_id':id});	//'jremove'
		//this.tasks.start();
	},
	
	
	getJailId:function(obj)
	{
		var id=-1;
		id=$(obj).attr('id');
		return id;
		/*
		var cl=obj[0].className;
		var rx=new RegExp(/id-(\d+)/);
		if(res=cl.match(rx)) id=res[1];
		return id;
		*/
	},
	getJailById:function(id)
	{
		var nl=0,n=0;
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			if(this.jailsList[n].id==id) return this.jailsList[n];
		}
	},
	getJailNumById:function(id)
	{
		for(n=0,nl=this.jailsList.length;n<nl;n++)
		{
			if(this.jailsList[n].id==id) return n;
		}
	},
	getModuleById:function(id)
	{
		id=this.getJailId(id);
		var nl=0,n=0;
		for(n=0,nl=this.modulesList.length;n<nl;n++)
		{
			if(this.modulesList[n].id==id) return this.modulesList[n];
		}
	},
	playButt2Update:function(id)
	{
		if(!id) return;
		
		
		
		return;
		//if(!this.jail) return;
		//var jail=this.getJailById(this.jail);
		/*
		if(typeof jail.status!='undefined')
		{
			var status=jail.status;
			if(status==0)
			{
				this.playButt2Status('icon-play',this.translate('run jail'));
				var status='off';
				var status_txt='Jail is not launched';
			}else{
				this.playButt2Status('icon-stop',this.translate('stop jail'));
				var status='on';
				var status_txt='Jail is launched';
			}
			$('#left-menu .jail'+this.jail+'.status').removeClass('on off').addClass(status).attr('title',status_txt);
		}
		*/
	},
	playButt2Status:function(icon,txt)
	{
		$('#play-but-2 .ico').removeClass('icon-play icon-stop icon-attention').addClass('ico '+icon);
		$('#play-but-2 .txt').html(txt);
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
		
		add:function(vars)	//,arr
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
			
			/*
			if(typeof arr!='undefined')
			{
				this.tasks['vars']=arr;
			}
			*/
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
			this.context.loadData('getTasksStatus',$.proxy(this.update,this),[{'name':'jsonObj','value':vars}],false);
		},
		
		update:function(data)
		{
/* 			if(typeof data['mod_ops']!='undefined')
			{
				var key='mod_ops';
				this.tasks[key]=data[key];
				var d=data[key];
				
				if(d.status==2)
				{
					//this.context.onTaskEnd(this.tasks[key],key);
					//this.context.modulesUpdate(data);
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
				
			} */
			
/* 			if(typeof data['proj_ops']!='undefined')
			{
				if(data['proj_ops']=='projDelete')
				{
					if(typeof data.projects!='undefined')
						this.context.projectsList=data.projects;
					this.context.showProjectsList();
					return;
				}
			} */
			
			for(key in data)
			{
				$('tr#'+this.context.dotEscape(key)+' .jstatus').html(data[key].txt_status);
				var errmsg=$('tr#'+this.context.dotEscape(key)+' .errmsg');
				if(typeof data[key].errmsg!='undefined')
				{
					//$(errmsg).html('<span class="label">Error:</span>'+data[key].errmsg);
					$('tr#'+this.context.dotEscape(key)).removeClass('busy').addClass('s-off');
					this.tasks[key].errmsg=data[key].errmsg;
				}
				this.tasks[key].operation=data[key].operation;
				this.tasks[key].task_id=data[key].task_id;
				this.tasks[key].status=data[key].status;
				this.tasks[key].txt_status=data[key].txt_status;
				
				if(data[key].status==2)
				{
					if(data[key].new_html!='undefined')
					{
						this.tasks[key].new_html=data[key].new_html;
					}
					this.context.onTaskEnd(this.tasks[key],key);
					delete this.tasks[key];
				}
				
				if(data[key].status==0)
				{
					this.context.wssend({
						'cmd':'update',
						'jail_id':key,
						'status':data[key].status,
						'task_id':data[key].task_id,
						'operation':data[key].operation,
						'status':data[key].txt_status,
						'path':location.pathname,
					},'system');
				}
			}
			
			this.checkTasks=false;
			
			if(this.interval===null)
			{
				this.interval=setInterval($.proxy(this.start,this),1000);
			}
			
		},
	},
	onTaskEnd:function(task,id)
	{
		if(typeof task.errmsg!='undefined' && id!='mod_ops')
		{
			if(['srcup','removebase','world','repo'].indexOf(task.operation)!=-1)
				this.enableClear(id);
			else
				this.enablePlay(id);
			this.notify(task.errmsg,'error');
			
		//	Если ошибка при создании новой записи в таблице, то удаляем её через N секунд
			if(['bcreate','vm_obtain','srcup'].indexOf(task.operation)!=-1)
			{
				setTimeout(function(id){$('#'+clonos.dotEscape(id)).remove();},5000,id);
			}
		}else{
			switch(task.operation)
			{
				case 'jcreate':
				case 'bcreate':
				case 'vm_obtain':
				case 'jclone':
				case 'bclone':
					var disp='s-off';
					if(typeof this.tmp_jail_info[id]!='undefined')
					{
						var runasap=this.tmp_jail_info[id]['runasap'];
						if(runasap==1) disp='s-on';
					}
					if(task.operation=='jcreate') disp='s-on';	// сделать зависимым от параметра в форме!
					if(task.new_html!='undefined')
					{
						$('#'+this.dotEscape(id)).html(task.new_html);
					}

					$('#'+id).removeClass('s-off').removeClass('s-on')
					$('#'+id).addClass(disp).removeClass('busy').removeClass('maintenance');
					this.enablePlay(id);
					//this.playButt2Update(id);
					break;
				case 'jstart':
				case 'jrestart':
				case 'bstart':
				case 'brestart':
					$('#'+id).removeClass('s-off').addClass('s-on').removeClass('busy');
					$('#'+id+' td.jstatus').html(this.translate(task.txt_status));
					this.enableStop(id);
					//this.playButt2Update(id);
					break;
				case 'jstop':
				case 'bstop':
					$('#'+id).removeClass('s-on').addClass('s-off').removeClass('busy');
					$('#'+id+' td.jstatus').html(this.translate(task.txt_status));
					this.enablePlay(id);
					//this.playButt2Update(id);
					break;
				case 'jedit':
					this.enableStop(id);
					break;
				case 'jremove':
				case 'bremove':
				case 'removesrc':
				case 'removebase':
					$('#'+this.dotEscape(id)+' td.jstatus').html(this.translate(task.txt_status));
					this.enableRip(id);
					window.setTimeout($.proxy(this.deleteItemsOk,this,id),2000);
					break;
				case 'srcup':
				case 'world':
				case 'repo':
					if(task.new_html!='undefined')
					{
						$('#'+this.dotEscape(id)).html(task.new_html);
					}
					$('#'+this.dotEscape(id)).removeClass('s-off').addClass('s-on').removeClass('busy');
					$('#'+this.dotEscape(id)+' td.jstatus').html(this.translate(task.txt_status));
					this.enableClear(id);
					break;
				case 'jexport':
					this.enablePlay(id);
					break;
				case 'jimport':
					this.enablePlay(id);
					break;
/*				case 'jclone':
					var num=this.getJailNumById(id);
					var j=this.jailsList[num];
					if(typeof j.task_status!='undefined')
					{
						var status=j.task_status;
						if(status==0)
							this.enablePlay(id);
						else
							this.enableStop(id);
					}
					if(typeof j.new_ip!='undifined')
					{
						$('.jails tr.id-'+id+' .jip').html(j.new_ip);
					}
					this.currentPage='jails';
					break;
*/
/*
				case 'modremove':
				case 'modinstall':
					this.modulesUpdate(task);
					break;
*/
				case 'sstart':
					this.enableStop(id);
					break;
				case 'sstop':
					this.enablePlay(id);
					break;
			}
		}
	},
	
	deleteItemsOk:function(id)
	{
		var tr=$('#'+this.dotEscape(id));
		if(tr.length<1) return;
		var table=$(tr).closest('table');
		if(table && tr)
		{
			table[0].deleteRow(tr[0].rowIndex);
		}
	},
	
	sleepFor:function(sleepDuration)
	{
		var now=new Date().getTime();
		while(new Date().getTime() < now + sleepDuration){ /* do nothing */ } 
	},
	removeConfirm:function(id,answer)
	{
		if(typeof id!='undefined')
		{
			var obj=$('#'+id);
			if(typeof obj!='undefined')
			{
				$(obj).addClass('del');
				var c=confirm(this.translate(answer));
				if(!c)
				{
					$(obj).removeClass('del');
					return false;
				}
				return true;
			}
		}
		return false;
	},
	
	authkeyRemove:function(id)
	{
		if(this.removeConfirm(id,'You want to delete selected authkey! Are you sure?')===false) return;
		var posts=[{'name':'auth_id','value':id}];
		this.loadData('authkeyRemove',$.proxy(this.onAuthkeyRemove,this),posts);
	},
	onAuthkeyRemove:function(data)
	{
		$('#authkeyslist tr#'+data.auth_id).remove();
	},
	
	vpnetRemove:function(id)
	{
		if(this.removeConfirm(id,'You want to delete selected network! Are you sure?')===false) return;
		var posts=[{'name':'vpnet_id','value':id}];
		this.loadData('vpnetRemove',$.proxy(this.onVpnetRemove,this),posts);
	},
	onVpnetRemove:function(data)
	{
		$('#vpnetslist tr#'+data.vpnet_id).remove();
	},
	
	mediaRemove:function(id)
	{
		if(this.removeConfirm(id,'You want to delete selected storage media! Are you sure?')===false) return;
		var posts=[{'name':'media_id','value':id}];
		this.loadData('mediaRemove',$.proxy(this.onMediaRemove,this),posts);
	},
	onMediaRemove:function(data)
	{
		$('#mediaslist tr#'+data.media_id).remove();
	},
	
	srcRemove:function(id)
	{
		if(this.removeConfirm(id,'You want to delete selected FreeBSD sources! Are you sure?')===false) return;
		var ver=$('#srcslist tr#'+this.dotEscape(id)+' .version').html();
		var op='removesrc';
		//this.enableWait(id);
		//this.tasks.add({'operation':op,'jail_id':id});
		//this.tasks.start();
		var posts=[{'name':'operation','value':op},{'name':'jname','value':id}];
		if(typeof this.commands[op]!='undefined')
			this.loadData(this.commands[op]['cmd'],$.proxy(this.onJailStart,this),posts,false);
	},
	srcUpdate:function(id,vers)
	{
		if(typeof vers=='undefined') vers='stable';
		var ver=$('#srcslist tr#'+this.dotEscape(id)+' .version').html();
		var op='srcup';
		//this.tasks.add({'operation':op,'jail_id':id});
		//this.tasks.start();
		var posts=[{'name':'operation','value':op},{'name':'jname','value':id}];
		if(typeof this.commands[op]!='undefined')
			this.loadData(this.commands[op]['cmd'],$.proxy(this.onJailStart,this),posts,false);
	},
	baseRemove:function(id)
	{
		if(this.removeConfirm(id,'You want to delete selected FreeBSD bases! Are you sure?')===false) return;
		var ver=$('#baseslist tr#'+this.dotEscape(id)+' .version').html();
		var op='removebase';
		//this.enableWait(id);
		//this.tasks.add({'operation':op,'jail_id':id});
		//this.tasks.start();
		var posts=[{'name':'operation','value':op},{'name':'jname','value':id}];
		if(typeof this.commands[op]!='undefined')
		{
			this.loadData(this.commands[op]['cmd'],$.proxy(this.onJailStart,this),posts,false);
			$('tr#'+id+' .jstatus').html(this.translate(this.commands[op]['stat'][1]));
		}
	},
	
	userRemove:function(id)
	{
		if(this.removeConfirm(id,'You want to delete selected CBSD user! Are you sure?')===false) return;
		var posts=[{'name':'user_id','value':id}];
		this.loadData('userRemove',$.proxy(this.onUserRemove,this),posts,false);
	},
	onUserRemove:function(data)
	{
		this.wssReload();
		this.dataReload();
	},
	
	vmTemplateRemove:function(id)
	{
		if(this.removeConfirm(id,'You want to delete selected template! Are you sure?')===false) return;
		var posts=[{'name':'template_id','value':id}];
		this.loadData('vmTemplateRemove',$.proxy(this.onVmTemplateRemove,this),posts,false);
	},
	onVmTemplateRemove:function(data)
	{
		this.wssReload();
		this.dataReload();
	},

	
	logOpen:function(id)
	{
		$('#taskloglist tr.sel').removeClass('sel');
		$('#taskloglist tr#'+id).addClass('sel');
		var posts=[{'name':'log_id','value':id}];
		this.loadData('logLoad',$.proxy(this.onLogLoad,this),posts);
	},
	onLogLoad:function(data)
	{
		$('dialog#tasklog .window-content').html(data.html);
		this.dialogShow1('tasklog');
	},
	logFlush:function()
	{
		this.loadData('logFlush',$.proxy(this.onLogFlush,this));
	},
	onLogFlush:function(data)
	{
		$('#taskloglist tbody').html('');
		this.wssend({'cmd':'reload','path':location.pathname},'system');
	},
	
	bodyClick:function(event)
	{
		//debugger;
		var target=event.target;
		if($(target).parents('form').length>0)
		{
			var cl=$(target).attr('class');
			var form=$(target).parents('form').get(0);
			if($(form).hasClass('helper'))
			{
				switch(cl)
				{
					case 'clear-helper':
						this.clearHelperForm(target);
						break;
					case 'save-helper-values':
						this.saveHelperValues(form);
						break;
					case 'fgroup-del-butt':
						var parent=$(target).closest('fieldset');
						var id=$(parent).attr('id');
						this.deleteHelperGroup(form,id);
						break;
					case 'fgroup-add-butt':
						this.addHelperGroup(form);
						break;
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
			
			if(cl=='letsedit')
			{
				var chkd=$(target).prop('checked');
				var efs=$(target).closest('fieldset');
				var inps=$('input[type="password"]',efs);
				for(var n=0;n<inps.length;n++)
					$(inps[n]).prop('disabled',!chkd);
			}
			
			return;
		}
		var elid=$(target).attr('id');
		
		/* ловим клики по выпадающему меню */
		if(typeof elid!='undefined')
		{
			switch(elid)
			{
				case 'jddm-edit':
				case 'jddm-clone':
				case 'jddm-rename':
				case 'jddm-helpers':
				case 'jddm-export':
					this.DDMenuSelect(elid);
					return;break;
			}
		}
		
		var outer=$(target).parents('.vnc-wait');
		if(outer.length)
		{
			$(outer).hide();
			return;
		}
		/* --- */
		
/*  		if(target.id=='main_chkbox')
		{
			this.mainChkBoxClick(event);
			return;
		} */
		
		var td=$(target).closest('td');
		td=td[0];
		var tr=$(target).closest('tr');
		var trc=$(tr).attr('class');
		var trid=$(tr).attr('id');
		var tbl=$(tr).closest('table');
		var tblid=$(tbl).attr('id');
		
if(tblid=='jailslist'){
	if(td==$(tr).children()[0] || td==$(tr).children()[1])
	{
		var e=$(tr).parents('div.main');if(e){$(e).toggleClass('asplit');}
		this.openedJailSummary=trid;
	}
}
		
		var opt='jail';
		if(tblid=='bhyveslist') opt='bhyve';

		var cl=target.className;
		switch(cl)
		{
			case 'icon-cancel':
				if(tblid=='authkeyslist')
				{
					this.authkeyRemove(trid);
					return;
				}
				if(tblid=='vpnetslist')
				{
					this.vpnetRemove(trid);
					return;
				}
				if(tblid=='mediaslist')
				{
					this.mediaRemove(trid);
					return;
				}
				if(tblid=='baseslist')
				{
					this.baseRemove(trid);
					return;
				}
				if(tblid=='srcslist')
				{
					this.srcRemove(trid);
					return;
				}
				if(tblid=='userslist')
				{
					this.userRemove(trid);
					return;
				}
				if(tblid=='jailslist' || tblid=='bhyveslist')
				{
					this.jailRemove(trid,opt);
					return;
				}
				if(tblid=='packageslist')
				{
					this.vmTemplateRemove(trid);
					return;
				}
				if(tblid=='impslist')
				{
					this.imageRemove(trid);
					return;
				}
				alert(tblid);
				return;break;
			case 'icon-arrows-cw':
				if(tblid=='srcslist')
				{
					this.srcUpdate(trid);
					return;
				}
				this.jailRestart(trid,opt);
				return;break;
			case 'icon-desktop':
				$('.vnc-wait').show();
				clearTimeout(this.coundown);
				this.countdown_seconds=10;
				this.vnc_countdown();
				this.dialogShow(trid,'small');
				return;break;
			case 'icon-cog':
				this.DDMenuShow(trid,td,tr,event);
				return;break;
				
			case 'close-but':
				this.dialogClose();
				return;break;
			case 'btn-openlog':
				this.logOpen(trid);
				return;break;
			case 'icon-edit':
				switch(tblid)
				{
					case 'userslist':
						this.userEdit(trid,tblid);
						return;
					case 'packageslist':
						this.vmTemplateEdit(trid,tblid);
						return;
				}
				
				return;break;
			case 'icon-download':
				if(tblid=='impslist')
				{
					this.imageDownload(trid,tblid);
				}
				return;break;
			case 'icon-export':
				if(tblid=='impslist')
				{
					this.imageImport(trid,tblid);
				}
				return;break;
		}
		
		if(cl.indexOf('cancel-but')>-1)
		{
			this.dialogClose();
			return;
		}
		if(cl.indexOf('ok-but')>-1)
		{
			var did=$(target).closest('dialog').attr('id');
			if(typeof did!='undefined') this.dialogSubmit(did);
			return;
		}
		
		if(cl.indexOf('top-button')>-1)
		{
			if(cl.indexOf('id:')>-1)
			{
				var bid=cl.match(/id:([^\s]+)/);
				if(bid!=null)
				{
					switch(bid[1])
					{
						case 'flushlog':
							this.logFlush();
							return;break;
					}
				}
				this.dialogOpen(event);
				return;
			}
		}

		if(tblid=='instanceslist' || tblid=='helperslist')
		{
			location.hash='#'+trid;
			return;
		}
		
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
		if(!td || typeof td.className=='undefined') return;
		var tdc=td.className;
		tdc=tdc.replace(' ','-');
		
		switch(tdc)
		{
			case 'ops':
				this.jailStart(tr,opt);
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
	
	vnc_countdown:function()
	{
		$('#vnc-countdown').html(this.countdown_seconds--);
		this.coundown=setTimeout($.proxy(this.vnc_countdown,this),1000);
		if(this.countdown_seconds<0)
		{
			$('.vnc-wait').hide();
			clearTimeout(this.coundown);
			this.countdown_seconds=10;
		}
	},
	
	onChangePkgTemplate:function(obj,event)
	{
		var id=$(obj).val();
		var index=$(obj).prop('selectedIndex');
		var txt=$('option:selected',obj).text();
		var res=txt.match(new RegExp(/cpu:[ ]*(\d+).*ram:[ ]*([\dmg]+).*hdd:[ ]*([\dmg]+)/));
		if(res!=null)
		{
			var par=$(obj).closest('form');
			$('input[name="vm_cpus"]',par).val(res[1]);
			$('input[name="vm_cpus_show"]',par).val(res[1]);
			$('input[name="vm_ram"]',par).val(res[2]);
			$('input[name="vm_size"]',par).val(res[3]);
		}
	},
	
	loginAction:function(event)
	{
		var target=event.target;
		var cl=$(target).attr('class');
		if(typeof cl=='undefined') return;
		var res=cl.match(new RegExp(/ok-but/));
		if(res==null) return;
		
		this.loginGo();
	},
	loginGo:function()
	{
		$('.login-wait').show();
		this.loadData('login',$.proxy(this.onLogin,this),
				[{'name':'login','value':$('#loginData input[name="login"]').val()},
				 {'name':'password','value':$('#loginData input[name="password"]').val()}]
			);
		$('#loginData input[name="password"]').val('');
	},
	onLogin:function(data)
	{
		$('.login-wait').hide();
		if(typeof data.errorCode!=='undefined')
		{
			if(data.errorCode==1)
			{
				$('.login-error-nouser').show();
				setTimeout(function(){$('.login-error-nouser').hide();},3000);
				return;
			}
			if(data.errorCode==0)
			{
				$('.login-area').fadeOut(200);
				$('#user-login').html(data.username);
				this.dataReload();
			}
		}
		
		//this.loginFadeOut();
	},
	loginFadeOut:function()
	{
		setTimeout(function(){$('.login-area').fadeOut(200);},2000);
		
		//$('.login-area').removeClass('fadeIn').addClass('fadeOut');
		//setTimeout(function() { $('.login-area').hide(); }, 400);
	},
	loginFadeIn:function()
	{
		$('#login').show();
		$('.login-wait').hide();
		$('.login-area').fadeIn(200);
		$('#login').find('input[type=text]').filter(':visible:first').focus();
		
		//$('#loginData').find('input[name="login"]').filter(':visible:first').focus();
		
		//$('.login-area').show();
		//$('.login-area').removeClass('fadeOut').addClass('fadeIn');
	},
	logout:function()
	{
		document.cookie='mhash=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;'
		
		$('#user-login').html('guest');
		this.loginFadeIn();
	},
	
	ddmenu_interval:null,
	cnt_mode:'new',
	DDMenuShow:function(id,td,tr,event)
	{
		$(tr).addClass('sel');
		var coords=$(td).position();
		var menu=$('div#config-menu');
		var ccoords=$('div#content').position();
		var lpad=parseInt($(td).css('padding-left'),10);
		var tpad=parseInt($(td).css('padding-top'),10);
		if(menu.length>0)
		{
			$(menu).css({
				'left':coords.left+lpad/2-3,
				'top':coords.top+$('div#content').scrollTop()+tpad/2,
				'display':'block',
			});
		}
		
		var table_id=$(tr).closest('table').attr('id');
		$(menu).prop('calEl',{'table_id':table_id,'id':id,'tr':tr});
		
		//this.test='test context';
		$(menu).off('mouseleave');
		$(menu).off('mouseenter');
		$(menu).on('mouseleave',$.proxy(function(){
			this.ddmenu_interval=setInterval($.proxy(this.DDMenuClose,this),2000);
			$(document).off('click',$.proxy(this.DDMenuClose,this));
			$(document).on('click',$.proxy(this.DDMenuClose,this));
		},this));
		$(menu).on('mouseenter',$.proxy(function(){
			clearInterval(this.ddmenu_interval);
		},this));
	},
	DDMenuClose:function()
	{
		$('table tr.sel').removeClass('sel');
		var menu=$('div#config-menu');
		$(menu).css('display','none');
		clearInterval(this.ddmenu_interval);
		$(document).off('click',$.proxy(this.DDMenuClose,this));
		$(menu).off('mouseleave',$.proxy(this.DDMenuClose,this));
	},
	DDMenuSelect:function(elid)
	{
		var dt=$('div#config-menu').prop('calEl');
		if(!dt)return;
		var id=dt.id;
		var table_id=dt.table_id;
		var preloadVars=false;
		
		this.DDMenuClose();
		
		switch(table_id)
		{
			case 'jailslist':
				switch(elid)
				{
					case 'jddm-edit':
						var dialog='jail-settings';
						var mode='jailEditVars';
						this.cnt_mode='edit';
						preloadVars=true;
						break;
					case 'jddm-clone':
						var dialog='jail-clone';
						var mode='jailCloneVars';
						this.cnt_mode='new';
						this.clonedOldName=dt.id;
						//$('dialog#jail-clone input[name="jname"]').val(dt.id+'clone');
						if($(dt.tr).hasClass('s-on'))
						{
							$('dialog#jail-clone .warning').show();
						}else{
							$('dialog#jail-clone .warning').hide();
						}
						preloadVars=true;
						break;
					case 'jddm-rename':
						var dialog='jail-rename';
						var mode='jailRenameVars';
						this.cnt_mode='new';
						if($(dt.tr).hasClass('s-on'))
						{
							$('dialog#jail-rename .warning').show();
						}else{
							$('dialog#jail-rename .warning').hide();
						}
						preloadVars=true;
						this.renamedOldName=dt.id;
						break;
					case 'jddm-helpers':
						//elid
						location.href='/jailscontainers/'+id+'/';
						return;
						break;
					case 'jddm-export':
						this.imageExport(id,table_id);
						return;
						break;
				}
				break;
			case 'bhyveslist':
				switch(elid)
				{
					case 'jddm-edit':
						var dialog='bhyve-new';
						var mode='bhyveEditVars';
						preloadVars=true;
						this.cnt_mode='edit';
						break;
					case 'jddm-clone':
						var dialog='bhyve-clone';
						var mode='bhyveClone';
						this.clonedOldName=dt.id;
						this.cnt_mode='new';
						$('dialog#bhyve-clone input[name="vm_name"]').val(dt.id+'clone');
						if($(dt.tr).hasClass('s-on'))
						{
							$('dialog#bhyve-clone .warning').show();
						}else{
							$('dialog#bhyve-clone .warning').hide();
						}
						break;
					case 'jddm-rename':
						var dialog='bhyve-rename';
						var mode='bhyveRenameVars';
						this.cnt_mode='new';
						if($(dt.tr).hasClass('s-on'))
						{
							$('dialog#bhyve-rename .warning').show();
						}else{
							$('dialog#bhyve-rename .warning').hide();
						}
						preloadVars=true;
						this.renamedOldName=dt.id;
						break;
				}
				break;
		}
		
		if(preloadVars)
		{
			var posts=[{'name':'jail_id','value':id},{'name':'dialog','value':dialog},{'name':'elid','value':elid}];
			this.loadData(mode,$.proxy(this.onDDMenuLoad,this),posts);
		}else{
			this.dialogShow1(dialog);
		}
	},
	onDDMenuLoad:function(data)
	{
		if(typeof data.error!='undefined')
		{
			if(data.error)
			{
				if(typeof data.reload!='undefined')
				{
					if(data.reload) this.dataReload();
						//this.loadData('getJsonPage',$.proxy(this.onLoadData,this));
				}
				return;
			}
		}
		
		var dialog=data.dialog;
		this.fillDialogVars(dialog,data.vars);
		if(data.dialog=='bhyve-new' && typeof data.iso_list!='undefined')
			$('dialog#bhyve-new select[name="vm_iso_image"]').html(data.iso_list);
		this.dialogShow1(dialog,this.cnt_mode);
		
/*
 		var dt=$('div#config-menu').prop('calEl');
		if(!dt)return;
		var table_id=dt.table_id;
		var id=dt.id;
		var tr=dt.tr;
*/

	},
	
	userEdit:function(user_id,tblid)
	{
		var mode='userEditInfo';
		var posts=[{'name':'tbl_id','value':tblid},{'name':'dialog','value':'users-new'},{'name':'user_id','value':user_id}];
		this.loadData(mode,$.proxy(this.onUserEdit,this),posts);
	},
	onUserEdit:function(data)
	{
		var dialog=data.dialog;
		$('dialog#'+dialog+' fieldset.edit input[type="password"]').prop('disabled',true);
		this.fillDialogVars(dialog,data.vars);
		this.lastEditedUser=data.user_id;
		this.dialogShow1(dialog,'edit');
	},
	
	vmTemplateEdit:function(template_id,tblid)
	{
		var mode='vmTemplateEditInfo';
		var posts=[{'name':'template_id','value':template_id}];
		this.loadData(mode,$.proxy(this.onVmTemplateEdit,this),posts);
		
	},
	onVmTemplateEdit:function(data)
	{
		var dialog='vm_packages-new';
		this.lastEditedVmTemplate=data.template_id;
		this.fillDialogVars(dialog,data.vars);
		this.dialogShow1(dialog,'edit');
	},
	
	imageExport:function(id,tblid)
	{
		var mode='imageExport';
		var posts=[{'name':'tbl_id','value':tblid},{'name':'id','value':id}];	//,{'name':'dialog','value':'image-import'}
		this.loadData(mode,$.proxy(this.onImageExport,this),posts);
	},
	onImageExport:function(data)
	{
		
	},
	imageImport:function(id,tblid)
	{
		var mode='getImportedImageInfo';
		var posts=[{'name':'tbl_id','value':tblid},{'name':'dialog','value':'image-import'},{'name':'id','value':id}];
		this.loadData(mode,$.proxy(this.onImageImport,this),posts);
	},
	onImageImport:function(data)
	{
		var dialog='image-import';
		this.fillDialogVars(dialog,data);
		if(typeof data['name_comment']!='undefined')
		{
			$('#name_comment').html(data['name_comment']);
			
		}
		this.dialogShow1(dialog);
	},
	imageDownload:function(id,tblid)
	{
		window.location='/?download&file='+id;
	},
	onImageImportStart:function(data)
	{
		this.dialogClose();
	},
	imageRemove:function(id)
	{
		var c=confirm(this.translate('You want to delete image «'+id+'»! Are you sure?'));
		if(!c) return;
		
		var posts=[{'name':'jname','value':id}];
		this.loadData('imageRemove',$.proxy(this.onJailStart,this),posts,false);
	},
	onImageRemove:function(data)
	{
		debugger;
	},
	
	dataReload:function()
	{
		this.loadData('getJsonPage',$.proxy(this.onLoadData,this));
	},
	wssReload:function()
	{
		this.wssend({'cmd':'reload','path':location.pathname},'system');
	},
	
	fillFormVars:function(form,data)
	{
		var n=0,
			f=$(form);
		if(f.length<1) return;
		
		for(n=0,nl=data.length;n<nl;n++)
		{
			var $el=$('[name="'+data[n].name+'"]'),
				type=$el.attr('type'),
				val=data[n].value;
			
			switch(type)
			{
				case 'checkbox':
					$el.attr('checked', val);
					break;
				case 'radio':
					$el.filter('[value="'+val+'"]').attr('checked', 'checked');
					break;
				default:
					$el.val(val);
			}
		}
	},
	fillDialogVars:function(dialog,vars)
	{
		var d=$('dialog#'+dialog);
		if(d.length<1) return;
		
		var inps=$('input,textarea,select',d);
		for(n=0,nl=inps.length;n<nl;n++)
		{
			var inp=inps[n];
			var v=vars[inp.name];
			var type=inp.type.toLowerCase();
			switch(type)
			{
				case 'text':
				case 'hidden':
				case 'password':
				case 'textarea':
				case 'select':
					if(typeof v!='undefined') $(inp).val(v);
					break;
				case 'radio':
					$(inp).prop('checked',$(inp).val()==v);
					break;
				case 'checkbox':
					if(typeof v=='undefined') break;
					$(inp).prop('checked',v==1);
					break;
				case 'range':
					$(inp).val(v);
					$(inp).next().val(v);
					break;
			}
		}
	},
	
	clearHelperForm:function(el)
	{
		if(!el) return;
		var form=$(el).closest('form');
		if(form.length) form[0].reset();
	},
	fillHelperDefault:function(el,def)
	{
		if(!el) return;
		var par=null;
		
		var inp=$(el).prev('input');
		if(inp.length) par=inp;
		
		var sel=$(el).prev('select');
		if(sel.length) par=sel;
		
		if(par.length)
		{
			$(par).val(def);
			return;
		}
	},
	
	saveHelperValues:function(frm)
	{
		var mode='saveHelperValues';
		var posts=$('form.helper').serializeArray();
		var jform=$('form#newJailSettings').serializeArray();
		if(jform.length<1)
		{
			mode='saveJailHelperValues';
		}else{
			posts=posts.concat(jform);
		}
		this.loadData(mode,$.proxy(this.onSaveHelperValues,this),posts);
	},
	onSaveHelperValues:function(data)
	{
		if(typeof data.redirect!='undefined')
		{
			if(data.redirect!='')
			{
				var redir=$('<dialog id="redirect_alert" class="window-box" style="height:60px;"><div class="window-content" style="line-height:40px;">'+this.translate('@redirect_alert@')+' </div></dialog>');
				$('body').append(redir);
				this.dialogShow1('redirect_alert','new');
				setTimeout(function(){location.href=data.redirect;},3000);
			}
		}
	},
	
	deleteHelperGroup:function(form,id)
	{
		this.tmp_formdata=$(form).serializeArray();
		this.tmp_form=form;
		
		var mode='deleteHelperGroup';
		var fh=$('form#newJailSettings');
		if(fh.length==0)
		{
			mode='deleteJailHelperGroup';
			posts=[{'name':'index','value':id}];
		}else{
			var db_path=this.getDbPath();
			posts=[{'name':'index','value':id},{'name':'db_path','value':db_path}];
		}
		this.loadData(mode,$.proxy(this.onDeleteHelperGroup,this),posts);
	},
	onDeleteHelperGroup:function(data)
	{
		if(!data) return;
		if(typeof data.db_path!='undefined')
		{
			this.saveDbPath(data.db_path);
		}
		if(typeof data.html!='undefined')
		{
			$('form.helper').html(data.html);
		}
		if(this.tmp_form)
		{
			this.fillFormVars(this.tmp_form,this.tmp_formdata);
		}
	},
	
	getDbPath:function()
	{
		if(location.hash=='') return '';
		var hash=location.hash.substr(1);
		var db_path='';
		var var_name='h-'+hash;
		if(localStorage) db_path=localStorage[var_name];
		return db_path;
	},
	saveDbPath:function(db_path)
	{
		if(location.hash=='') return false;
		var hash=location.hash.substr(1);
		var var_name='h-'+hash;
		if(localStorage) localStorage[var_name]=db_path;
		return true;
	},
	
	addHelperGroup:function(form)
	{
		this.tmp_formdata=$(form).serializeArray();
		this.tmp_form=form;
		
		var mode='addHelperGroup';
		var fh=$('form#newJailSettings');
		var posts=[];
		if(fh.length==0)
		{
			mode='addJailHelperGroup';
		}else{
			var db_path=this.getDbPath();
			posts=[{'name':'db_path','value':db_path}];
		}
		this.loadData(mode,$.proxy(this.onAddHelperGroup,this),posts);
	},
	onAddHelperGroup:function(data)
	{
		if(!data) return;
		if(typeof data.db_path!='undefined')
		{
			this.saveDbPath(data.db_path);
		}
		if(typeof data.html!='undefined')
		{
			$('form.helper').html(data.html);
		}
		if(this.tmp_form)
		{
			this.fillFormVars(this.tmp_form,this.tmp_formdata);
		}
	},
	
	notify:function(message,type,timeout)
	{
	//	alert, success, warning, error, information
		if(typeof type=='undefined') type='warning';
		if(typeof timeout=='undefined') timeout=5000;
		noty({
			text        : message,
			type        : type,
			dismissQueue: true,
			layout      : 'bottomRight',
			theme       : 'defaultTheme',
			timeout     : timeout,
		});
	},
	
	dotEscape:function(txt)
	{
		return txt.replace(/\./,'\\\.');
	},
	
	wsconnect:function()
	{
		this.client_id=Math.random(10000);	// поменять на сессию
		this.socket = new WebSocket("ws://"+_server_name+":8023/clonos"+location.pathname);
		$(this.socket).on('open',$.proxy(this.wsopen,this))
			.on('close',$.proxy(this.wsclose,this))
			.on('error',$.proxy(this.wserror,this))
			.on('message',$.proxy(this.wsmessage,this));
	},
	wsopen:function(event)
	{
		//this.notify('Соединение по вебсокету успешно открыто!','success');
		this.connected=true;
		setTimeout($.proxy(this.if_wsopened,this),500);
	},
	if_wsopened:function()
	{
		if(!this.connected) return;
		if(!_first_start) this.dataReload();	//this.loadData('getJsonPage',$.proxy(this.onLoadData,this));
		_first_start=false;
		$('#net-stat').attr('class','online icon-online');

	},
	wsclose:function(event)
	{
		if(event.wasClean)
		{
			var msg_type='warning';
			var msg='Сервер закрыл соединение!';
		}else{
			var msg_type='error';
			var msg='Соединение с сервером разорвано аварийно! Перезагрузите страницу!';
		}
		//this.notify(msg,msg_type);
		this.connected=false;
		setTimeout($.proxy(this.wsconnect,this),5000);
		$('#net-stat').attr('class','offline icon-online');
	},
	wserror:function(error)
	{
		this.connected=false;
		//this.notify(error.message,'error');
	},
	wsmessage:function(event)
	{
		try{
			var msg=JSON.parse(event.originalEvent.data);
		}catch(e){
/*
			var msg=event.originalEvent.data;
			if(msg.substr(0,5)=='JSON:')
			{
				data=JSON.parse(msg.substr(5));
				this.onEndOperation(data);
			}

			//this.notify(msg,'warning');
			return;
*/
		}
		
		if(msg && typeof msg.cmd!='undefined')
		{
			this.onChangeStatus(msg);
			return;
		}
		
		if(msg && msg.author=='system')
		{
			var msg=JSON.parse(msg.body);
			if(this.client_id!=msg.client_id && msg.path==location.pathname)
			{
				if(msg.cmd=='update')
				{
					this.enableWait(msg.jail_id);
					this.tasks.add({'operation':msg.operation,'jail_id':msg.jail_id,'status':msg.status,'task_id':msg.task_id,'txt_status':msg.txt_status});
					this.tasks.start();
				}
				if(msg.cmd=='reload')
				{
					this.loadData('getJsonPage',$.proxy(this.onLoadData,this));
				}
			}
			return;
		}
		
		if(msg)
		{
			var txt='<storng>'+msg.author+':</storng> '+msg.body;
			this.notify(txt,'information');
		}
	},
	wssend:function(txt,user)
	{
		var author='user';
		if(typeof user!='undefined') author=user;
		if(typeof txt=='object')
		{
			txt.client_id=this.client_id;
			txt=JSON.stringify(txt);
		}
		var msg=JSON.stringify({'author':author,'body':txt});
		this.socket.send(msg);
	},
	
	jArr:{},
	onChangeStatus:function(data)	// publish /clonos/jailscontainers/ '{"cmd":"refresh"}'
	{
		var cmd=data.cmd;
		var id=data.id;
		var status=data.status;
		if(isset(this.commands[cmd]))
			$('#'+this.dotEscape(id)+' td.jstatus').html(this.translate(this.commands[cmd]['stat'][status]));
		if(['jstart','jstop','bstart','bstop','update','refresh'].indexOf(cmd)==-1)
		{
			if(status==1)
			{
				if(!isset(this.jArr[id])) this.jArr[id]=[];
				if(this.jArr[id].indexOf(cmd)==-1) this.jArr[id].push(cmd);
			}
			if(status==2) this.jArr[id]=[];
		}
		if(isset(data.data))
		{
			if(typeof data.data['vm_ram']!='undefined')
			{
				var ram=data.data['vm_ram'];
				if(isNaN(ram))
				{
					data.data['vm_ram']=ram.replace(/^\d+([gmt])$/gi,function(orig,lett)
					{
						var a={'m':' MB','g':' GB','t':' TB'}[lett.toUpperCase()];	//!!!
						return orig.replace(lett,a);
					});
				}else{
					data.data['vm_ram']=this.formatBytes(ram,0);
				}
			}
			if(typeof data.data['hidden'] && data.data['hidden']==1) return;
		}
		switch(cmd)
		{
			case 'refresh':
				this.loadData('getJsonPage',$.proxy(this.onLoadData,this));
				return;break;
			case 'delete':
				this.deleteItemsOk(id);
				return;break;
			case 'jrename':
			case 'brename':
				if(status==1)
				{
					if(isset(data.data))
					{
						var d=data.data;
						if(isset(d.jname))
						{
							$('#'+id).attr('id',d.jname);
							$('#'+d.jname+' td.jname').html(d.jname);
							var oldID=id;
							id=d.jname;
							this.jArr[id]=this.jArr[oldID];
							delete(this.jArr[oldID]);
						}
					}
				}
			case 'jrestart':
			case 'brestart':
				if(status==2)
				{
					this.evtStatus2(id,status,data);
				}
			case 'jstart':
			case 'bstart':
				if(status==1)
				{
					$('#'+this.dotEscape(id)).removeClass('s-on').addClass('s-off').addClass('busy');
					this.enableWait(id);
				}
				if(status==2)
				{
					if(typeof this.jArr[id]=='undefined' || this.jArr[id].length==0)
						this.evtStatus2(id,status,data);
				}
				break;
			case 'jstop':
			case 'bstop':
				if(status==1)
				{
					$('#'+this.dotEscape(id)).removeClass('s-on').addClass('s-off').addClass('busy');
					this.enableWait(id);
				}
				if(status==2)
				{
					if(typeof this.jArr[id]=='undefined' || this.jArr[id].length==0)
					{
						this.evtStatus2(id,status,data);
					}
				}
				break;
			case 'jcreate':
			case 'bcreate':
			case 'vm_obtain':
			case 'jclone':
			case 'bclone':
			case 'jexport':
			case 'srcup':
			case 'world':
			case 'repo':
				if(status==1)
				{
					if(isset(data.data))
					{
						this.addNewJail(data,cmd);
					}
					if(['srcup','world','repo','jexport'].indexOf(cmd)!=-1)
					{
						this.enableWait(id);
					}
				}
				if(status==2)
				{
					var o=$('#'+this.dotEscape(id));
					if(!o.length) this.addNewJail(data,cmd);
					this.evtStatus2(id,status,data);
				}
				break;
			case 'jremove':
			case 'bremove':
			case 'removesrc':
			case 'removebase':
			case 'imgremove':
				if(status==1)
				{
					$('#'+this.dotEscape(id)).removeClass('s-on').addClass('s-off').addClass('busy');
					this.enableWait(id);
				}
				if(status==2)
				{
					this.enableRip(id);
					window.setTimeout($.proxy(this.deleteItemsOk,this,id),2000);
					if(cmd=='jremove' || cmd=='bremove')
						this.deleteGraphById(id);
				}
				break;
			case 'update':
				if(isset(data.data))
				{
					for(n in data.data)
					{
						if(n=='impsize') data.data[n]=this.formatBytes(data.data[n],0);
						$('#'+this.dotEscape(id)+' .'+n).html(data.data[n]);
					}
				}
				break;
			case 'tooltip':
				var txt='<storng>'+data.author+':</storng> '+data.msg;
				var timeout=5000;
				var type='information';
				if(typeof data.timeout!='undefined') timeout=data.timeout;
				if(typeof data.type!='undefined' && data.type!='') type=data.type;
				this.notify(txt,type,timeout);
				return;
				break;
		}
		if(typeof data.data!='undefined')
		{
			if(typeof data.data['protected']!='undefined')
			if(isset(this.tpl_protected,data.data['protected']))
			{
				var table=$('table.tsimple').attr('id');
				var p=this.tpl_protected[data.data['protected']];
				$('table#'+table+' tr#'+id+' td.op-del').attr('title',p['title']);
				$('table#'+table+' tr#'+id+' td.op-del span').attr('class',p['icon']);
			}
		}
	},
	
	evtStatus2:function(id,status,data)
	{
		if(status==2)
		{
			var cmd=data.cmd;
			
			if(['srcup','repo','world'].indexOf(cmd)!=-1)
			{
				$('#'+this.dotEscape(id))
					.removeClass('s-off').removeClass('busy').removeClass('maintenance')
					.addClass('s-on');
				this.enableClear(id);
				return;
			}
			
			var stat_cl='s-off';
			if(isset(data.data))
			{
				if(isset(data.data['status']))
				{
					var stat=data.data['status'];
					var stat_cl=(stat==0?'s-off':'s-on');
				}
			}
			$('#'+this.dotEscape(id))
				.removeClass('maintenance').removeClass('s-on').removeClass('s-off').removeClass('busy')
				.addClass(stat_cl);
			if(stat==0) this.enablePlay(id); else this.enableStop(id);
		}
	},
	
	addNewJail:function(data,cmd)
	{
		var injected=false,
			status=false;
		var n,nl;
		
		var id=data.id;
		
		var html=this.template;
		if(typeof html=='undefined') html='no data!';
		var table='jailslist';
		if(['bcreate','bclone'].indexOf(cmd)!=-1) table='bhyveslist';
		if(['srcup'].indexOf(cmd)!=-1) table='srcslist';
		if(['repo','world'].indexOf(cmd)!=-1) table='baseslist';
		if(['jexport'].indexOf(cmd)!=-1) table='impslist';

		if(isset(data.data))
		{
			if(isset(this.commands[cmd]['stat']))
				data.data['jstatus']=this.translate(this.commands[cmd]['stat'][1]);
			if(!isset(data.data['id'])) data.data['id']=data['id'];
			for(n in data.data)
				html=html.replace(new RegExp('#'+n+'#','g'),this.translate(data.data[n]));
		}
		
		var el=$('#'+this.dotEscape(id));
		if(el.length>0) return;
		
		var trs=$('table#'+table+' tbody tr');
		for(n=0,nl=trs.length;n<nl;n++)
		{
			var tr=trs[n];
			var tid=$(tr).attr('id');
			if(data.id<tid)
			{
				$(html).insertBefore(tr);
				injected=true;
				status=true;
				break;
			}
		}
		if(!injected)	//	Вставляем запись в конец таблицы
		{
			if(trs.length==0)
			{
				$('table#'+table+' tbody').append(html);
			}else{
				$(html).insertAfter(tr);
			}
			status=true;
		}
		
		this.createGraphById(id);
	},
	
	formatBytes:function(bytes,decimals)
	{
		if(bytes == 0) return '0 Bytes';
		var k = 1024,
			dm = decimals + 1 || 3,
			sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
			i = Math.floor(Math.log(bytes) / Math.log(k));
	   return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
	},
	
	alphanumSort:function(arr,caseInsensitive)
	{
		for (var z = 0, t; t = arr[z]; z++)
		{
			arr[z] = [];
			var x = 0, y = -1, n = 0, i, j;

			while (i = (j = t.charAt(x++)).charCodeAt(0))
			{
				var m = (i == 46 || (i >=48 && i <= 57));
				if (m !== n)
				{
					arr[z][++y] = "";
					n = m;
				}
				arr[z][y] += j;
			}
		}

		arr.sort(function(a, b)
		{
			for (var x = 0, aa, bb; (aa = a[x]) && (bb = b[x]); x++)
			{
				if (caseInsensitive)
				{
					aa = aa.toLowerCase();
					bb = bb.toLowerCase();
				}
				if (aa !== bb)
				{
					var c = Number(aa), d = Number(bb);
					if (c == aa && d == bb)
					{
						return c - d;
					} else return (aa > bb) ? 1 : -1;
				}
			}
			return a.length - b.length;
		});

		for (var z = 0; z < arr.length; z++)
			arr[z] = arr[z].join("");
		return arr;
	},
	
	fileUploadPrepare:function()
	{
		$('#drag-and-drop-zone').dmUploader(
		{
			extraData:{"uplace":location.pathname},
			url: '/?upload',
			dataType: 'json',
			//allowedTypes: 'iso/*',
			extFilter: 'iso;img',	//iso;jpg;jpeg;
			onInit: function(){
				clonos.add_log('Penguin initialized :)');
			},
			onBeforeUpload: function(id){
				//add_log('Starting the upload of #' + id);
				
				clonos.update_file_status(id, 'uploading', 'Uploading...');
			},
			onNewFile: function(id, file){
				clonos.add_log('New file added to queue #' + id);
				
				clonos.add_file(id, file);
			},
			onComplete: function(){
				clonos.add_log('All pending tranfers finished');
			},
			onUploadProgress: function(id, percent){
				var percentStr = percent + '%';
				clonos.update_file_progress(id, percentStr);
			},
			onUploadSuccess: function(id, data){
				clonos.add_log('Upload of file #p-' + id + ' completed');
				
				clonos.add_log('Server Response for file #' + id + ': ' + JSON.stringify(data));
				
				clonos.update_file_status(id, 'success', 'Upload Complete');
				
				//clonos.update_file_progress(id, '0');
				//window.setTimeout($.proxy(this.deleteItemsOk,this,id),2000);
				setTimeout($.proxy(clonos.delete_file,this,id),3000);
				//clonos.dataReload();
				//clonos.wssReload();
			},
			onUploadError: function(id, message){
				clonos.add_log('Failed to Upload file #p-' + id + ': ' + message);
				
				clonos.update_file_status(id, 'error', message);
			},
			onFileTypeError: function(file){
				clonos.notify('File \'' + file.name + '\' cannot be added: must be an ISO','error');
			},
			onFileSizeError: function(file){
				clonos.notify('File \'' + file.name + '\' cannot be added: size excess limit','error');
			},
			onFileExtError: function(file){
				clonos.notify('File \'' + file.name + '\' has a Not Allowed Extension','error');
			},
			onFallbackMode: function(message){
				alert('Browser not supported(do something else here!): ' + message);
			}
		});
	},
	update_file_status:function(id,type,message)
	{
		console.log(message);
		if(type=='error')
		{
			this.notify(message,'error');
			$('#p-'+id+'.line').css('background-color','red');
		}
		if(type=='success')
		{
			$('#p-'+id+'.line').css('background-color','green');
		}
	},
	update_file_progress:function(id, percent)
	{
		$('#p-'+id+'.line').width(percent);
		console.log(percent);
	},
	add_log:function(message)
	{
		console.log(message);
	},
	add_file:function(id, file)
	{
		$('.uploader-progress').append('<div class="file" id="f-'+id+'"><div class="file-name">'+file.name+'</div><div id="p-'+id+'" class="line"></div></div>');
	},
	delete_file:function(id)
	{
		$('.uploader-progress #f-'+id).remove();
	},
	
	debug:function(message,data)
	{
		this.dialogClose();
		$('body').append('<div id="debug" onclick="clonos.closeDebug();"><h1>'+message+'</h1><div>'+data+'</div>');
	},
	closeDebug:function()
	{
		$('#debug').remove();
	},
	
	setLang:function(event)
	{
		var target=event.target;
		var lang=$(target).val();
		if(localStorage)
		{
			//localStorage['lang']=lang;
		}
		document.cookie="lang="+lang+";path=/;";
		location.reload();
	},
	
	createGraphs:function()
	{
		var grs=$('.graph');
		for(n=0,nl=grs.length;n<nl;n++)
		{
			var gr=grs[n];
			this.createGraphByGr(gr);
		}
		graphs.getMetrics();
	},
	createGraphByGr(gr)
	{
		$(gr).css({'padding':'1px 0','margin':0,'vertical-align':'middle','font-size':0});
		var cl=$(gr).attr('class');
		var width=$(gr).width();
		var height=$(gr).height();
		var tooltip1='';
		var tooltip2='';
		res=cl.match(/\bl-([^ ]+)\b/);
		if(res!=null)
		{
			res=res[1].split(',');
			tooltip1=res[0];
			tooltip2=res[1];
		}

		var res=cl.match(/\bg-([^ ]+)\b/);
		if(res!=null)
		{
			var name=res[1];
			if($.isEmptyObject(graphs.list[name]))
			{
				var g=new graph(name,width,height,gr,tooltip1,tooltip2);
				g.create();
			}
		}
	},
	
	createGraphById:function(id)
	{
		var gr=$('td.graph.g-'+id)
		this.createGraphByGr(gr);
	},
	
	deleteGraphById:function(id)
	{
		delete graphs.list[id];
	},
}

/* --- GRAPH START --- */
socket=null;

graphs={
	list:{},
	
	listAdd:function(name)
	{
		this.list.push(name);
	},
	
	getMetrics:function()
	{
		this.wsconnect();
	},
	
	wsconnect:function()
	{
		console.log('Поступила команда на подсоединение по ws');
		if(!socket || socket.readyState==socket.CLOSED)
		{
			console.log('Соединяемся по сокету');
			this.client_id=this.name;
			socket = new WebSocket("ws://"+_server_name+":8024/graph"+location.pathname+"client-"+Math.random());
			$(socket).on('open',$.proxy(this.wsopen,this))
				.on('close',$.proxy(this.wsclose,this))
				.on('error',$.proxy(this.wserror,this))
				.on('message',$.proxy(this.wsmessage,this));
		}
	},
	wsclose:function(event)
	{
		if(event.wasClean)
		{
			var msg_type='warning';
			var msg='Сервер закрыл соединение!';
		}else{
			var msg_type='error';
			var msg='Соединение с сервером разорвано аварийно! Перезагрузите страницу!';
		}
		
		if(socket.readyState==socket.CLOSED)
		{
			this.connected=false;
			console.log('Соединение закрыто по неизвестной причине...');
			socket=null;
			setTimeout($.proxy(this.wsconnect,this),5000);
		}
		console.log('Произошло событие «close», нужно проверить что с соединением.');
	},
	wsopen:function(event)
	{
		this.connected=true;
		console.log('Соединились по ws');
	},
	wserror:function(event)
	{
		//this.connected=false;
		console.log('Какая-то ошибка в соединении сокета');
	},
	wsmessage:function(event)
	{
		try{
			var msg=JSON.parse(event.originalEvent.data);
		}catch(e){ }
		
		if(msg && typeof msg.cmd!='undefined')
		{
			return;
		}
		
		if(typeof msg.__all!='undefined')
		{
			for(a in msg.__all)
			{
				var items=msg.__all[a];
				for(i in items)
				{
					var item=items[i];
					var gr=this.list[item.name];
					var date = new Date();
					date.setTime(item.time*1000);
					if(gr)
					{
						var cpu,mem;
						if(typeof item.pcpu!='undefined')
						{
							gr.line1.append(date.getTime(), item.pcpu);
						}
						if(typeof item.pmem!='undefined')
						{
							gr.line2.append(date.getTime(), item.pmem);
						}
					}else{
						var gr=this.list[item.name+'-pcpu'];
						if(gr)
						{
							if(gr) gr.line1.append(date.getTime(), item.pcpu);
						}
						var gr=this.list[item.name+'-pmem'];
						if(gr)
						{
							if(gr) gr.line1.append(date.getTime(), item.pmem);
						}
					}
				}
			}
		}else{
			
			var larr=[];
			for(l in this.list)
				larr.push(this.list[l].name)
			
			if(typeof msg=='object')
			{
				for(n in msg)
				{
					var inf=msg[n];
					var name=inf['name'];
					var gr=this.list[name];
					var date = new Date();
					date.setTime(inf.time*1000);
					if(gr)
					{
						var cpu=inf.pcpu, mem=inf.pmem;
						gr.line1.append(date.getTime(), cpu);
						gr.line2.append(date.getTime(), mem);
						var res=larr.indexOf(name);
						if(res>-1) larr.splice(res,1);
						//if($('#cdown').css('display')!='block')
						if(clonos.openedJailSummary==name)
						{
							var ngrs=['!summary-cpu','!summary-mem','!summary-iops','!summary-bps'];
							var gr1=this.list[ngrs[0]];
							gr1.line1.append(date.getTime(),inf.pcpu);
								res=larr.indexOf(ngrs[0]); if(res>-1) larr.splice(res,1);
							gr1=this.list[ngrs[1]];
							gr1.line1.append(date.getTime(),inf.pmem);
								res=larr.indexOf(ngrs[1]); if(res>-1) larr.splice(res,1);
							gr1=this.list[ngrs[2]];
							gr1.line1.append(date.getTime(),inf.readiops);
							gr1.line2.append(date.getTime(),inf.writeiops);
								res=larr.indexOf(ngrs[2]); if(res>-1) larr.splice(res,1);
							gr1=this.list[ngrs[3]];
							gr1.line1.append(date.getTime(),inf.readbps);
							gr1.line2.append(date.getTime(),inf.writebps);
								res=larr.indexOf(ngrs[3]); if(res>-1) larr.splice(res,1);
						}
					}else{
						var nname=name+'-pcpu';
						var gr=this.list[nname];
						if(gr)
						{
							if(gr) gr.line1.append(date.getTime(), inf.pcpu);
							var res=larr.indexOf(nname);
							if(res>-1) larr.splice(res,1);
						}
						var nname=name+'-pmem';
						var gr=this.list[nname];
						if(gr)
						{
							if(gr) gr.line1.append(date.getTime(), inf.pmem);
							var res=larr.indexOf(nname);
							if(res>-1) larr.splice(res,1);
						}
					}
				}
			}
			
			for(n=0,nl=larr.length;n<nl;n++)
			{
				this.list[larr[n]].line1.append(new Date().getTime(), 0);
				this.list[larr[n]].line2.append(new Date().getTime(), 0);
			}
			
		}
	},
	onResize:function()
	{
		// ресайз не работает. Нужно найти нормальный способ
		/*
		var graphs=$('.graph');
		for(n=0,nl=graphs.length;n<nl;n++)
		{
			var gpar=$(graphs[n]).parent();
			var width=$(gpar).width();
			$('canvas',graphs[n]).width(width);
		}
		*/
	}
}
function graph(name,width,height,el_parent,tooltip1,tooltip2)
{
	this.name=name;
	this.height=height;
	this.width=width;
	this.el_parent=el_parent;
	this.is_init=false;
	this.el_id=null;
	this.line1=null;
	this.line2=null;
	this.connected=false;
	this.tooltip1=tooltip1;
	this.tooltip2=tooltip2;
	
	this.graphView={
		white:{
			view:{
				interpolation:'bezier',
				grid:{
					fillStyle:'rgba(0,0,0,0.02)',
					sharpLines:true,
					strokeStyle:'transparent',
					borderVisible:true
				},
				labels:{
					fontSize:8,
					disabled:true,
					fillStyle:'#000000',
					precision:0
				},
				millisPerPixel:1000,
				enableDpiScaling:false,
				tooltip:true,
				maxValue:100,
				minValue:0
			},
			colors:{
				line1_color:'rgb(17,125,187)',
				line1_fillStyle:'rgba(17,125,187,0.03)',
				line2_color:'rgb(149,40,180)',
				line2_fillStyle:'rgba(149,40,180,0.03)'
			},
			lineWidth:0,
		},
		black:{
			view:{
				interpolation:'bezier',
				grid:{
					//fillStyle:'rgba(100,100,100,0.02)',
					sharpLines:true,
					//strokeStyle:'transparent',
					borderVisible:true,
					verticalSections:4,
				},
				labels:{
					//fontSize:8,
					//disabled:true,
					//fillStyle:'#000000',
					precision:0,
				},
				millisPerPixel:100,
				enableDpiScaling:false,
				tooltip:true,
				maxValue:100,
				minValue:0,
				tooltipLabel:'test',
			},
			colors:{
				line1_color:'rgb(0,255,0)',
				line1_fillStyle:'rgba(0,255,0,0.4)',
				line2_color:'rgb(255,0,255)',
				line2_fillStyle:'rgba(255,0,255,0.3)'
			},
			lineWidth:1,
		}
	};
}
graph.prototype.create=function()
{
	var el_parent=$(this.el_parent);
	if(typeof el_parent=='undefined')
	{
		el_parent=$('body');
	}else{
		this.el_id=el_parent;
		this.is_init=true;
	}
	$(el_parent).append('<canvas id="g-'+this.name+'" width="'+this.width+'" height="'+this.height+'" vertical-align="middle"></canvas>');
	
	var view;
	var cl=$(el_parent).attr('class');
	var res=cl.match(/v-([a-z]+)/);
	if(res!=null)
	{
		view=res[1];
	}else{
		view='white';
	}
	var varr=this.graphView[view];
	
	var res=cl.match(/\bpr-no\b/);
	if(res!=null)
	{
		varr.view.enableDpiScaling=true;
		delete varr.view.maxValue;
		//delete varr.view.minValue;
	}
	
	this.line1 = new TimeSeries({resetBounds:true,resetBoundsInterval:1000});
	this.line2 = new TimeSeries({resetBounds:true,resetBoundsInterval:1000});
	
	var back_color='rgba(0,0,0,0.02)';
	var line1_color=varr.colors.line1_color;
	var line1_fillStyle=varr.colors.line1_fillStyle;
	var line2_color=varr.colors.line2_color;
	var line2_fillStyle=varr.colors.line2_fillStyle;
	
	this.smoothie = new SmoothieChart(varr.view);

	this.smoothie.addTimeSeries(this.line1, { strokeStyle: line1_color, lineWidth:varr.lineWidth, fillStyle:line1_fillStyle, tooltipLabel:this.tooltip1+':' });
	this.smoothie.addTimeSeries(this.line2, { strokeStyle: line2_color, lineWidth:varr.lineWidth, fillStyle:line2_fillStyle, tooltipLabel:this.tooltip2+':' });

	this.smoothie.streamTo(document.getElementById('g-'+this.name), 1000);
	graphs.list[this.name]=this;
}
/* === GRAPH END === */

function isset(varr){for(a in arguments){if(typeof arguments[a]=='undefined')return false;}return true;}

function ws_debug(){
	var res=prompt('Введите JSON строку','');
	if(res=='' || res==null) return;
	var data=JSON.parse(res);
	clonos.onChangeStatus(data);
}


$(window).on('load',function(){clonos.start();});
$(window).on('unload',function(){});	/* эта функция заставляет FireFox запускать JS-функции при нажатии кнопки «Назад»
http://stackoverflow.com/questions/2638292/after-travelling-back-in-firefox-history-javascript-wont-run */
$(function(){clonos.loadData('getJsonPage',$.proxy(clonos.onLoadData,clonos));});