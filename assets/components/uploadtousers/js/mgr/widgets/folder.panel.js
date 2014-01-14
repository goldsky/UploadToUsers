Uploadtousers.panel.Folder = function(config) {
    config = config || {};

    var check = Ext.getCmp('uploadtousers-panel-folder');
    if (check) {
        check.destroy();
    }

    Ext.applyIf(config, {
        id: 'uploadtousers-panel-folder',
        border: false,
        defaults: {
            border: false,
            autoHeight: true
        },
        items: [
            {
                html: '<strong>' + _('uploadtousers.username') + ': ' + config.node.attributes.username + ' (' + config.node.attributes.uid + ')</strong><br>' +
                        '<strong>' + _('uploadtousers.file.dirPath') + ': ' + config.node.attributes.dirPath + '</strong>'
            }, {
                xtype: 'uploadtousers-grid-files',
                node: config.node
            }
        ]
    });

    Uploadtousers.panel.Folder.superclass.constructor.call(this, config);
};

Ext.extend(Uploadtousers.panel.Folder, MODx.Panel, {
});
Ext.reg('uploadtousers-panel-folder', Uploadtousers.panel.Folder);
