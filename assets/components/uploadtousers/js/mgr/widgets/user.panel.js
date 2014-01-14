Uploadtousers.panel.User = function(config) {
    config = config || {};

    var check = Ext.getCmp('uploadtousers-panel-user');
    if (check) {
        check.destroy();
    }

    Ext.applyIf(config, {
        id: 'uploadtousers-panel-user',
        border: false,
        defaults: {
            border: false,
            autoHeight: true
        },
        items: [
            {
                html: '<strong>' + _('uploadtousers.username') + ': ' + config.node.attributes.text + '</strong><br>' +
                        '<strong>' + _('uploadtousers.file.dirPath') + ': ' + config.node.attributes.dirPath + '</strong>'
            }, {
                xtype: 'uploadtousers-grid-files',
                node: config.node
            }
        ]
    });

    Uploadtousers.panel.User.superclass.constructor.call(this, config);
};

Ext.extend(Uploadtousers.panel.User, MODx.Panel, {
});
Ext.reg('uploadtousers-panel-user', Uploadtousers.panel.User);
