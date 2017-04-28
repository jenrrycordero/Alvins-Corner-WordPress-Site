(function($, window)
{
	var $w = $(window),
		$body = $('body'),
		ajo,
		origtitle = $.address.title();
		aptdata.raw = {};
	
	function getAptData()
	{
		if(aptdata.raw.fetched) return true;
		var success = false;
		$.ajax({
			url: aptdata.ajaxurl,
			async: false,
			data: {
				//'sec': aptdata.sec,
				'action': 'lbm',
				'a': 1
			},
			dataType: 'json',
			error: function(http, message, exc)
			{
				success = false;
			},
			success: function(res)
			{
				if(res.res == 'ok')
				{
					aptdata.raw.fetched = true;
					aptdata.raw.units = res.units;
					aptdata.raw.models = res.models;
					aptdata.raw.floors = res.floors;
					
					aptdata.raw.unitorder = [];
					$.each(res.units, function(key, value)
					{
						aptdata.raw.unitorder.push(key);
					});
					aptdata.raw.unitorder.sort();
					
					
					aptdata.raw.modelorder = [];
					$.each(res.models, function(key, value)
					{
						aptdata.raw.modelorder.push(key);
					});
					aptdata.raw.modelorder.sort();
					
					
					aptdata.raw.floororder = [];
					$.each(res.floors, function(key, value)
					{
						aptdata.raw.floororder.push(key);
					});
					aptdata.raw.floororder.sort();
					success = true;
				}
			}
		});
		return success;
	}
	
	function getNextUnit(id)
	{
		if(!getAptData()) return false;
		var order = aptdata.raw.unitorder,
			index = order.indexOf(id);
		if(index == -1) return false;
		
		if(index >= order.length-1) index = 0;
		else index++;
		return aptdata.raw.unitorder[index];
	}
	function getPrevUnit(id)
	{
		if(!getAptData()) return false;
		var order = aptdata.raw.unitorder,
			index = order.indexOf(id);
		if(index == -1) return false;
		
		if(index <= 0) index = order.length-1;
		else index--;
		return aptdata.raw.unitorder[index];
	}
	function getNextFloor(id)
	{
		if(!getAptData()) return false;
		var order = aptdata.raw.floororder,
			index = order.indexOf(id);
		if(index == -1) return false;
		
		if(index >= order.length-1) index = 0;
		else index++;
		return aptdata.raw.floororder[index];
	}
	function getPrevFloor(id)
	{
		if(!getAptData()) return false;
		var order = aptdata.raw.floororder,
			index = order.indexOf(id);
		if(index == -1) return false;
		
		if(index <= 0) index = order.length-1;
		else index--;
		return aptdata.raw.floororder[index];
	}
	function getNextModel(id)
	{
		if(!getAptData()) return false;
		var order = aptdata.raw.modelorder,
			index = order.indexOf(id);
		if(index == -1) return false;
		
		if(index >= order.length-1) index = 0;
		else index++;
		return aptdata.raw.modelorder[index];
	}
	function getPrevModel(id)
	{
		if(!getAptData()) return false;
		var order = aptdata.raw.modelorder,
			index = order.indexOf(id);
		if(index == -1) return false;
		
		if(index <= 0) index = order.length-1;
		else index--;
		return aptdata.raw.modelorder[index];
	}
	
	aptdata.getUnits = function()
	{
		if(!getAptData()) return false;
		return aptdata.raw.units;
	};
	aptdata.getFloors = function()
	{
		if(!getAptData()) return false;
		return aptdata.raw.floors;
	};
	aptdata.getModels = function()
	{
		if(!getAptData()) return false;
		return aptdata.raw.models;
	};
	
	var setTitle = function(title)
	{
		if(!title)
			$.address.title(origtitle);
		else
			$.address.title(title + ' | ' + origtitle);
	};
	aptdata.setTitle = setTitle;
	
	$.urlParam = function(name, url)
	{
		if(url === undefined) url = window.location.href;
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(url);
		if (results==null){
		   return false;
		}
		else{
		   return results[1] || 0;
		}
	}
	

	aptdata.openUnit = function(id)
	{
		var units = aptdata.getUnits();
		if(!units || !units[id]) return false;
		
		$.magnificPopup.open({
			key: 'lb-unit',
			items: {
				src: aptdata.pluginurl + 'floor-plan/unit.php?id=' + id
			},
			showCloseBtn: false,
			callbacks: {
				open: function(){
					setTitle('Unit ' + id);
				},
				change: function(){
					setTitle('Unit ' + id);
				},
				close: function(){
					setTitle('');
					window.location.hash = ('!');
				}
			},
			closeBtnInside: true,
			type: 'iframe',
			mainClass: '',
			iframe: {
				markup: '<div class="mfp-iframe-scaler">'+
							'<iframe class="mfp-iframe" frameborder="0" allowfullscreen allowtransparency="true"></iframe>'+
						'</div>'
			}
		});
		var mp = $.magnificPopup.instance;
		mp.next = function()
		{
			var target = getNextUnit(id);
			if(target === false) return;
			window.location.hash = ('#!/floor-plans/unit/' + target);
		};
		mp.prev = function()
		{
			var target = getPrevUnit(id);
			if(target === false) return;
			window.location.hash = ('#!/floor-plans/unit/' + target);
		};
	};
	
	aptdata.openModel = function(id)
	{
		$.magnificPopup.open({
			key: 'lb-model',
			items: {
				src: aptdata.pluginurl + 'floor-plan/model.php?id=' + id
			},
			showCloseBtn: false,
			callbacks: {
				open: function(){
					setTitle('Model ' + id);
				},
				change: function(){
					setTitle('Model ' + id);
				},
				close: function(){
					setTitle('');
					window.location.hash = ('!');
				}
			},
			closeBtnInside: true,
			type: 'iframe',
			mainClass: '',
			iframe: {
				markup: '<div class="mfp-iframe-scaler">'+
							'<iframe class="mfp-iframe" frameborder="0" allowfullscreen allowtransparency="true"></iframe>'+
						'</div>'
			}
		});
		var mp = $.magnificPopup.instance;
		mp.next = function()
		{
			var target = getNextModel(id);
			if(target === false) return;
			window.location.hash = ('#!/floor-plans/model/' + target);
		};
		mp.prev = function()
		{
			var target = getPrevModel(id);
			if(target === false) return;
			window.location.hash = ('#!/floor-plans/model/' + target);
		};
	};
	
	aptdata.openFloor = function(id)
	{
		var floors = aptdata.getFloors();
		if(!floors || !floors[id]) return false;
		
		$.magnificPopup.open({
			key: 'lb-floor',
			items: {
				src: aptdata.pluginurl + 'floor-plan/floor.php?id=' + id
			},
			showCloseBtn: true,
			callbacks: {
				open: function(){
					setTitle('Floor ' + id);
				},
				change: function(){
					setTitle('Floor ' + id);
				},
				close: function(){
					setTitle('');
					window.location.hash = ('!');
				}
			},
			closeBtnInside: true,
			type: 'iframe',
			mainClass: '',
			iframe: {
				markup: '<div class="mfp-iframe-scaler">'+
							'<iframe class="mfp-iframe" frameborder="0" allowfullscreen allowtransparency="true"></iframe>'+
						'</div>'
			}
		});
		var mp = $.magnificPopup.instance;
		mp.next = function()
		{
			var target = getNextFloor(id);
			if(target === false) return;
			window.location.hash = ('!/floor-plans/floor/' + target);
		};
		mp.prev = function()
		{
			var target = getPrevFloor(id);
			if(target === false) return;
			window.location.hash = ('!/floor-plans/floor/' + target);
		};
	};
	
	aptdata.openFloors = function()
	{
		$.magnificPopup.open({
			key: 'lb-bldg',
			items: {
				src: aptdata.pluginurl + 'floor-plan/'
			},
			showCloseBtn: true,
			callbacks: {
				open: function(){
					setTitle('Building View');
				},
				close: function(){
					setTitle('');
					window.location.hash = ('!');
				}
			},
			closeBtnInside: true,
			type: 'iframe',
			mainClass: 'bldg-iframe',
			iframe: {
				markup: '<div class="mfp-iframe-scaler">'+
							'<iframe class="mfp-iframe" frameborder="0" allowfullscreen allowtransparency="true"></iframe>'+
						'</div>'
			}
		});
	};
	
	$.address.change(function(e)
	{
		console.log(e.pathNames);
		if(e.pathNames.length < 2 || e.pathNames[0] != '!' || e.pathNames[1] != 'floor-plans')
		{
			if($.magnificPopup.instance.content) $.magnificPopup.instance.close();
			return;
		}
		switch(e.pathNames[2])
		{
			case undefined: //Building view
			{
				aptdata.openFloors();
				break;
			}
			case 'unit':
			case 'units':
			{
				var units = aptdata.getUnits();
				if(!units[e.pathNames[3]])
				{
					if($.magnificPopup.instance.content) $.magnificPopup.instance.close();
					window.location.hash = ('!/floor-plans');
				}
				else
				{
					aptdata.openUnit(e.pathNames[3]);
				}
				break;
			}
			case 'floor':
			case 'floors':
			{
				var floors = aptdata.getFloors();
				if(!floors[e.pathNames[3]])
				{
					if($.magnificPopup.instance.content) $.magnificPopup.instance.close();
					window.location.hash = ('!/floor-plans');
				}
				else
				{
					aptdata.openFloor(e.pathNames[3]);
				}
				break;
			}
			case 'model':
			case 'models':
			{
				if(!e.pathNames[3])
				{
					if($.magnificPopup.instance.content) $.magnificPopup.instance.close();
					window.location.hash = ('!/floor-plans');
				}
				else
				{
					aptdata.openModel(e.pathNames[3]);
				}
				break;
			}
			case 'search':
			{
				
				break;
			}
			default: //Move this stuff to try/catch then throw
			{
				window.location.hash = ('!/floor-plans');
			}
		}
	});
	$.address.update();
	
})(jQuery, window);