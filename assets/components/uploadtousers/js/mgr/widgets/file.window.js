Uploadtousers.window.File = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        url: Uploadtousers.config.connectorUrl,
        stateful: false,
        monitorValid: true,
        autoHeight: true,
        resizable: false,
        preventRender: true,
        modal: true,
        bodyStyle: 'padding:5px;',
        buttonAlign: 'center',
        fields: [
            {
                xtype: 'hidden',
                name: 'id'
            }, {
                xtype: 'displayfield',
                fieldLabel: _('uploadtousers.file.dirPath'),
                name: 'dirPath'
            }, {
                xtype: 'displayfield',
                fieldLabel: _('uploadtousers.file.name'),
                name: 'name'
            }, {
                xtype: 'textfield',
                fieldLabel: _('uploadtousers.file.title'),
                name: 'title',
                msgTarget: 'side',
                anchor: '100%',
                listeners: {
                    scope: this,
                    specialkey: function(f, e) {
                        if (e.getKey() === e.ENTER) {
                            e.stopEvent();
                            this.submit();
                        }
                    },
                    afterrender: function(field) {
                        field.focus(false, 500);
                    }
                }
            }, {
                xtype: 'textarea',
                fieldLabel: _('uploadtousers.file.desc'),
                name: 'description',
                anchor: '100%'
            }
        ],
        keys: [{
                key: [Ext.EventObject.ENTER],
                handler: function(keyCode, event) {
                    var elem = event.getTarget();
                    var component = Ext.getCmp(elem.id);
                    if (component instanceof Ext.form.TextArea) {
                        return component.append("\n");
                    } else if (!this.fp.getForm().isValid()) {
                        return;
                    } else {
                        this.submit();
                    }

                },
                scope: this
            }]
    });
    Uploadtousers.window.File.superclass.constructor.call(this, config);
};
Ext.extend(Uploadtousers.window.File, MODx.Window);
Ext.reg('uploadtousers-window-file', Uploadtousers.window.File);