Ext.onReady(function() {
    MODx.load({ xtype: 'uploadtousers-page-home'});
});

Uploadtousers.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'uploadtousers-panel-home'
            ,renderTo: 'uploadtousers-panel-home-div'
        }]
    });
    Uploadtousers.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Uploadtousers.page.Home,MODx.Component);
Ext.reg('uploadtousers-page-home',Uploadtousers.page.Home);