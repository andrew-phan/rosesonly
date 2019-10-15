jQuery.noConflict();
jQuery(function(e){
    function r(){
        var e=-1;
        if(navigator.appName=="Microsoft Internet Explorer"){
            var t=navigator.userAgent;
            var n=new RegExp("MSIE ([0-9]{1,}[.0-9]{0,})");
            if(n.exec(t)!=null)e=parseFloat(RegExp.$1)
                }
                return e
        }
        function i(){
        var e=arguments[0];       
        var t=/\/[^\/]{0,}$/ig;
        if(e[e.length-1]=="/"){
            e=e.substring(0,e.length-1);
            return e.match(t)+"/"
            }
            return e.match(t)
        }
        function s(){
        return arguments[0].replace(/^\s+|\s+$/g,"")
        }
        function o(){
        var n=arguments[0];
        var o=e(n.itemClass);
        var u;
        var a="quickshop/index/view";
        if(EM.QuickShop.BASE_URL.indexOf("index.php")==-1){
            a="index.php/quickshop/index/view"
            }
            var f=EM.QuickShop.BASE_URL+a;
        var l='<a id="em_quickshop_handler" href="#" style="visibility:hidden;position:absolute;top:0;left:0"><img  alt="quickshop" src="'+EM.QuickShop.QS_IMG+'" /></a>';
        e(document.body).append(l);
        var c=e("#em_quickshop_handler img");
        e.each(o,function(o,u){
            var a=f;
            t=e(u).children(n.aClass);
            var l=i(t.attr("href"))[0];
            l[0]=="/"?l=l.substring(1,l.length):l;
            l=s(l);
            a=f+"/path/"+l;
            version=r();
            if(version<8&&version>-1){
                a=f+"/path"+l
                }
                e(n.imgClass,this).bind("mouseover",function(){
                var t=e(this).offset();
                e("#em_quickshop_handler").attr("href",a).show().css({
                    top:t.top+(e(this).height()-c.height())/2+"px",
                    left:t.left+(e(this).width()-c.width())/2+"px",
                    visibility:"visible"
                });
                e("#em_quickshop_handler").hide()
                });
            e(u).bind("mouseout",function(){
                e("#em_quickshop_handler").hide()
                })
            });
        e("#em_quickshop_handler").bind("mouseover",function(){
            e(this).show()
            }).bind("click",function(){
            e(this).hide()
            });
        e("#em_quickshop_handler").fancybox({
            width:EM.QuickShop.QS_FRM_WIDTH,
            height:EM.QuickShop.QS_FRM_HEIGHT,
            autoScale:false,
            padding:0,
            margin:0,
            type:"iframe",
            onComplete:function(){
                e.fancybox.showActivity();
                e("#fancybox-frame").unbind("load");
                e("#fancybox-frame").bind("load",function(){
                    e.fancybox.hideActivity()
                    })
                }
            });
    e(".em_quickshop_handler").fancybox({
        //width:EM.QuickShop.QS_FRM_WIDTH,
        //height:EM.QuickShop.QS_FRM_HEIGHT,
        width:600,
        height:400,
        autoScale:false,
        padding:0,
        margin:0,
        type:"iframe",
        onComplete:function(){
            e.fancybox.showActivity();
            e("#fancybox-frame").unbind("load");
            e("#fancybox-frame").bind("load",function(){
                e.fancybox.hideActivity()
                })
            }
        })
}
var t,n;
o({
    itemClass:".quickview-products li.item",
    aClass:"a.product-image",
    imgClass:".product-image img"
})
})