Uploadtousers.grid.Files = function(config) {
    config = config || {};

    var checkbox = new Ext.grid.CheckboxSelectionModel({
        checkOnly: true,
        listeners: {
            'selectionchange': {
                fn: function() {
                    return this.toogleDeleteButton();
                },
                scope: this
            }
        }
    });

    Ext.applyIf(config, {
        id: 'uploadtousers-grid-files',
        url: Uploadtousers.config.connectorUrl,
        method: 'GET',
        baseParams: {
            action: 'mgr/directories/getFiles',
            dir: config.node.attributes.dirPath
        },
        fields: ['id', 'title', 'description', 'name', 'lastmod', 'size', 'dirPath'],
        paging: false,
        autoExpandColumn: 'name',
        fileUpload: true,
        border: false,
        autoHeight: true,
        iconCls: 'icon-grid',
        sm: checkbox,
        columns: [
            checkbox,
            {
                header: _('uploadtousers.file.title'),
                dataIndex: 'title',
                sortable: true
            },
            {
                header: _('uploadtousers.file.desc'),
                dataIndex: 'description',
                sortable: true
            },
            {
                header: _('uploadtousers.file'),
                dataIndex: 'name',
                width: 80,
                sortable: true
            }, {
                header: _('uploadtousers.modified'),
                dataIndex: 'lastmod',
                width: 80,
                sortable: false,
                renderer: function(value) {
                    var date = new Date(value);
                    return date.format('Y-m-d');
                }
            }, {
                header: _('uploadtousers.size'),
                dataIndex: 'size',
                align: 'right',
                cls: 'listview-filesize',
                width: 80,
                sortable: false,
                renderer: function(value) {
                    return Ext.util.Format.fileSize(value);
                }
            }
        ],
        tbar: [
            {
                text: _('uploadtousers.refresh'),
                handler: this.refresh
            }, {
                text: _('uploadtousers.upload'),
                handler: function() {
                    var fileWin = new Uploadtousers.window.FileUpload({
                        blankValues: true,
                        node: config.node ? config.node : ''
                    });
                    return fileWin.show();
                }
            }, {
                text: _('uploadtousers.delete'),
                id: 'uploadtousers-grid-tbar-delete',
                disabled: true,
                handler: this.deleteFileConfirm
            }
        ]
    });

    Uploadtousers.grid.Files.superclass.constructor.call(this, config);
};

Ext.extend(Uploadtousers.grid.Files, MODx.grid.Grid, {
    getMenu: function() {
        return [
            {
                text: _('uploadtousers.edit'),
                handler: this.editFile
            }, '-', {
                text: _('uploadtousers.delete'),
                handler: this.deleteFileConfirm
            }
        ];
    },
    editFile: function(btn, e) {
        var fileWindow = MODx.load({
            xtype: 'uploadtousers-window-file',
            title: _('uploadtousers.edit'),
            baseParams: {
                action: 'mgr/files/update',
                id: this.menu.record.id
            },
            listeners: {
                'success': {
                    fn: this.refresh,
                    scope: this
                }
            }
        });

        fileWindow.setValues(this.menu.record);
        return fileWindow.show(e.target);
    },
    deleteFileConfirm: function(object, event) {
        var selectedFiles = this.getSelectionModel().getSelections();
        var paths = '';
        var pathsArray = [];
        Ext.each(selectedFiles, function(item, i) {
            var filePath = item.data.dirPath + item.data.name;
            pathsArray.push(filePath);
            paths += ',' + filePath;
        });
        paths = paths.substr(1);
        var _this = this;
        if (paths.length > 0) {
            var pathsString = pathsArray.join('<br>'), msg;
            if (pathsArray.length > 1) {
                msg = _('uploadtousers.delete.confirmation.body.files');
            } else {
                msg = _('uploadtousers.delete.confirmation.body.file');
            }
            return Ext.Msg.show({
                title: _('uploadtousers.delete.confirmation.title'),
                msg: msg + "<br>" + pathsString,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function(btnID, text, opt) {
                    if (btnID === 'yes') {
                        MODx.Ajax.request({
                            url: Uploadtousers.config.connectorUrl,
                            params: {
                                action: 'mgr/files/delete',
                                paths: paths
                            },
                            listeners: {
                                'success': {
                                    fn: function(response, opts) {
                                        _this.refresh();
                                        return Ext.MessageBox.alert(_('uploadtousers.success'), response.message);
                                    },
                                    scope: this
                                },
                                'failure': {
                                    fn: function(response, opts) {
                                        console.log('server-side failure with status code ' + response.status);
                                        return Ext.MessageBox.alert(_('uploadtousers.error'), response.message);
                                    }
                                }
                            }
                        });
                    }
                }
            });
        } else {
            Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.error.file.ne'));
            return false;
        }
    },
    toogleDeleteButton: function() {
        var selectedFiles = this.getSelectionModel().getSelections();
        var deleteBtn = Ext.getCmp('uploadtousers-grid-tbar-delete');
        if (selectedFiles.length > 0) {
            deleteBtn.enable();
        } else {
            deleteBtn.disable();
        }
    }
});
Ext.reg('uploadtousers-grid-files', Uploadtousers.grid.Files);