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
<table border="0" cellspacing="2">
    <tbody>
        <tr>
            <td rowspan="4" width="100%">
                {{if $isMapExpanded}}
                    <img width="420px" height="84px" src="{$logoUrl}" alt=""/>
                {{else}}
                    <img width="300px" height="60px" src="{$logoUrl}" alt=""/>
                {{/if}}
            </td>
        </tr>
    </tbody>
    </table>
    <p>&nbsp;</p>
    <table style="width: 100%;" border="0" cellspacing="2">
    <tbody>
        <tr>
            <td rowspan="1"><img src="{$map}&text=.png" alt="" /></td>
        </tr>
    </tbody>
</table>
<p>&nbsp;</p>
<p>&nbsp;</p>
<h3>{$mapPoints}:</h3>
<table style="width: 100%;" border="0">
    <tbody>
        <tr style="color: #ffffff;" bgcolor="#4B4B4B">
                <td style="text-align:center" width="40%">{$headerPoint}</td>
                <td style="text-align:center" width="120%">{$headerName}</td>
                <td style="text-align:center" width="85%">{$headerLat}</td>
                <td style="text-align:center" width="85%">{$headerLong}</td>
                <td style="text-align:center" width="170%">{$headerAddress}</td>
        </tr>
        {{foreach from=$recordsMeta item="record"}}
        <!--START_GEOCODE_POINTS_LOOP-->
        <tr>
                <td style="text-align:center" width="40%">{$record.point}</td>
                <td style="text-align:center" width="120%">{$record.parent_name}</td>
                <td style="text-align:center" width="85%">{$record.latitude}</td>
                <td style="text-align:center" width="85%">{$record.longitude}</td>
                <td style="text-align:center" width="170%">{$record.address}</td>
        </tr>
        <!--END_GEOCODE_POINTS_LOOP-->
    </tbody>
    {{/foreach}}
</table>
