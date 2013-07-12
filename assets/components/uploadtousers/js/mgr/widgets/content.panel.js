Uploadtousers.panel.Content = function(config) {
    config = config || {};

    Ext.apply(config,{
        id: 'uploadtousers-panel-content',
        collapsible: false,
        margins: '0 0 0 0',
        cmargins: '0 0 0 0'
    });
    Uploadtousers.panel.Content.superclass.constructor.call(this,config);
};

Ext.extend(Uploadtousers.panel.Content, MODx.Panel);
Ext.reg('uploadtousers-panel-content', Uploadtousers.panel.Content);
