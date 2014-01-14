Uploadtousers.window.FileUpload = function(config) {
    config = config || {};

    var check = Ext.getCmp('uploadtousers-window-file-upload');
    if (check) {
        check.destroy();
    }

    Ext.applyIf(config, {
        id: 'uploadtousers-window-file-upload',
        url: Uploadtousers.config.connectorUrl,
        baseParams: {
            action: 'mgr/files/upload',
            path: config.node.attributes.dirPath
        },
        layout: 'anchor',
        title: _('uploadtousers.file.select'),
        width: 300,
        blankValues: true,
        fileUpload: true,
        fields: [
            {
                xtype: 'fileuploadfield',
                id: 'uploadtousers-input-file',
                anchor: '100%',
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
                text: _('uploadtousers.cancel'),
                handler: function() {
                    config.closeAction !== 'close' ? this.hide() : this.close();
                },
                scope: this
            }
        ],
        success: function() {
            Ext.getCmp('uploadtousers-grid-files').refresh();
            var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
            return usersTree.getLoader().load(config.node);
        }
    });
    Uploadtousers.window.FileUpload.superclass.constructor.call(this, config);
};
Ext.extend(Uploadtousers.window.FileUpload, MODx.Window, {
    upload: function() {
        var file = Ext.get('uploadtousers-input-file').getValue();
        if (!file) {
            Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.file_err_ns'));
            return false;
        }

        return this.submit();
    },
    /* overriding */
    submit: function(close) {
        close = close === false ? false : true;
        var f = this.fp.getForm();

        if (f.isValid() && this.fireEvent('beforeSubmit', f.getValues())) {
            f.submit({
                waitMsg: _('uploadtousers.waiting_msg'),
                scope: this,
                failure: function(frm, a) {
                    if (this.fireEvent('failure', {f: frm, a: a})) {
                        MODx.form.Handler.errorExt(a.result, frm);
                    }
                },
                success: function(frm, a) {
                    if (this.config.success) {
                        Ext.callback(this.config.success, this.config.scope || this, [frm, a]);
                    }
                    this.fireEvent('success', {f: frm, a: a});
                    if (close) {
                        this.config.closeAction !== 'close' ? this.hide() : this.close();
                    }
                }
            });
        }
    }
});
Ext.reg('uploadtousers-window-file-upload', Uploadtousers.window.FileUpload);