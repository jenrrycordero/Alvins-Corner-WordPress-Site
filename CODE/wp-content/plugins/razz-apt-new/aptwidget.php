<?php
class AptWidget
{

    public function __construct()
    {
        if(isset($_GET['widget']) && empty($_GET['widget']))
        {
            $this->showClientJS();
            exit;
        }


	    //add_action('init', array($this, 'setUpRewrite'));
	    //add_action('wp_head', array($this, 'handleRedirect'));
    }

	public function setUpRewrite()
	{
		//TODO: this isn't working
		add_rewrite_rule('^floor-plans/(.*)', 'index.php?floor_plans=$matches[1]', 'top');
	}

	public function handleRedirect()
	{
		//This part does work with the param directly but not through rewrite_rule, find when to load it?
		if(isset($_GET['floor_plans']))
		{
			wp_redirect(home_url() . '/#!/floor-plans/' . $_GET['floor_plans']);
			exit;
		}
	}

    public function showClientJS()
    {
        $settings = get_option('razz_apt_opt');
        $anim_in = $settings['widget_animate_in'];
        $anim_out = $settings['widget_animate_out'];
        $trans_in = $settings['widget_transition_in'];
        $trans_out = $settings['widget_transition_out'];
        $pathname = $settings['widget_pathname'];
        header('Content-Type: text/javascript');
	    ob_start();
?>
//<script>
    (function($) {
        <?php if(!empty($pathname)): ?>
        if(window.location.pathname != <?= json_encode($pathname) ?>) return;
        <?php endif; ?>
        var isMobile = /Android|webOS|iP(hone|ad|od)|BlackBerry/i.test( (window.navigator.userAgent||window.navigator.vendor||window.opera) );


        var $w = $(window),
            $body = $('body'),
            ajo,
            origtitle,
            $cont = $('');
        if(!window['aptdata']) window.aptdata = {};
        aptdata.raw = {};

        function getAptData()
        {
            if(aptdata.raw.fetched) return true;
            var success = false;
            $.ajax({
                url: <?= json_encode(admin_url('admin-ajax.php')) ?>,
                async: false,
                data: {
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

            loadLBURL(cdn + 'floor-plan/unit.php?id=' + id);
        };

        aptdata.openModel = function(id)
        {
            loadLBURL(cdn + 'floor-plan/model.php?id=' + id);
        };

        aptdata.openFloor = function(id)
        {
            var floors = aptdata.getFloors();
            if(!floors || !floors[id]) return false;
            loadLBURL(cdn + 'floor-plan/floor.php?id=' + id);
        };

        aptdata.openModels = function()
        {
            loadLBURL(cdn + 'floor-plan/models.php');
        };

        aptdata.openSearch = function(filter, filter_value)
        {
            loadLBURL(cdn + 'floor-plan/search.php?filter=' + filter + '&filter_value=' + filter_value);
        };

        aptdata.openFloors = function()
        {
            if(isMobile)
                window.location.hash = '!/floor-plans/search';
            else
                loadLBURL(cdn + 'floor-plan/floors.php');
        };

	    aptdata.openHome = function()
	    {
		    <?php if($settings['widget_home'] === 'nothing'): ?>
		    hideIFrame();
		    <?php else: ?>
		    window.location.hash = ('!/floor-plans/<?= $settings['widget_home'] ?>');
		    <?php endif; ?>
	    };

        var lw = []; //Load Wait
        var cdn = <?= json_encode(plugin_dir_url(__FILE__)) ?>;
        var rpc;


        function loadJS(url, callback)
        {
            setTimeout(function()
            {
                $.ajax({
                    dataType: "script",
                    cache: true,
                    url: url,
                    complete: callback
                });
            }, 1);
        }

        function loadJSDef(url, callback)
        {
            var id = lw.push($.Deferred())-1;
            loadJS(url, function()
            {
                //console.info('Loaded: ' + url);
                if(callback) callback();
                lw[id].resolve();
            });
        }

        function loadCSS(url)
        {
            setTimeout(function()
            {
                $("<link>", {rel: "stylesheet", type: "text/css",  href: url}).appendTo('head');
            }, 1);
        }
        $(document).ready(function()
        {
            $cont = $('<div/>', {
                id: 'apttool-container',
                style: ''
            });
            $cont.prependTo('body');

            if(/iP(hone|ad|od)/i.test( (window.navigator.userAgent||window.navigator.vendor||window.opera) )) $cont.css({
                '-webkit-overflow-scrolling': 'touch',
                'overflow-y': 'scroll'
            });
            $('head').append($('<style></style>').text('#apttool-container{display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: <?= $settings['lb_bgcolor']; ?>; z-index: 9999999999}#apttool-container iframe{position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1001}'));

            //loadCSS(cdn + 'css/magnific-popup.css');
            <?php if(!empty($anim_in) || !empty($anim_out)): ?>
            loadCSS('//cdnjs.cloudflare.com/ajax/libs/animate.css/3.1.1/animate.min.css');
            <?php endif; ?>
            loadJSDef(cdn + 'xdm/json2.js');
            loadJSDef(cdn + 'xdm/easyXDM.js');
            loadJSDef(cdn + 'floor-plan/js/jquery.address.js');
            //loadJSDef('//cdnjs.cloudflare.com/ajax/libs/magnific-popup.js/0.9.9/jquery.magnific-popup.min.js');

            $.when.apply($, lw).done(main);
        });

        function main()
        {
            origtitle = $.address.title();
            //setupRPC();
            //initAPI();
            initAddress();
        }

        function loadLBURL(rurl)
        {
//            $cont.html('');
            //var rurl = cdn+'/floor-plan/';

            rpc = new easyXDM.Rpc(
            {
                swf: cdn+"xdm/easyxdm.swf",
                remote: rurl,
                container: $cont[0]
            },
            {
                remote: {

                },
                local: {
                    setHash: function(dest)
                    {
                        setTitle('');
                        window.location.hash = dest;
                    },
                    setTitle: function(title)
                    {
                        setTitle(title);
                    }
                }
            });
            //console.log(rpc);
            showIFrame();
        }

        function showIFrame()
        {
            if ($cont.is(':hidden'))
            {
                $('html,body').css({'overflow': 'hidden'});
                $cont.show();
                <?php if(!empty($anim_in)): ?>
                $cont.animatecss(<?= json_encode($anim_in) ?>);
                <?php endif; ?>
            }
            else
            {
                var $old = $cont.find('iframe:not(:last-child)'),
                    $new = $cont.find('iframe:last-child');
                <?php if(empty($trans_out)): ?>
                $old.remove();
                <?php else: ?>
                $old.animatecss(<?= json_encode($trans_out) ?>, {
                    onComplete: function () {
                        $(this).remove();
                    }
                });
                <?php endif; ?>
                <?php if(!empty($trans_in)): ?>
                $new.animatecss(<?= json_encode($trans_in) ?>);
                <?php endif; ?>
            }

        }

        function hideIFrame()
        {
            if ($cont.is(':visible'))
            {
                $('html,body').css('overflow', '');
                <?php if(empty($anim_out)): ?>
                $cont.hide();
                $cont.html('');
                <?php else: ?>
                $cont.animatecss(<?= json_encode($anim_out) ?>, {
                    onComplete: function () {
                        $cont.hide();
                        $cont.html('');
                    }
                });
                <?php endif; ?>
            }
        }

        function initAddress()
        {
            var lastpath = window.location.hash;
	        $.address.strict(false);
            $.address.change(function(e)
            {
                if(e.path == lastpath) return;
                lastpath = e.path;
                //console.log(e.pathNames);
                if(e.pathNames.length < 2 || e.pathNames[0] != '!' || (e.pathNames[1] != 'floor-plans' && e.pathNames[1] != 'apt'))
                {
                    hideIFrame();
                    return;
                }
                switch(e.pathNames[2])
                {
	                case 'floors':
                    {
                        aptdata.openFloors();
                        break;
                    }
                    case 'unit':
                    {
                        var units = aptdata.getUnits();
                        if(!units[e.pathNames[3]])
                        {
	                        aptdata.openHome();
                        }
                        else
                        {
                            aptdata.openUnit(e.pathNames[3]);
                        }
                        break;
                    }
                    case 'floor':
                    {
                        var floors = aptdata.getFloors();
                        if(!floors[e.pathNames[3]])
                        {
	                        aptdata.openHome();
                        }
                        else
                        {
                            aptdata.openFloor(e.pathNames[3]);
                        }
                        break;
                    }
                    case 'model':
                    {
                        if(!e.pathNames[3])
                        {
	                        aptdata.openHome();
                        }
                        else
                        {
                            aptdata.openModel(e.pathNames[3]);
                        }
                        break;
                    }
                    case 'models':
                    {
	                    aptdata.openModels();
                        break;
                    }
                    case 'search':
                    {
	                    var filter = e.pathNames[3] ? e.pathNames[3] : '',
		                    filter_value = e.pathNames[4] ? e.pathNames[4] : '';

	                    aptdata.openSearch(filter, filter_value);
                        break;
                    }
                    default: //Move this stuff to try/catch then throw
                    {
	                    aptdata.openHome();
                    }
                }
            });
            $.address.update();
        }

        window.animend = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend cancelanimation';
        $.fn.extend({
            animatecss: function(type, settings)
            {
                if(!settings) settings = {};
                settings = $.extend({
                    onComplete: function(){}
                }, settings);
                var cananimate = window['Modernizr'] ? Modernizr.csstransitions : true;
                return this.each(function()
                {
                    var $this = $(this);
                    if(!cananimate)
                    {
                        settings.onComplete.call(this);
                    }
                    else
                    {
                        if($this.hasClass('animating'))
                        {
                            $this.trigger('cancelanimation');
                        }

                        var tmr = setTimeout(function()
                        {
                            $this.trigger('cancelanimation');
                        },2100);
                        $this.addClass('animated animating ' + type).one(animend, function(e)
                        {
                            e.stopPropagation();
                            clearTimeout(tmr);
                            $this.removeClass('animated animating ' + type);
                            settings.onComplete.call(this);
                        });
                    }
                    return true;
                });
            }
        });
    })(jQuery);
<?php
	    $output = ob_get_clean();
	    if(true)//Minify
	    {
		    require_once( plugin_dir_path( __FILE__ ) . 'JSMinPlus.php');
		    $output = JSMinPlus::minify($output);
		    $output = str_replace("\n", ";", $output);
	    }
	    echo $output;
        exit;
    }


}
?>