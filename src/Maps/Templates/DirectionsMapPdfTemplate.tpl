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
<p>&nbsp;</p>
<p>&nbsp;</p>
<h3>{$travelDetails}:</h3>
<table style="width: 100%;" border="0">
    <ul>
        <li>{$totalDistanceLbl}: {$totalDistance}</li>
        <li>{$totalDurationLbl}: {$totalDuration}</li>
        <li>{$totalDurationWithoutTraficLbl}: {$totalDurationWithoutTrafic}</li>
        <br>
        <li>{$itineraryLbl}:</li>
            <ul>
                {{foreach from=$itinerary item=itineraryItem}}
                    <li>{$fromLbl} {$itineraryItem.startPoint} {$toLbl} {$itineraryItem.endPoint}
                    <ul>
                        <li>{$travelDistanceLbl}: {$itineraryItem.travelDistance}</li>
                        <li>{$travelDurationLbl}: {$itineraryItem.travelDuration}</li>
                        <li>{$travelStepsLbl}:</li>
                        <ul>
                            {{foreach from=$itineraryItem.steps item=itineraryStep}}
                                <li>
                                    {$itineraryStep.text}
                                    <ul>
                                        <li>{$itineraryStep.travelDistance}</li>
                                        <li>{$itineraryStep.travelDuration}</li>
                                        {{if !empty($itineraryStep.warnings)}}
                                            <li>
                                                {$travelWarningsLbl}:
                                                    <ul>
                                                        {{foreach from=$itineraryStep.warnings item=warning}}
                                                            <li>{$warning.text}</li>
                                                        {{/foreach}}
                                                    </ul>
                                            </li>
                                        {{/if}}
                                    </ul>
                                </li>
                            {{/foreach}}
                        </ul>
                    </ul>
                {{/foreach}}
            </ul>
    </ul>
</table>
