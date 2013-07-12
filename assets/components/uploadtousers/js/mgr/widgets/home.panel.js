Uploadtousers.panel.Home = function(config) {
    config = config || {};

    Ext.apply(config,{
        id: 'uploadtousers-panel-home',
        baseCls: 'modx-formpanel',
        cls: 'container',
        layout: 'border',
        defaults: {
            collapsible: true,
            split: true,
            bodyStyle: 'padding: 15px',
            border: false,
            autoHeight: true
        },
        bodyStyle: 'min-height: 500px;',
        items: [{
            collapsible: false,
            region: 'north',
            defaults: {
                border: false,
                autoHeight: true
            },
            items:[{
                html: '<h2>'+_('uploadtousers.title')+'</h2>',
                border: false,
                height: 150,
                minSize: 75,
                maxSize: 250,
                margins: '0 0 0 0',
                cmargins: '5 0 0 0',
                region: 'north',
                collapsible: false,
                cls: 'modx-page-header'
            },{
                html: '<p>'+_('uploadtousers.title_desc')+'</p>',
                bodyCssClass: 'panel-desc',
                collapsible: false,
                region: 'center'
            }]
        },{
            xtype: 'uploadtousers-panel-userstree',
            preventRender: true,
            region: 'left'
        },{
            xtype: 'uploadtousers-panel-content',
            collapsible: false,
            preventRender: true,
            region: 'center'
        }]
    });

    Ext.getCmp('modx-content').doLayout();
    Uploadtousers.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(Uploadtousers.panel.Home,MODx.Panel);
Ext.reg('uploadtousers-panel-home',Uploadtousers.panel.Home);