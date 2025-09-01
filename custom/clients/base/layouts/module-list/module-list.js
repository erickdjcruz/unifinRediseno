({
	/* File: ./custom/clients/base/layouts/module-list/module-list.js */
	
	extendsFrom: 'ModuleListLayout',
	
	_addDefaultMenus: function() {
		var moduleList = app.metadata.getModuleNames({filter: 'display_tab', access: 'read'});
        //moduleList.push('Expediente');
        // moduleList.push('ExpedienteUniclick');
		var creditaria = 0;
		var roles = app.user.attributes.roles;
		for(var i=0;i<roles.length;i++)
		{
			if(roles[i] == "Seguros - Creditaria")
			{
				creditaria = 1;
			}
		}
		if(!creditaria) moduleList.splice(8, 0, 'Quantico');
        moduleList.push('CotizadorProspectos');
        //moduleList.push('Quantico');
        _.each(moduleList, function(module) {
			this._addMenu(module, true);
        }, this);
	},

    _addMenu: function(module, sticky) {
        var menu = {};
        var filter = 'favorites';
        var alias = false;
        
        switch(module) {
            case 'Expediente':
                filter = 'all_records';
                alias = true;
                break;

            case 'CotizadorProspectos':
                filter = 'all_records';
                alias = false;
                break;

            case 'Quantico':
                filter = 'all_records';
                alias = true;
                break;
        }

        var def = {
            view: {
            	filter: filter,
            	alias: alias,
                name: 'module-menu',
                sticky: sticky,
                short: false
            }
        };

        menu.long = this.createComponentFromDef(def, null, module);
        
        this.addComponent(menu.long, def);

        if (!sticky) {
            return menu;
        }

        def = {
            view: {
            	filter: filter,
            	alias: alias,
                name: 'module-menu',
                short: true
            }
        };
        menu.short = this.createComponentFromDef(def, null, module);
        this.addComponent(menu.short, def);

        return menu;
    },
})
