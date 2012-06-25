var Uploadtousers = function(config) {
    config = config || {};
    Uploadtousers.superclass.constructor.call(this,config);
};
Ext.extend(Uploadtousers,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('uploadtousers',Uploadtousers);
Uploadtousers = new Uploadtousers();