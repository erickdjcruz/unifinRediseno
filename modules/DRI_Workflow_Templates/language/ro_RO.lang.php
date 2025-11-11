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
$mod_strings = [
    'LBL_HOMEPAGE_TITLE' => 'Șabloanele mele de ghid inteligent',
    'LBL_LIST_FORM_TITLE' => 'Listă cu șabloane de ghid inteligent',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Importare șabloane de ghid inteligent',
    'LBL_MODULE_TITLE' => 'Șabloane de ghid inteligent',
    'LBL_MODULE_NAME' => 'Șabloane de ghid inteligent',
    'LBL_NEW_FORM_TITLE' => 'Șabloane noi de ghid inteligent',
    'LBL_REMOVE' => 'Eliminare',
    'LBL_SEARCH_FORM_TITLE' => 'Căutare șabloane de ghid inteligent',
    'LBL_TYPE' => 'Tip',
    'LNK_LIST' => 'Șabloane de ghid inteligent',
    'LNK_NEW_RECORD' => 'Creare șablon de ghid inteligent',
    'LBL_COPIES' => 'Copii',
    'LBL_COPIED_TEMPLATE' => 'Șablon copiat',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Importare șabloane',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Șabloanele au fost importate.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Salvează din nou șabloanele',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Șabloanele au fost salvate din nou.',
    'LNK_VIEW_RECORDS' => 'Vizualizare șabloane de ghid inteligent',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Vizualizare șabloane de ghid inteligent',
    'LBL_AVAILABLE_MODULES' => 'Module disponibile',
    'LBL_CANCEL_ACTION' => 'Anulează acțiunea',
    'LBL_NOT_APPLICABLE_ACTION' => 'Acțiune neaplicabilă',
    'LBL_POINTS' => 'Puncte',
    'LBL_RELATED_ACTIVITIES' => 'Activități asociate',
    'LBL_ACTIVE' => 'Activ',
    'LBL_ASSIGNEE_RULE' => 'Regulă împuternicit',
    'LBL_TARGET_ASSIGNEE' => 'Împuternicit țintă',
    'LBL_STAGE_NUMBERS' => 'Numerotare etape',
    'LBL_EXPORT_BUTTON_LABEL' => 'Export',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Import',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Creați/actualizați automat o înregistrare nouă de șablon de ghid inteligent importand un fisier *.json din sistemul dvs. de fișiere.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Șablonul <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> a fost creat cu succes.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Șablonul <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> a fost actualizat cu succes.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Importul nu a reușit. Există deja un șablon numit "<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>". Modificați numele înregistrării importate și încercați din nou sau utilizați „Copiere” pentru a crea un șablon de ghid inteligent duplicat.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Există deja un șablon cu acest ID. Pentru a actualiza șablonul existent, faceți clic <b>Confirmare</b>. Pentru a ieși fără a efectua modificări la șablonul existent, faceți clic pe <b>Anulare</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Șablonul pe care încercați să îl importați este șters în instanța curentă.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Vă rugăm să selectați un fișier *.json valid.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Validare',
    'LBL_IMPORTING_TEMPLATE' => 'Importare',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Acțiuni de etapă dezactivate',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Acțiuni de activitate dezactivate',
    'LBL_FORMS' => 'Formulare',
    'LBL_ACTIVE_LIMIT' => 'Limită ghid(uri) inteligent(e) activ(e)',
    'LBL_WEB_HOOKS' => 'Web hook-uri',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Activitate de pornire a următorului ghid inteligent',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Porniți linkul pentru etapa de ghid inteligent următor',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Alegeți modulele în care Ghidul inteligent ar trebui să fie accesibil',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'La o etapă puteți adăuga mai multe activități sau le puteți șterge. Dezactivați acțiunile la care nu doriți să aibă acces utilizatorul în acest Ghid inteligent',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'La o activitate puteți adăuga mai multe activități ca subactivități. Dezactivați acțiunile la care nu doriți să aibă acces utilizatorul în acest Ghid inteligent',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Câte dintre aceste Ghiduri inteligente pot fi active într-o înregistrare în același timp',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Dacă este bifată opțiunea Dacă Împuternicitul țintă = Împuternicitul părinte, atunci când utilizatorul „Atribuit lui” se modifică la un părinte, utilizatorii „Atribuit lui” se vor modifica automat și în ghidurile inteligente, etape și activități. Rețineți că setările împuternicitului țintă pe șabloane de activitate au prioritate față de șablonul de ghid inteligent',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Când ar trebui atribuit un utilizator la activități',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'La cine ar trebui să fie atribuite activitățile',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Acest comutator vă permite să afișați sau să ascundeți numerotarea automată a etapelor.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Șablon pentru ghid inteligent/etapă/activitate',
];
