Uploadtousers.window.FileUpload = function(config) {
    config = config || {};

    var check = Ext.getCmp('uploadtousers-window-file-upload');
    if (check) {
        check.destroy();
    }
    
    Ext.applyIf(config, {
        id: 'uploadtousers-window-file-upload',
        layout: 'fit',
        title: _('uploadtousers.file.select'),
        width: 300,
        height: 160,
        modal: true,
        items: [
            {
                xtype: 'uploadtousers-panel-upload-form',
                blankValues: true,
                node: config.node ? config.node : ''
            }
        ]
    });
    Uploadtousers.window.FileUpload.superclass.constructor.call(this, config);
};
Ext.extend(Uploadtousers.window.FileUpload, Ext.Window); // don't use MODx.Window. We don't need saving in here!
Ext.reg('uploadtousers-window-file-upload', Uploadtousers.window.FileUpload);