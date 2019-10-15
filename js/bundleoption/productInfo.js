var ProductInfo = Class.create();
ProductInfo.prototype = {
    settings: {
        'loadingMessage': 'Please wait ...'
    },
    
    initialize: function(selector, x_image, settings)
    {
        Object.extend(this.settings, settings);
        this.createWindow();  
        
        var that = this;
        $$(selector).each(function(el, index){
            el.observe('click', that.loadInfo.bind(that));
        })
        
        
    },
    
    createLoader: function()
    {
        var loader = new Element('div', {id: 'ajax-preloader'});
        loader.innerHTML = "<p class='loading'>"+this.settings.loadingMessage+"</p>";
        document.body.appendChild(loader);
        $('ajax-preloader').setStyle({
            position: 'absolute',
            top:  document.viewport.getScrollOffsets().top + 200 + 'px',
            left:  document.body.clientWidth/2 - 75 + 'px'
        });
    },
    
    destroyLoader: function()
    {
        $('ajax-preloader').remove();
    },

    
    createWindow: function()
    {
        var qWindow = new Element('div', {id: 'robbie-popup'});
        qWindow.innerHTML = '<div id="robbie-popup-bkgrnd"></div><div id="quick-window"><div id="quickview-header"><a href="javascript:void(0)" id="quickview-close">close</a></div><div class="quick-view-content"></div></div>';
        document.body.appendChild(qWindow);
        $('quickview-close').observe('click', this.hideWindow.bind(this)); 
        $('robbie-popup-bkgrnd').observe('click', this.hideWindow.bind(this)); 
    },
    
    showWindow: function()
    {
        $('quick-window').setStyle({
            top:  document.viewport.getScrollOffsets().top + 100 + 'px',
            left:  document.body.clientWidth/2 - $('quick-window').getWidth()/2 + 'px',
            display: 'block',
            zIndex: '11000'
        });

        $('robbie-popup-bkgrnd').setStyle({
            backgroundColor: 'rgb(119, 119, 119)',
            opacity: '0.7',
            cursor: 'pointer',
            height: '1992px',
            display: 'block',
            position: 'absolute',
            top: 0,
            left: 0,
            height : document.body.clientHeight + 'px',
            width: '100%',
            zIndex: '11000' 
        });

        
    },
    
    setContent: function(content)
    {
        $$('.quick-view-content')[0].insert(content);
    },
    
    clearContent: function()
    {
        $$('.quick-view-content')[0].replace('<div class="quick-view-content"></div>');
    },
    
    hideWindow: function()
    {
        this.clearContent();
        $('quick-window').hide();
        $('robbie-popup-bkgrnd').hide();
        
    },

    loadInfo: function(e)
    {
        e.stop();
        var that = this;      
        if(e.element().classList[0] === 'product-image'){
            var ajaxURL = Element.up(e.element());
        }else{
            var ajaxURL = e.element();
        }
        
        this.createLoader();
        new Ajax.Request(ajaxURL.href, {
            onSuccess: function(response) {
                that.clearContent();
                that.setContent(response.responseText);
                that.destroyLoader();
                that.showWindow();
            }
        }); 
    }
}

Event.observe(window, 'load', function() {
    new ProductInfo('.ajax', {}, {
    });
});
