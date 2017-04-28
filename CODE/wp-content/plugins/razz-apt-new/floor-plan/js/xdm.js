jQuery(document).ready(function($){
    window.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test( (window.navigator.userAgent||window.navigator.vendor||window.opera) );
    easyXDM.DomHelper.requiresJSON("../xdm/json2.js");

    try {
        window.rpc = new easyXDM.Rpc(
        {
            swf: "../xdm/easyxdm.swf"
        },
        {
            remote: {
                setHash: {},
                setTitle: {}
            }
        });
        $('body').on('click', '.goto-floor', function (e)
        {
            e.preventDefault();
            rpc.setHash("!/floor-plans/floor/" + encodeURIComponent($(this).data('floor')).replace(/%20/g, '+'));
        }).on('click', '.goto-model', function (e)
        {
            e.preventDefault();
            rpc.setHash("!/floor-plans/model/" + encodeURIComponent($(this).data('model')).replace(/%20/g, '+'));
        }).on('click', '.goto-unit', function (e)
        {
            e.preventDefault();
            rpc.setHash("!/floor-plans/unit/" + encodeURIComponent($(this).data('unit')).replace(/%20/g, '+'));
        }).on('click', '.goto-floors', function (e)
        {
            e.preventDefault();
            rpc.setHash("!/floor-plans");
        }).on('click', '.goto-models', function (e)
        {
            e.preventDefault();
            rpc.setHash("!/floor-plans/models");
        }).on('click', '.goto-search', function (e)
        {
            e.preventDefault();
            rpc.setHash("!/floor-plans/search");
        });
        $('.goto-close,.fp-modal-close,.closebtn').on('click', function(e)
        {
            e.preventDefault();
            rpc.setHash("!");
        });
    }
    catch(err)
    {
        window.rpc = false;
    }
});