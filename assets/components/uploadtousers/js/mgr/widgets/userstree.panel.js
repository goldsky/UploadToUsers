Uploadtousers.panel.Userstree = function(config) {
    config = config || {};

    Ext.QuickTips.init();

    var usersTree = new Ext.tree.TreeLoader({
        id: 'uploadtousers-treeloader-userstree',
        dataUrl: Uploadtousers.config.connectorUrl,
        baseParams: {
            action: 'mgr/users/getList'
        },
        listeners: {
            load: function(object, node, response) {
                if (node.attributes.id === 'usersRoot') {
                    var data = Ext.util.JSON.decode(response.responseText);
                    var dataArray = data.results;
                    var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
                    var root = usersTree.getRootNode();
                    Ext.each(dataArray, function(user, i) {
                        root.appendChild(user);
                    });
                } else {
                    var childData = Ext.util.JSON.decode(response.responseText);
                    var childDataArray = childData.object;
                    Ext.each(childDataArray, function(child, i) {
                        node.appendChild(child);
                    });
                }
                /* overide the loader */
                this.baseParams = {
                    action: 'mgr/directories/getFolders'
                            //,dirPath: escDirPath // defined below!
                };
            },
            beforeload: function(object, node, callback) {
                if (node.attributes.id !== 'usersRoot') {
                    var dirPath = node.attributes.dirPath;
                    if (dirPath) {
                        var encDirPath = encodeURIComponent(dirPath);
                    }
                    this.baseParams.dirPath = encDirPath;
                    this.baseParams.uid = node.attributes.uid;
                }
            }
        }
    });

    Ext.apply(config, {
        id: 'uploadtousers-panel-userstree',
        region: 'west',
        margins: '0 0 0 0',
        cmargins: '0 0 0 5',
        width: 300,
        autoHeight: true,
        minSize: 200,
        loader: usersTree,
        root: {
            nodeType: 'async',
            text: 'Users',
            draggable: false,
            id: 'usersRoot',
            expanded: true
        },
        rootVisible: false,
        tbar: [{
                id: 'uploadtousers-menu-userstree-expand-all',
                text: _('uploadtousers.userstree.expandall'),
                handler: function() {
                    var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
                    usersTree.expandAll(true);
                }
            }, {
                id: 'uploadtousers-menu-userstree-collapse-all',
                text: _('uploadtousers.userstree.collapseall'),
                handler: function() {
                    var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
                    usersTree.collapseAll();
                }
            }, {
                id: 'uploadtousers-menu-userstree-refresh',
                text: _('uploadtousers.refresh'),
                handler: this.refreshTree
            }],
        title: _('uploadtousers.userstree.title'),
        autoScroll: true,
        enableDD: false,
        containerScroll: true,
        listeners: {
            click: function(node) {
                if (node.attributes.type === 'user') {
                    return this.userPanel(node);
                } else {
                    return this.folderPanel(node);
                }
            },
            render: function() {
                this.getRootNode().expand(true);
            },
            collapse: function(panel) {
                var contentsPanel = Ext.getCmp('uploadtousers-panel-content');
                return contentsPanel.doLayout();
            },
            expand: function(panel) {
                var contentsPanel = Ext.getCmp('uploadtousers-panel-content');
                return contentsPanel.doLayout();
            }
        }
    });

    Uploadtousers.panel.Userstree.superclass.constructor.call(this, config);
};

Ext.extend(Uploadtousers.panel.Userstree, MODx.tree.Tree, {
    getMenu: function() {
        return [{
                text: _('uploadtousers.upload_file'),
                handler: this.uploadFile
            }, '-', {
                text: _('uploadtousers.createfolder'),
                handler: this.newFolderDialog
            }, '-', {
                text: _('uploadtousers.delete'),
                handler: this.deleteFolderConfirm
            }];
    },
    uploadFile: function(btn, e) {
        var selectedNode = this.getSelectionModel().getSelectedNode();
        var uploadFileWindow = MODx.load({
            xtype: 'uploadtousers-window-file-upload',
            node: selectedNode.attributes
        });
        return uploadFileWindow.show(e.target);
    },
    userPanel: function(node) {
        var contentPanel = Ext.getCmp('uploadtousers-panel-content');
        contentPanel.removeAll();
        contentPanel.add({
            xtype: 'uploadtousers-panel-user',
            node: node,
            preventRender: true
        });

        var container = Ext.getCmp('modx-content');

        return container.doLayout();
    },
    folderPanel: function(node) {
        var contentPanel = Ext.getCmp('uploadtousers-panel-content');
        contentPanel.removeAll();
        contentPanel.add({
            xtype: 'uploadtousers-panel-folder',
            node: node,
            preventRender: true
        });

        var container = Ext.getCmp('modx-content');

        return container.doLayout();
    },
    refreshTree: function(parentId) {
        parentId = Number(parentId) ? Number(parentId) : 0;

        var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
        usersTree.getLoader().baseParams = {
            action: 'mgr/users/getList'
        };
        return usersTree.getLoader().load(usersTree.root);
    },
    deleteFolderConfirm: function(item, event) {
        var selectedNode = this.getSelectionModel().getSelectedNode();
        var dirPath = selectedNode.attributes.dirPath;
        var _this = this;
        if (dirPath !== '') {
            return Ext.Msg.show({
                title: _('uploadtousers.delete.confirmation.title'),
                msg: _('uploadtousers.delete.confirmation.body.file') + "<br>" + dirPath,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function(btnID, text, opt) {
                    if (btnID === 'yes') {
                        MODx.Ajax.request({
                            url: Uploadtousers.config.connectorUrl,
                            params: {
                                action: 'mgr/directories/delete',
                                dirPath: dirPath
                            },
                            listeners: {
                                'success': {
                                    fn: function(response, opts) {
                                        _this.refreshTree();
                                        Ext.getCmp('uploadtousers-grid-files').refresh();
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
    newFolderDialog: function() {
        var selectedNode = this.getSelectionModel().getSelectedNode();
        var _this = this;
        Ext.MessageBox.prompt(_('uploadtousers.createfolder.foldername'),
                _('uploadtousers.createfolder.foldername_label'), function(btn, text) {
            if (btn === 'ok') {
                if (!text) {
                    return Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.createfolder.foldername_err_ns'));
                }
                MODx.Ajax.request({
                    url: Uploadtousers.config.connectorUrl,
                    params: {
                        action: 'mgr/directories/create',
                        parent: selectedNode.attributes.dirPath,
                        name: text
                    },
                    listeners: {
                        'success': {
                            fn: function(response, opts) {
                                _this.refreshTree();
                                var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
                                usersTree.getLoader().load(usersTree.selNode);
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
        });
    }
});
Ext.reg('uploadtousers-panel-userstree', Uploadtousers.panel.Userstree);