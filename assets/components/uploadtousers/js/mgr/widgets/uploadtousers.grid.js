Uploadtousers.grid.Uploadtousers = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();

    Ext.QuickTips.init();

    var getListView = function (data, element) {
        var dataArray = [];
        var innerDataArray = [];
        Ext.each(data, function(items){
            innerDataArray = [items.name, items.url, items.size, items.lastmod];
            dataArray.push(innerDataArray);
        }, this);
        var dataStore = new Ext.data.ArrayStore({
            fields: ['name', 'url', {
                name:'size',
                type: 'float'
            },{
                name:'lastmod',
                type:'date',
                dateFormat:'timestamp'
            }],
            data : dataArray
        });

        var rowUrl = '';
        var deleteFile = function (item, event) {
            Ext.Msg.show({
                title: _('uploadtousers.delete.confirmation.title'),
                msg: _('uploadtousers.delete.confirmation.body.file')+" "+rowUrl,
                buttons: Ext.Msg.YESNO,
                icon: Ext.Msg.QUESTION,
                fn: function(btnID, text, opt) {
                    if (btnID == 'yes') {
                        if (rowUrl != '') {
                            MODx.Ajax.request({
                                url: config.url,
                                params: {
                                    action: 'mgr/files/delete',
                                    file: rowUrl
                                },
                                listeners: {
                                    'success': {
                                        fn:function(response, opts) {
                                            fp.getForm().reset();
                                            clearPanels();
                                            Ext.MessageBox.alert(_('uploadtousers.success'), response.message);
                                        },
                                        scope:this
                                    },
                                    'failure': {
                                        fn: function(response, opts) {
                                            console.log('server-side failure with status code ' + response.status);
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
        var rowMenu = new Ext.menu.Menu({
            items: [{
                text: 'Delete',
                iconCls : 'icon-uninstall',
                handler: deleteFile
            }]
        });
        var listView = new Ext.list.ListView({
            store: dataStore,
            multiSelect: false,
            emptyText: '',
            reserveScrollOffset: true,
            columns: [{
                header: 'File',
                width: .5,
                dataIndex: 'name'
            },{
                header: 'Last Modified',
                xtype: 'datecolumn',
                format: 'm-d h:i a',
                width: .35,
                dataIndex: 'lastmod'
            },{
                header: 'Size',
                dataIndex: 'size',
                tpl: '{size:fileSize}',
                align: 'right',
                cls: 'listview-filesize'
            }],
            listeners: {
                contextmenu: {
                    fn: function (object, index, node, event ) {
                        event.preventDefault();
                        rowUrl = object.store.getAt(index).get("url");
                        return rowMenu.showAt(event.getXY());
                    }
                }
            }
        });

        element && listView.render(element);
        return listView;
    }

    var expander = new Ext.grid.RowExpander({
        tpl : '<div class="ux-row-expander-box"></div>',
        expandOnDblClick: false,
        enableCaching: false,
        dataIndex: 'files',
        listeners: {
            expand: function(expander, record, body, rowIndex) {
                return getListView(record.data.files, Ext.get(this.grid.getView().getRow(rowIndex)).child('.ux-row-expander-box'));
            }
        }
    });

    var fp = new MODx.FormPanel({
        fileUpload: true,
        width: 300,
        frame: true,
        bodyStyle: 'padding: 10px 10px 0 10px;',
        labelWidth: 60,
        defaults: {
            anchor: '95%',
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
                if(fp.getForm().isValid()){
                    var file = Ext.get('form-file').getValue();
                    if (!file){
                        Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.file_err_ns'));
                        return false;
                    }
                    var users = config.sm.getSelections();
                    if(!users || !users.length) {
                        Ext.MessageBox.alert(_('uploadtousers.error'), _('uploadtousers.users_err_ns'));
                        return false;
                    }

                    var ids = '';
                    var usernames = '';
                    Ext.each(users, function(ar){
                        ids += ','+ar.data.id;
                        usernames += ','+ar.data.username;
                    },this);
                    ids = ids.substr(1);
                    usernames = usernames.substr(1);

                    fp.getForm().submit({
                        url: config.url,
                        params: {
                            action: 'mgr/files/upload',
                            ids: ids,
                            usernames: usernames
                        },
                        waitMsg: _('uploadtousers.waiting_msg'),
                        success: function(fp, o){
                            Ext.MessageBox.alert(_('uploadtousers.success'), o.result.message);

                            /* clear up */
                            fp.reset();
                            clearPanels();
                        }
                    });
                }
            }
        },{
            text: _('uploadtousers.reset'),
            handler: function(){
                fp.getForm().reset();
            }
        }]
    });

    Ext.applyIf(config,{
        id: 'uploadtousers-grid-uploadtousers',
        url: Uploadtousers.config.connectorUrl,
        method: 'GET',
        baseParams: {
            action: 'mgr/users/getList'
        },
        fields: ['id','username','fullname','email','files','upload'],
        paging: true,
        remoteSort: true,
        sm: this.sm,
        anchor: '97%',
        autoExpandColumn: 'name',
        fileUpload : true,
        columns: [
        expander,
        this.sm,
        {
            header: _('id'),
            dataIndex: 'id',
            sortable: true,
            width: 30
        },{
            header: _('uploadtousers.username'),
            dataIndex: 'username',
            sortable: true,
            width: 100
        },{
            header: _('uploadtousers.fullname'),
            dataIndex: 'fullname',
            sortable: true,
            width: 100
        },{
            header: _('uploadtousers.email'),
            dataIndex: 'email',
            sortable: true,
            width: 100
        }],
        tbar:[
        fp,'->',{
            xtype: 'textfield',
            id: 'uploadtousers-search-filter',
            emptyText: _('uploadtousers.search...'),
            listeners: {
                'change': {
                    fn:this.search,
                    scope:this
                },
                'render': {
                    fn: function(cmp) {
                        new Ext.KeyMap(cmp.getEl(), {
                            key: Ext.EventObject.ENTER,
                            fn: function() {
                                this.fireEvent('change',this);
                                this.blur();
                                expander.collapseAll();
                                return true;
                            },
                            scope: cmp
                        });
                    },
                    scope:this
                }
            }
        }],
        plugins: expander,
        collapsible: true,
        animCollapse: false,
        iconCls: 'icon-grid'
    });

    function clearPanels() {
        config.sm.clearSelections();
        var grid = Ext.getCmp('uploadtousers-grid-uploadtousers');
        grid.getStore().commitChanges();
        grid.refresh();
        expander.collapseAll();
    }

    Uploadtousers.grid.Uploadtousers.superclass.constructor.call(this,config);
};
Ext.extend(Uploadtousers.grid.Uploadtousers,MODx.grid.Grid,{
    search: function(tf,nv,ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
});

Ext.reg('uploadtousers-grid-uploadtousers',Uploadtousers.grid.Uploadtousers);
