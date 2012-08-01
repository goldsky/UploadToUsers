Uploadtousers.panel.Contents = function(config) {
    config = config || {};

    Ext.apply(config,{
        id: 'uploadtousers-panel-contents',
//        region: 'center',
        collapsible: false,
        margins: '0 0 0 0',
        cmargins: '0 0 0 0'
    });
    Uploadtousers.panel.Contents.superclass.constructor.call(this,config);
};

Ext.extend(Uploadtousers.panel.Contents, MODx.Panel);
Ext.reg('uploadtousers-panel-contents', Uploadtousers.panel.Contents);
