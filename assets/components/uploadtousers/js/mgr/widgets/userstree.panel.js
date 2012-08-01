Uploadtousers.panel.Userstree = function(config) {
    config = config || {};

    Ext.QuickTips.init();

    /**
    * Folder's name
    **/
    var newFolderDialog = function() {
        var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
        var selectedNode = usersTree.selNode;
        Ext.MessageBox.prompt(_('uploadtousers.createfolder.foldername'),
            _('uploadtousers.createfolder.foldername_label'), function(btn, text){
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
                                fn:function(response, opts) {
                                    refreshTree();
                                    usersTree.getLoader().load(usersTree.selNode);
                                    return Ext.MessageBox.alert(_('uploadtousers.success'), response.message);
                                },
                                scope:this
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
    };

    /**
    * Confirm box to create a new folder
    **/
    var createFolderConfirm = function (item, event) {
        return Ext.Msg.show({
            title: _('uploadtousers.createfolder.confirmation.title'),
            msg: _('uploadtousers.createfolder.confirmation.body.file'),
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            fn: function(btnID, text, opt) {
                if (btnID == 'yes') {
                    return newFolderDialog();
                }
            }
        });
    };

    /**
    * Confirm box to create a new folder
    **/
    var deleteFolderConfirm = function(item, event) {
        var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
        var selectedNode = usersTree.selNode;
        var dirPath = selectedNode.attributes.dirPath;
        return Ext.Msg.show({
            title: _('uploadtousers.delete.confirmation.title'),
            msg: _('uploadtousers.delete.confirmation.body.file')+" "+dirPath,
            buttons: Ext.Msg.YESNO,
            icon: Ext.Msg.QUESTION,
            fn: function(btnID, text, opt) {
                if (btnID == 'yes') {
                    if (dirPath != '') {
                        MODx.Ajax.request({
                            url: Uploadtousers.config.connectorUrl,
                            params: {
                                action: 'mgr/directories/delete',
                                dirPath: dirPath
                            },
                            listeners: {
                                'success': {
                                    fn:function(response, opts) {
                                        refreshTree();
                                        return Ext.MessageBox.alert(_('uploadtousers.success'), response.message);
                                    },
                                    scope:this
                                },
                                'failure': {
                                    fn: function(response, opts) {
                                        console.log('server-side failure with status code ' + response.status);
                                        return Ext.MessageBox.alert(_('uploadtousers.error'), response.message);
                                    }
                                }
                            }
                        });
                    } else {
                        Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.error.file.ne'));
                        return false;
                    }
                }
            }
        });
    };

    /**
     * File upload form
     **/
    var formPanel = new MODx.FormPanel({
        id: 'uploadfile-form',
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
            id: 'form-file',
            emptyText: '',
            name: 'file',
            buttonText: _('uploadtousers.browse')
        }],
        buttons: [{
            text: _('uploadtousers.upload'),
            handler: function(){
                if(formPanel.getForm().isValid()){
                    var file = Ext.get('form-file').getValue();
                    if (!file){
                        Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.file_err_ns'));
                        return false;
                    }

                    var contentsGrid = Ext.getCmp('uploadtousers-grid-contents');
                    var store = contentsGrid.getStore();
                    var dirPath = store.baseParams.dir;

                    formPanel.getForm().submit({
                        url: Uploadtousers.config.connectorUrl,
                        params: {
                            action: 'mgr/files/upload',
                            path: dirPath
                        },
                        waitMsg: _('uploadtousers.waiting_msg'),
                        success: function(fp, o){
                            Ext.MessageBox.alert(_('uploadtousers.success'), o.result.message);
                            fp.reset();
                            Ext.getCmp('uploadfile-windowbox').hide();
                            return refreshContentsGrid();
                        }
                    });
                }
            }
        },{
            text: _('uploadtousers.reset'),
            handler: function(){
                return formPanel.getForm().reset();
            }
        },{
            text: _('uploadtousers.cancel'),
            handler: function(){
                return Ext.getCmp('uploadfile-windowbox').hide();
            }
        }]
    });

    var refreshContentsGrid = function() {
        var contentsGrid = Ext.getCmp('uploadtousers-grid-contents');
        var store = contentsGrid.getStore();
        store.load();
        return contentsGrid.getView().refresh();
    }

    var uploadFile = function (object, event) {
        var uploadFileWindow = Ext.getCmp('uploadfile-windowbox');
        if (uploadFileWindow) {
            var uploadFileForm = Ext.getCmp('uploadfile-form');
            if (uploadFileForm)
                uploadFileForm.getForm().reset();

            return uploadFileWindow.show();
        } else {
            return new Ext.Window({
                id: 'uploadfile-windowbox',
                layout: 'fit',
                title: _('uploadtousers.file.select'),
                width: 300,
                height: 160,
                modal: true,
                closeAction: 'hide',
                items: formPanel
            }).show();
        }
    }

    /**
    * Right click menu
    **/
    var treeContextMenu = new Ext.menu.Menu({
            items: [{
//                text: 'Upload File',
//                handler: uploadFile
//            },'-',{
                text: _('uploadtousers.createfolder'),
                handler: createFolderConfirm
            },'-',{
                text: _('uploadtousers.delete'),
                handler: deleteFolderConfirm
            }]
        });

    var deleteFileConfirm = function(object, event) {
        var filesGrid = Ext.getCmp('uploadtousers-grid-contents');
        var checked = filesGrid.getSelectionModel().getSelections();
        var paths = '';
        Ext.each(checked, function(item,i){
            paths += ',' + item.data.url;
        });
        paths = paths.substr(1);
        if (paths.length > 0) {
            MODx.Ajax.request({
                url: Uploadtousers.config.connectorUrl,
                params: {
                    action: 'mgr/files/delete',
                    paths: paths
                },
                listeners: {
                    'success': {
                        fn:function(response, opts) {
                            refreshContentsGrid();
                            return Ext.MessageBox.alert(_('uploadtousers.success'), response.message);
                        },
                        scope:this
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

    /**
    * Right click menu
    **/
    var fileContextMenu = new Ext.menu.Menu({
            items: [{
                text: _('uploadtousers.delete'),
                handler: deleteFileConfirm
            }]
        });

    var refreshTree = function(parentId){
        parentId = Number(parentId) ? Number(parentId) : 0;

        var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
        usersTree.getLoader().baseParams = {
            action: 'mgr/users/getList'
        };
        return usersTree.getLoader().load(usersTree.root);
    }

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
                    Ext.each(dataArray, function(user,i){
                        root.appendChild(user);
                    });
                } else {
                    var childData = Ext.util.JSON.decode(response.responseText);
                    var childDataArray = childData.object;
                    Ext.each(childDataArray, function(child,i){
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
                    if (dirPath){
                        var encDirPath = encodeURIComponent(dirPath);
                    }
                    this.baseParams.dirPath = encDirPath;
                }
            }
        }
    });

    var centerPanel = function(node) {
        var headersContent = '', contentsHeaderHeight;
        if (node.attributes.email) {
            headersContent += node.attributes.text + '<br \/>';
            headersContent += node.attributes.fullname ? node.attributes.fullname + '<br \/>' : '';
            headersContent += node.attributes.email ? node.attributes.email + '<br \/>' : '';
            contentsHeaderHeight = 80;
        } else {
            contentsHeaderHeight = 0;
        }
        var contentsHeader = new Ext.Panel({
            id: 'uploadtousers-contents-header',
            region: 'north',
            border: false,
            autoHeight: true,
            height: contentsHeaderHeight,
            margins: '0 0 0 0',
            cmargins: '0 0 0 0',
            html: headersContent
        });

        var checkbox = new Ext.grid.CheckboxSelectionModel();

        var contentsGrid = new MODx.grid.Grid({
            id: 'uploadtousers-grid-contents',
            region: 'center',
            border: false,
            autoHeight: true,
            margins: '0 0 0 0',
            cmargins: '0 0 0 0',
            tbar: [{
                id: 'uploadtousers-menu-contents-refresh',
                text: _('uploadtousers.refresh'),
                handler: refreshContentsGrid
            },{
                id: 'uploadtousers-menu-contents-upload',
                text: _('uploadtousers.upload'),
                handler: uploadFile
            },{
                id: 'uploadtousers-menu-contents-delete',
                text: _('uploadtousers.delete'),
                handler: deleteFileConfirm
            }],

            url: Uploadtousers.config.connectorUrl,
            method: 'GET',
            baseParams: {
                action: 'mgr/directories/getFiles',
                dir: node.attributes.dirPath
            },
            fields: ['name','lastmod','size','url','parentUrl'],

//            paging: true,
//            remoteSort: true,
            autoExpandColumn: 'name',
            fileUpload : true,
            sm: checkbox,
            columns: [
            checkbox,
            {
                header: _('uploadtousers.file'),
                dataIndex: 'name',
                sortable: true
            },{
                header: _('uploadtousers.lastmodified'),
                dataIndex: 'lastmod',
                renderer: function(value) {
                    var date = new Date(value);
                    return date.format('Y-m-d');
                }
            },{
                header: _('uploadtousers.size'),
                dataIndex: 'size',
                align: 'right',
                cls: 'listview-filesize',
                renderer: function(value) {
                    return Ext.util.Format.fileSize(value);
                },
                sortable: false
            }],
            collapsible: true,
            animCollapse: false,
            iconCls: 'icon-grid',
            remove: function(){} // overrides MODX grid's config
        });

        var contentsPanel = Ext.getCmp('uploadtousers-panel-contents');
        contentsPanel.update({
            layout: 'border',
            autoHeight: true
        });
        contentsPanel.add(contentsHeader, contentsGrid);

        var container = Ext.getCmp('modx-content');

        return container.doLayout();
    }

    Ext.apply(config,{
        id: 'uploadtousers-panel-userstree',
        xtype: 'treepanel',
//        xtype: 'modx-tree',
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
            handler: function(){
                var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
                usersTree.expandAll();
            }
        },{
            id: 'uploadtousers-menu-userstree-collapse-all',
            text: _('uploadtousers.userstree.collapseall'),
            handler: function(){
                var usersTree = Ext.getCmp('uploadtousers-panel-userstree');
                usersTree.collapseAll();
            }
        },{
            id: 'uploadtousers-menu-userstree-refresh',
            text: _('uploadtousers.refresh'),
            handler: refreshTree
        }],
        title: _('uploadtousers.userstree.title'),
        autoScroll: true,
        enableDD: false,
        containerScroll: true,
        listeners: {
            click: function(node) {
                return centerPanel(node);
            },
            contextmenu: function (node, event) {
                event.preventDefault();
                this.selNode = node;
                return treeContextMenu.showAt(event.getXY());
            },
            render: function() {
                this.getRootNode().expand(true);
            },
            collapse: function(panel) {
                var contentsPanel = Ext.getCmp('uploadtousers-panel-contents');
                return contentsPanel.doLayout();
            },
            expand: function(panel) {
                var contentsPanel = Ext.getCmp('uploadtousers-panel-contents');
                return contentsPanel.doLayout();
            }
        }
//        ,_saveState: function(n) {} // overrides MODX grid's config
    });

    Uploadtousers.panel.Userstree.superclass.constructor.call(this,config);
};

Ext.extend(Uploadtousers.panel.Userstree, Ext.tree.TreePanel);
//Ext.extend(Uploadtousers.panel.Userstree, MODx.tree.Tree);
Ext.reg('uploadtousers-panel-userstree', Uploadtousers.panel.Userstree);