Uploadtousers.panel.Home = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('uploadtousers.title')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,items: [{
                title: _('uploadtousers.userstab')
                ,defaults: { autoHeight: true }
                ,items: [{
                    html: '<p>'+_('uploadtousers.title_desc')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'uploadtousers-grid-uploadtousers'
                    ,cls: 'main-wrapper'
                    ,preventRender: true
                }]
            }]
        }]
    });
    Uploadtousers.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Uploadtousers.panel.Home,MODx.Panel);
Ext.reg('uploadtousers-panel-home',Uploadtousers.panel.Home);