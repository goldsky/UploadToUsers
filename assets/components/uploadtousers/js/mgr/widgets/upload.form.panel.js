Uploadtousers.panel.UploadForm = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        fileUpload: true,
        width: 300,
        frame: true,
        bodyStyle: 'padding: 10px 10px 0 10px;',
        labelWidth: 60,
        defaults: {
            anchor: '100%',
            allowBlank: true,
            msgTarget: 'side'
        },
        items: [
            {
                xtype: 'fileuploadfield',
                id: 'uploadtousers-input-file',
                emptyText: '',
                name: 'file',
                buttonText: _('uploadtousers.browse')
            }
        ],
        buttons: [
            {
                text: _('uploadtousers.upload'),
                handler: this.upload,
                scope: this
            }, {
                text: _('uploadtousers.reset'),
                handler: function() {
                    return this.form.reset();
                },
                scope: this
            }, {
                text: _('uploadtousers.cancel'),
                handler: function() {
                    return Ext.getCmp('uploadtousers-window-file-upload').hide();
                }
            }
        ]
    });

    Uploadtousers.panel.UploadForm.superclass.constructor.call(this, config);
};

Ext.extend(Uploadtousers.panel.UploadForm, MODx.FormPanel, {
    upload: function() {
        if (this.getForm().isValid()) {
            var file = Ext.get('uploadtousers-input-file').getValue();
            if (!file) {
                Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.file_err_ns'));
                return false;
            }
            var _this = this;
            return this.getForm().submit({
                url: Uploadtousers.config.connectorUrl,
                params: {
                    action: 'mgr/files/upload',
                    path: _this.config.node.attributes.dirPath
                },
                waitMsg: _('uploadtousers.waiting_msg'),
                success: function(fp, o) {
                    Ext.MessageBox.alert(_('uploadtousers.success'), o.result.message);
                    fp.reset();
                    Ext.getCmp('uploadtousers-window-file-upload').close();
                    Ext.getCmp('uploadtousers-grid-files').refresh();
                    var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
                    return usersTree.getLoader().load(_this.config.node);
                },
                failure: function(fp, o) {
                    Ext.MessageBox.alert(_('uploadtousers.failure'), o.result.message);
                    console.log('_this.config.node', _this.config.node);
                    fp.reset();
                }
            });
        }
    }
});
Ext.reg('uploadtousers-panel-upload-form', Uploadtousers.panel.UploadForm);