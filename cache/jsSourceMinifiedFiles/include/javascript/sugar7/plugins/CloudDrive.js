(function(app){app.events.on('app:init',function(){app.plugins.register('CloudDrive',['view'],{_searchForDashlet:function(type){let layout=app.controller.layout;if(layout.name!=='record'){return false;}
let sidebar=layout.getComponent('sidebar');if(!sidebar){return false;}
let dashboardPane=sidebar.getComponent('dashboard-pane');if(!dashboardPane){return false;}
let base=dashboardPane.getComponent('base');let baseDashboard=base.getComponent('dashboard');if(!baseDashboard){return false;}
let dashletMain=baseDashboard.getComponent('dashlet-main');if(!dashletMain){return false;}
let dashlets=dashletMain.getComponent('dashboard-grid');if(!dashlets){return false;}
for(let dashlet of dashlets._components){if(dashlet.getComponent('cloud-drive')){let _dashlet=dashlet.getComponent('cloud-drive');if(_dashlet&&_dashlet.options.driveType===type){return{cid:_dashlet.cid,pathFolders:_dashlet.pathFolders,};}}}
return false;},syncDriveDashlet:function(dashletCid,result){app.alert.show('file-upload',{level:'info',messages:app.lang.get(result.message),});app.events.trigger(`${dashletCid}:cloud-drive:reload`);},});});})(SUGAR.App);