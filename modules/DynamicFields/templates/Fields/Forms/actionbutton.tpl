{*
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */
*}
{include file='modules/DynamicFields/templates/Fields/Forms/coreTop.tpl'}
<script type='text/javascript'>

window.abSettings = '{$ACTIONBUTTON_SETTINGS}';
window.abModule = '{$ACTIONBUTTON_MODULE}';
window.abModules = '{$ACTIONBUTTON_MODULES}';
window.abLabel = '{$lbl_value}';

{literal}
function setupActionButtons() {
    // we encode all the strings so we won't have any issues with formula or more complex strings
    var encodeData = function(data, encode) {
        _.each(data, function parseButtons(childData, key){
            if (typeof childData === 'object' && childData !== null) {
                data[key] = encodeData(childData, encode);
            } else if (typeof childData === 'string') {
                data[key] = encode ? btoa(encodeURIComponent(childData)) : decodeURIComponent(atob(childData));
            }
        });
        return data;
    };

    var saveCallback = function(data) {
        var ext4Element = document.getElementById('ext4');

        ext4Element.value  = JSON.stringify(encodeData(data, true));
        window.abSettings  = ext4Element.value;
    };

    var cancelCallback = function() {
    };

    // populate the model with everything that's needed inside studio
    var abData = encodeData(JSON.parse(window.abSettings), false);
    var abModel = app.data.createBean('ActionButton');

    // make sure the abData is valid
    if (!abData.actionMenu) {
        abData.actionMenu = {};
    }

    abModel.set({
        data: abData,
        label: window.abLabel,
        module: window.abModule,
        modules: JSON.parse(window.abModules),
        encodeData: encodeData,
    });

    // open up the config drawer
    app.drawer.open({
        layout: 'actionbutton-setup',
        context: {
            model: abModel,
            module: 'Administration',
            saveCallback: saveCallback,
            cancelCallback: cancelCallback,
        }
    });
};

function trySetup() {
    app.metadata.sync(function() {
        if (!app.drawer.getActive()) {
            setupActionButtons();
        }
    });
};
{/literal}
</script>

<script>
{literal}
document.getElementById('ext4').value = window.abSettings;
document.getElementById('validateConfiguration').value = window.abSettings;
{/literal}
</script>

<tr>
    <td class='mbLBL'>{sugar_translate module='DynamicFields' label='LBL_ACTIONBUTTON_CONFIG'}:</td>
    <input
        id='validateConfiguration'
        name='validateConfiguration'
        style='display: none;'
        value='window.abSettings'>
    <script>
            {literal}
            addToValidateCallback(
                'popup_form',
                'validateConfiguration',
                'callback',
                true,
                app.lang.get('LBL_ACTIONBUTTON_CONFIG'),
                (
                    function validateData(nameExceptions, existingFields) {
                    return function checkValidData(formName, fieldName, index) {
                        // a button with no actions is not a valid button
                        if(window.abSettings){
                            var abData = JSON.parse(window.abSettings);

                            if(Object.keys(abData.buttons).length === 0){
                                validate[formName][index][msgIndex] = app.lang.get('LBL_ACTIONBUTTON_CONFIG_ERROR');
                                return false;
                            }
                        } else {
                            validate[formName][index][msgIndex] = app.lang.get('LBL_ACTIONBUTTON_CONFIG_ERROR');
                            return false;
                        }

                        return true;
                    }
                })({/literal}{$field_name_exceptions}, {$existing_field_names}));
    </script>
    <td>
        <input
            type='button'
            value='{sugar_translate module='DynamicFields' label='LBL_ACTIONBUTTON_CONFIG_BTN'}'
            onclick='trySetup.call(this)'>
    </td>
    <div>
        <input id='ext4' type='text' name='ext4' value='' style='display: none;'>
    </div>
</tr>
