<?php
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
?>
<!DOCTYPE HTML>
<html>
<head>

    <script src='https://code.jquery.com/jquery-1.11.3.min.js'></script>

    <script>
        function getQueryValueOf(key) {
            return unescape(
                window.location.search.replace(
                    new RegExp('^(?:.*[&\\?]' + escape(key).replace(/[\.\+\*]/g, '\\$&') + '(?:\\=([^&]*))?)?.*$', 'i'),
                    '$1'
                )
            );
        }

        function saveDocusignEnvelopeRecord(saveData) {
            $.ajax({
                type: 'POST',
                url: apiRoot + '/DocuSign/saveBean',
                headers: {
                    'Content-Type': 'application/json',
                    'OAuth-Token': oauthToken,
                },
                data: JSON.stringify(saveData),
            }).always(function () {
                window.localStorage.setItem('docusignAction', status);
                window.localStorage.removeItem('docusignAction');
                window.close();
            });
        }

        function deleteEnvelope(deleteData) {
            $.ajax({
                type: 'POST',
                url: apiRoot + '/DocuSign/removeEnvelope',
                headers: {
                    'Content-Type': 'application/json',
                    'OAuth-Token': oauthToken,
                },
                data: JSON.stringify(deleteData),
            }).always(function () {
                window.localStorage.setItem('docusignAction', status);
                window.localStorage.removeItem('docusignAction');

                window.close();
            });
        }

        var oauthToken = getQueryValueOf('token');
        var apiRoot = '<?php echo $apiRoot;?>';

        var recordType = getQueryValueOf('parentRecord');
        var recordId = getQueryValueOf('parentId');
        var envelopeId = getQueryValueOf('envelopeId');
        var status = getQueryValueOf('event');
        if (status === 'Send') {
            status = 'sent';
        } else if (status === 'Save') {
            status = 'created';
        } else if (status === 'Cancel') {
            status = 'cancel';
        }

        if (status === 'cancel') {
            var deleteData = {
                envelopeId: envelopeId,
            };
            deleteEnvelope(deleteData);
        } else {
            var saveData = {
                envelopeId: envelopeId,
                parentType: recordType,
                parentId: recordId,
                status: status,
            };
            saveDocusignEnvelopeRecord(saveData);
        }
    </script>
</head>
<body style='position: absolute; top: 45%; left: 45%;'>
<img src='../../../styleguide/assets/img/loader.gif'/>
</body>
</html>
