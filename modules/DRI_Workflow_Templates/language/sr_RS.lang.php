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
    'LBL_HOMEPAGE_TITLE' => 'Moji Smart Guide šabloni',
    'LBL_LIST_FORM_TITLE' => 'Lista Smart Guide šablona',
    'LNK_IMPORT_CUSTOMER_JOURNEY_TEMPLATES' => 'Uvezi Smart Guide šablone',
    'LBL_MODULE_TITLE' => 'Smart Guide šabloni',
    'LBL_MODULE_NAME' => 'Smart Guide šabloni',
    'LBL_NEW_FORM_TITLE' => 'Novi Smart Guide šablon',
    'LBL_REMOVE' => 'Ukloni',
    'LBL_SEARCH_FORM_TITLE' => 'Pretraži Smart Guide šablone',
    'LBL_TYPE' => 'Tip',
    'LNK_LIST' => 'Smart Guide šabloni',
    'LNK_NEW_RECORD' => 'Kreiraj Smart Guide šablon',
    'LBL_COPIES' => 'Kopije',
    'LBL_COPIED_TEMPLATE' => 'Kopirani šablon',
    'LBL_IMPORT_TEMPLATES_BUTTON_LABEL' => 'Uvezi šablone',
    'LBL_IMPORT_TEMPLATES_SUCCESS_MESSAGE' => 'Šabloni su uvezeni.',
    'LBL_RESAVE_TEMPLATES_BUTTON_LABEL' => 'Ponovno sačuvaj šablone',
    'LBL_RESAVE_TEMPLATES_SUCCESS_MESSAGE' => 'Šabloni su ponovo sačuvani.',
    'LNK_VIEW_RECORDS' => 'Prikaži Smart Guide šablone',
    'LNK_DRI_WORKFLOW_TEMPLATE_LIST' => 'Prikaži Smart Guide šablone',
    'LBL_AVAILABLE_MODULES' => 'Dostupni moduli',
    'LBL_CANCEL_ACTION' => 'Otkaži radnju',
    'LBL_NOT_APPLICABLE_ACTION' => 'Nema primenjive radnje',
    'LBL_POINTS' => 'Bodovi',
    'LBL_RELATED_ACTIVITIES' => 'Povezane aktivnosti',
    'LBL_ACTIVE' => 'Aktivan',
    'LBL_ASSIGNEE_RULE' => 'Pravilo dodeljenog korisnika',
    'LBL_TARGET_ASSIGNEE' => 'Ciljni dodeljeni korisnik',
    'LBL_STAGE_NUMBERS' => 'Numerisanje faze',
    'LBL_EXPORT_BUTTON_LABEL' => 'Izvoz',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_BUTTON_LABEL' => 'Uvoz',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEXT' => 'Automatski kreiraj/ažuriraj novi zapis Smart Guide šablona pomoću uvoza *.json dokumenta iz sopstvenog sistema fajlova.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_CREATE_SUCCESS' => 'Šablon <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> je uspešno kreiran.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_SUCCESS' => 'Šablon <a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a> je uspešno ažuriran.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_DUPLICATE_NAME_ERROR' => 'Uvoz nije uspeo. Šablon sa nazivom „<a href="#{{buildRoute model=this module="DRI_Workflow_Templates"}}">{{name}}</a>” već postoji. Promenite naziv uvezenog zapisa i pokušajte ponovo ili upotrebite opciju „Kopiraj” da biste kreirali duplirani Smart Guide šablon.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_UPDATE_CONFIRM' => 'Šablon sa ovim ID-om već postoji. Da biste ažurirali postojeći šablon, kliknite na dugme <b>Potvrdi</b>. Da biste izašli bez promena u postojećem šablonu, kliknite na dugme <b>Otkaži</b>.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_IMPORT_TEMPLATE_DELETED' => 'Šablon koji pokušavate da uvezete se briše u trenutnoj instanci.',
    'LBL_CUSTOMER_JOURNEY_TEMPLATE_EMPTY_WARNING' => 'Izaberite važeću *.json datoteku.',
    'LBL_CHECKING_IMPORT_UPLOAD' => 'Validacija',
    'LBL_IMPORTING_TEMPLATE' => 'Uvoz',
    'LBL_DISABLED_STAGE_ACTIONS' => 'Onemogućene radnje faze',
    'LBL_DISABLED_ACTIVITY_ACTIONS' => 'Onemogućene radnje aktivnosti',
    'LBL_FORMS' => 'Obrasci',
    'LBL_ACTIVE_LIMIT' => 'Ograničenje aktivnih Smart Guide-ova',
    'LBL_WEB_HOOKS' => 'Webhook-ovi',
    'LBL_START_NEXT_JOURNEY_ACTIVITIES' => 'Sledeća Smart Guide početna aktivnost',
    'LBL_START_NEXT_JOURNEY_STAGES' => 'Pokreni sledeći Smart Guide link faze',
    'LBL_SMART_GUIDE_ACCESSIBLE' => 'Odaberite module u kojima bi Smart Guide trebalo da bude dostupan',
    'LBL_SMART_GUIDE_MODIFY_ACTIONS' => 'U fazi možete da dodate još aktivnosti ili da izbrišete. Onemogućite one radnje kojima ne želite da korisnik ima pristup u ovom Smart Guide-u',
    'LBL_SMART_GUIDE_DISABLE_ACTIONS' => 'Možete da dodate više aktivnosti kao podaktivnosti u Aktivnost. Onemogućite one radnje kojima ne želite da korisnik ima pristup u ovom Smart Guide-u',
    'LBL_SMART_GUIDE_ACTIVATES' => 'Koliko Smart Guide-ova može da bude aktivno na zapisu istovremeno',
    'LBL_SMART_GUIDE_TARGET_ASSIGNEE' => 'Ako je polje potvrđeno i ako je Ciljni dodeljeni korisnik = Nadređeni dodeljeni korisnik kada je korisnik „Dodeljeno” promenjen na nadređenom korisniku, korisnici sa oznakom „Dodeljeno” će se takođe automatski promeniti u Smart Guide-ovima, fazama i aktivnostima. Imajte na umu da podešavanja za Ciljnjeg dodeljenog korisnika u Šablonima aktivnosti imaju prednost nad Smart Guide šablonom',
    'LBL_SMART_GUIDE_USER_ASSIGNED' => 'Kada korisniku treba dodeliti aktivnosti',
    'LBL_SMART_GUIDE_ACTIVITIES_ASSIGNED' => 'Kome treba dodeliti aktivnosti',
    'LBL_SMART_GUIDE_STAGE_NUMBERS' => 'Ovaj preklopnik vam omogućava da prikažete ili sakrijete automatsko numerisanje faza.',
    'CJ_FORMS_LBL_PARENT_NAME' => 'Smart Guide/Faza/Šablon aktivnosti',
];
