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
    // Dashboard Names
    'LBL_OPPORTUNITIES_LIST_DASHBOARD' => 'לוח מחוונים של רשימת הזדמנויות',
    'LBL_OPPORTUNITIES_RECORD_DASHBOARD' => 'לוח מחוונים של רשומת הזדמנויות',
    'LBL_OPPORTUNITIES_MULTI_LINE_DASHBOARD' => 'לוח מחוונים של פוקוס הזדמנויות - מסוף',
    'LBL_OPPORTUNITIES_FOCUS_DRAWER_DASHBOARD' => 'מגירת פוקוס הזדמנויות',
    'LBL_RENEWAL_OPPORTUNITY' => 'הזדמנות לחידוש',

    'LBL_MODULE_NAME' => 'הזדמנויות',
    'LBL_MODULE_NAME_SINGULAR' => 'הזדמנות',
    'LBL_MODULE_TITLE' => 'הזדמנויות: דף הבית',
    'LBL_SEARCH_FORM_TITLE' => 'חיפוש הזדמנויות',
    'LBL_VIEW_FORM_TITLE' => 'צפייה בהזדמנות',
    'LBL_LIST_FORM_TITLE' => 'רשימת הזדמנויות',
    'LBL_OPPORTUNITY_NAME' => 'שם ההזדמנות:',
    'LBL_OPPORTUNITY' => 'הזדמנות:',
    'LBL_NAME' => 'שם ההזדמנות',
    'LBL_TIME' => 'זמן',
    'LBL_INVITEE' => 'אנשי קשר',
    'LBL_CURRENCIES' => 'מטבעות',
    'LBL_LIST_OPPORTUNITY_NAME' => 'שם',
    'LBL_LIST_ACCOUNT_NAME' => 'שם חשבון',
    'LBL_LIST_DATE_CLOSED' => 'נסגר',
    'LBL_LIST_AMOUNT' => 'סכום ההזדמנות',
    'LBL_LIST_AMOUNT_USDOLLAR' => 'סכום',
    'LBL_ACCOUNT_ID' => 'חשבון זהות',
    'LBL_CURRENCY_RATE' => 'שער מטבע',
    'LBL_CURRENCY_ID' => 'מטבע זהות',
    'LBL_CURRENCY_NAME' => 'שם מטבע',
    'LBL_CURRENCY_SYMBOL' => 'סימול מטבע',
//DON'T CONVERT THESE THEY ARE MAPPINGS
    'db_sales_stage' => 'LBL_LIST_SALES_STAGE',
    'db_name' => 'LBL_NAME',
    'db_amount' => 'LBL_LIST_AMOUNT',
    'db_date_closed' => 'LBL_LIST_DATE_CLOSED',
//END DON'T CONVERT
    'UPDATE' => 'Opportunity - עדכון מטבע',
    'UPDATE_DOLLARAMOUNTS' => 'עדכן שער חליפין לדולר',
    'UPDATE_VERIFY' => 'וודא שער חליפין',
    'UPDATE_VERIFY_TXT' => 'Verifies that the amount values in opportunities are valid decimal numbers with only numeric characters(0-9) and decimals(.)',
    'UPDATE_FIX' => 'שער חליפין קבוע',
    'UPDATE_FIX_TXT' => 'ניסיונות לתקן כל סכומים לא חוקיים ביצירת ערך עשרוני חוקי מהסכום הנוכחי. כל סכום שנערך מגובה בשדה מסד הנתונים amount_backup. במידה ואתה מפעיל את זה ונתקל בבעיות, אל תפעיל בשנית מבלי לשחזר את הגיבוי, מכיוון שהוא עלול לכתוב נתונים לא חוקיים חדשים על הגיבוי.',
    'UPDATE_DOLLARAMOUNTS_TXT' => 'Update the U.S. Dollar amounts for opportunities based on the current set currency rates. This value is used to calculate Graphs and List View Currency Amounts.',
    'UPDATE_CREATE_CURRENCY' => 'יוצר מטבע חדש:',
    'UPDATE_VERIFY_FAIL' => 'רשומה נכלשה בעדכון:',
    'UPDATE_VERIFY_CURAMOUNT' => 'שער חליפין:',
    'UPDATE_VERIFY_FIX' => 'הרצת תיקון תאפשר',
    'UPDATE_INCLUDE_CLOSE' => 'לכלול רשומות שנסגרו',
    'UPDATE_VERIFY_NEWAMOUNT' => 'שער חליפין חדש:',
    'UPDATE_VERIFY_NEWCURRENCY' => 'מטבע חדש:',
    'UPDATE_DONE' => 'בוצע',
    'UPDATE_BUG_COUNT' => 'נמצא באג ונסיתי לפתור אותו:',
    'UPDATE_BUGFOUND_COUNT' => 'באגים נמצאו:',
    'UPDATE_COUNT' => 'רשומות שעודכנו:',
    'UPDATE_RESTORE_COUNT' => 'רשומות שערי חליפין ששוחזרו:',
    'UPDATE_RESTORE' => 'שחזר שער חליפין',
    'UPDATE_RESTORE_TXT' => 'שחזר שערי חליפין מגבוי שבוצע תוך כדי תיקון.',
    'UPDATE_FAIL' => 'לא הצלחתי לעדכן -',
    'UPDATE_NULL_VALUE' => 'הכמות היא NULL ותוגדר כ-0',
    'UPDATE_MERGE' => 'מזג מטבעות',
    'UPDATE_MERGE_TXT' => 'Merge multiple currencies into a single currency. If there are multiple currency records for the same currency, you merge them together. This will also merge the currencies for all other modules.',
    'LBL_ACCOUNT_NAME' => 'שם חשבון:',
    'LBL_CURRENCY' => 'מטבע:',
    'LBL_DATE_CLOSED' => 'תאריך סגירה צפוי:',
    'LBL_DATE_CLOSED_TIMESTAMP' => 'תאריך סיום מצופה',
    'LBL_TYPE' => 'סוג:',
    'LBL_CAMPAIGN' => 'קמפיין:',
    'LBL_NEXT_STEP' => 'השלב הבא:',
    'LBL_SERVICE_START_DATE' => 'תאריך תחילת השירות',
    'LBL_LEAD_SOURCE' => 'מקור הליד:',
    'LBL_SALES_STAGE' => 'שלב במכירות:',
    'LBL_SALES_STATUS' => 'מצב',
    'LBL_PROBABILITY' => 'הסתברות (%):',
    'LBL_DESCRIPTION' => 'תיאור',
    'LBL_DUPLICATE' => 'ככל הנראה הזדמנות כפולה',
    'MSG_DUPLICATE' => 'The opportunity record you are about to create might be a duplicate of a opportunity record that already exists. Opportunity records containing similar names are listed below.<br>Click Save to continue creating this new opportunity, or click Cancel to return to the module without creating the opportunity.',
    'LBL_NEW_FORM_TITLE' => 'צור הזדמנות',
    'LNK_NEW_OPPORTUNITY' => 'צור הזדמנות',
    'LNK_CREATE' => 'צור עסקה',
    'LNK_OPPORTUNITY_LIST' => 'צפה בהזדמנויות',
    'ERR_DELETE_RECORD' => 'יש לציין מספר רשומה על מנת למחוק את ההזדמנות.',
    'LBL_TOP_OPPORTUNITIES' => 'ההזדמנויות הבשלות שלי',
    'NTC_REMOVE_OPP_CONFIRMATION' => 'האם אתה בטוח שברצונך להסיר איש קשר זה מההזדמנות?',
    'OPPORTUNITY_REMOVE_PROJECT_CONFIRM' => 'האם אתה בטוח שברצונך להסיר הזדמנות זו מהפרוייקט?',
    'LBL_DEFAULT_SUBPANEL_TITLE' => 'הזדמנויות',
    'LBL_ACTIVITIES_SUBPANEL_TITLE' => 'פעיליות',
    'LBL_HISTORY_SUBPANEL_TITLE' => 'הסטוריה',
    'LBL_RAW_AMOUNT' => 'סכום גלמי',
    'LBL_LEADS_SUBPANEL_TITLE' => 'לידים',
    'LBL_CONTACTS_SUBPANEL_TITLE' => 'אנשי קשר',
    'LBL_DOCUMENTS_SUBPANEL_TITLE' => 'מסמכים',
    'LBL_PROJECTS_SUBPANEL_TITLE' => 'פרויקטים',
    'LBL_ASSIGNED_TO_NAME' => 'הוקצה עבור:',
    'LBL_LIST_ASSIGNED_TO_NAME' => 'משתמש שהוקצה',
    'LBL_LIST_SALES_STAGE' => 'שלב במכירות',
    'LBL_MY_CLOSED_OPPORTUNITIES' => 'ההזדמנויות הסגורות שלי',
    'LBL_TOTAL_OPPORTUNITIES' => 'סך-בכל הזדמנויות',
    'LBL_CLOSED_WON_OPPORTUNITIES' => 'הזדמנויות שנסגרו בהרכשה',
    'LBL_ASSIGNED_TO_ID' => 'משתמש שהוקצה:',
    'LBL_CREATED_ID' => 'נוצר על ידי זהות',
    'LBL_MODIFIED_ID' => 'שונה על ידי זהות',
    'LBL_MODIFIED_NAME' => 'שונה על ידי שם משתמש',
    'LBL_CREATED_USER' => 'משתמש שנוצר',
    'LBL_MODIFIED_USER' => 'משתמש ששונה',
    'LBL_CAMPAIGN_OPPORTUNITY' => 'קמפיינים',
    'LBL_PROJECT_SUBPANEL_TITLE' => 'פרוייקטים',
    'LABEL_PANEL_ASSIGNMENT' => 'מטלה',
    'LNK_IMPORT_OPPORTUNITIES' => 'ייבוא הזדמנויות',
    'LBL_EDITLAYOUT' => 'ערוך תצורה' /*for 508 compliance fix*/,
    //For export labels
    'LBL_EXPORT_CAMPAIGN_ID' => 'קמפיין ID',
    'LBL_OPPORTUNITY_TYPE' => 'סוג הזדמנות',
    'LBL_EXPORT_ASSIGNED_USER_NAME' => 'הוקצה למשתמש ששמו',
    'LBL_EXPORT_ASSIGNED_USER_ID' => 'הוקצה למשתמש ID',
    'LBL_EXPORT_MODIFIED_USER_ID' => 'נערך על ידי מזהה',
    'LBL_EXPORT_CREATED_BY' => 'נוצר על ידי ID',
    'LBL_EXPORT_NAME' => 'שם',
    // SNIP
    'LBL_CONTACT_HISTORY_SUBPANEL_TITLE' => 'קשור לדואר אלקטרוני של איש קשר',
    'LBL_FILENAME' => 'קובץ מצורף',
    'LBL_PRIMARY_QUOTE_ID' => 'תהצעת מחיר ראשי',
    'LBL_CONTRACTS' => 'אנשי קשר',
    'LBL_CONTRACTS_SUBPANEL_TITLE' => 'חוזים',
    'LBL_PRODUCTS' => 'שורות פריטים מצוטטים',
    'LBL_RLI' => 'שורות פרטי רווח',
    'LNK_OPPORTUNITY_REPORTS' => 'צפייה בדוחות על הזדמנויות',
    'LBL_QUOTES_SUBPANEL_TITLE' => 'הצעות מחיר',
    'LBL_TEAM_ID' => 'צוות זהות',
    'LBL_TIMEPERIODS' => 'תקופות זמן',
    'LBL_TIMEPERIOD_ID' => 'תקופת זמן ID',
    'LBL_COMMITTED' => 'מחוייב',
    'LBL_FORECAST' => 'כלול בתחזית',
    'LBL_COMMIT_STAGE' => 'שלב חיוב',
    'LBL_COMMIT_STAGE_FORECAST' => 'שלב תחזית',
    'LBL_WORKSHEET' => 'גיליון עבודה',
    'LBL_PURCHASED_LINE_ITEMS' => 'פריטי שורה - רכישות',

    // KPI Metrics
    'LBL_ORGANIZE' => 'ארגון',
    'LBL_CREATE_NEW' => 'צור חדש',
    'LBL_MANAGE' => 'ניהול',
    'LBL_SEE_DETAILS' => 'צפה בפרטים',
    'LBL_HIDE_NEW' => 'הסתר',

    'LBL_FORECASTED_LIKELY' => 'נחזה כסביר',
    'LBL_LOST' => 'הפסד',
    'LBL_RENEWAL' => 'חידוש',
    'LBL_RENEWAL_OPPORTUNITIES' => 'הזדמנויות חידוש',
    'LBL_RENEWAL_PARENT' => 'הזדמנות אב',
    'LBL_PARENT_RENEWAL_OPPORTUNITY_ID' => 'מזהה אב חידוש',
    'LBL_MONTH_YEAR_RENEWAL' => '{{month}}, {{year}}',

    'LBL_WIDGET_SALES_STAGE' => 'שלב במכירות',
    'LBL_WIDGET_DATE_CLOSED' => 'תאריך סגירה צפוי',
    'LBL_WIDGET_AMOUNT' => 'סכום',

    'TPL_RLI_CREATE' => 'הזדמנות חייבת לכלול פריט שורת הכנסה מקושר',
    'TPL_RLI_CREATE_LINK_TEXT' => 'צור פריט שורת הכנסה',
    'LBL_PRODUCTS_SUBPANEL_TITLE' => 'שורות הצעות מחיר',
    'LBL_RLI_SUBPANEL_TITLE' => 'שורות פרטי הכנסה',

    'LBL_TOTAL_RLIS' => 'סך הכל פרטי שורות הכנסה',
    'LBL_CLOSED_RLIS' => 'מספר שורות פרטי הכנסה סגורות',
    'LBL_CLOSED_WON_RLIS' => 'מספר פריטי הכנסה שנסגרו בהצלחה',
    'LBL_SERVICE_OPEN_FLEX_DURATION_RLIS' => 'מס&#39; פריטי שורה של הכנסה עם משך גמיש משירות פתוח',
    'NOTICE_NO_DELETE_CLOSED_RLIS' => 'לא ניתן למחוק הזדמנויות המכילות פרטי שורות רווח',
    'WARNING_NO_DELETE_CLOSED_SELECTED' => 'רשומה אחת או יותר שנבחרו מכילות פרטי שורות רווח סגורות ואינן ניתנות למחיקה',
    'LBL_INCLUDED_RLIS' => '# פריטי שורת רווח כלולים',
    'LBL_UPDATE_OPPORTUNITIES_RLIS' => 'עדכון פתוח',
    'LBL_CASCADE_RLI_EDIT' => 'עדכן פריטי שורה פתוחים של הכנסות',
    'LBL_CASCADE_RLI_CREATE' => 'הגדר בין פריטי שורה של הכנסה',
    'LBL_SERVICE_START_DATE_INVALID' => 'תאריך ההתחלה של השירות לא יכול להיות מאוחר יותר מתאריך הסיום של השירות עבור אף פריט שורה פתוח של הכנסה.',

    'LBL_QUOTE_SUBPANEL_TITLE' => 'הצעות מחיר',
    'LBL_FILTER_OPPORTUNITY_TEMPLATE' => 'הזדמנויות לפי חשבון דינמי',
    'LBL_TOP_10_OPP' => '10 ההזדמנויות הפתוחות המובילות',
    'LBL_DASHLET_MY_ACTIVE_OPP' => 'דשלט: ההזדמנויות הפעילות שלי',
    'LBL_MY_ACTIVE_OPP' => 'ההזדמנויות הפעילות שלי',


    // Config
    'LBL_OPPS_CONFIG_VIEW_BY_LABEL' => 'היררכיית הזדמנות',
    'LBL_OPPS_CONFIG_VIEW_BY_DATE_ROLLUP' => 'קבע את שדה תאריך סגירה מצופה של רשומות ההזדמנות כתאריכי סגירה מוקדמים או מאוחרים ביותר של שורות פרטי הרווח הקיימים',

    //Dashlet
    'LBL_PIPELINE_TOTAL_IS' => 'סך הכל לצינור מכירות',

    'LBL_OPPORTUNITY_ROLE' => 'תפקיד הזדמנות',
    'LBL_NOTES_SUBPANEL_TITLE' => 'הערות',
    'LBL_TAB_OPPORTUNITY' => 'בדיקת המודול {{module}}',

    // Help Text
    'LBL_OPPS_CONFIG_ALERT' => 'בלחיצה על אישור, ימחקו כל נתוני התחזית וכל תצוגות ההזדמנויות ימחקו.',
    'LBL_OPPS_CONFIG_ALERT_TO_OPPS' =>
        'לחיצה על &#39;אשר&#39; תגרום למחיקת נתוני &#39;כל התחזיות&#39; ולשינוי תצוגת ההזדמנויות שלך. '
        . 'גם האפשרות &#39;כל הגדרות התהליכים&#39; עם מודול המטרה &#39;שורות פריטי הכנסה&#39; תושבת. '
        . 'אם לא לכך התכוונת, לחץ על &#39;ביטול&#39; כדי לחזור להגדרות הקודמות.',
    'LBL_OPPS_CONFIG_SALES_STAGE_1a' => ', אם כל שורות פרטי הרווח סגורים ולפחות אחד זכה',
    'LBL_OPPS_CONFIG_SALES_STAGE_1b' => 'שלב ההזדמנות הוא "נסגר בנצחון"',
    'LBL_OPPS_CONFIG_SALES_STAGE_2a' => ', אם כל שורות פרטי הרווח בשלב מכירה סגור בהפסד',
    'LBL_OPPS_CONFIG_SALES_STAGE_2b' => 'שלב המכירה של ההזדמנות הוגדר כסגור ואבוד',
    'LBL_OPPS_CONFIG_SALES_STAGE_3a' => ', אם יד שורות פרטי רווח פתוחות',
    'LBL_OPPS_CONFIG_SALES_STAGE_3b' => 'ההזדמנות תסומן כשלב המכירה המתקדם מעט ביותר',

// BEGIN ENT/ULT

    // Opps Config - View By Opportunities
    'LBL_HELP_CONFIG_OPPS' => 'לאחר החלת שינוי זה, הערות הסיכום של פריט שורת ההכנסות יווצרו ברקע. ברגע שהערות יסתיימו להיווצר ויהיו זמינות, תישלח אליך הודעה לכתובת הדוא"ל המופיעה בפרופיל המשתמש שלך. אם המופע (Instance) שלך מוגדר עבור {{forecasts_module}}, Sugar ישלח לך הודעה גם כאשר רישומי ה-{{module_name}} שלך סונכרנו עם מודל ה-{{forecasts_module}} וזמינים ל-{{forecasts_module}} חדש. שים לב שהמופע שלך חייב להיות מוגד לשליחת הודעות דוא"ל בהגדרות Admin>Email על מנת שההתראות יישלחו.',

    // Opps Config - View By Opportunities And RLIs
    'LBL_HELP_CONFIG_RLIS' => 'לאחר החלת שינוי זה, הערות הסיכום של פריטי שורות ההכנסות יווצרו ברקע עבור כל {{module_name}} קיים. ברגע שפריטי שורות ההכנסות יסתיימו להיווצר ויהיו זמינות, תישלח אליך הודעה לכתובת הדוא"ל המופיעה בפרופיל המשתמש שלך. שים לב שהמופע שלך חייב להיות מוגד לשליחת הודעות דוא"ל בהגדרות Admin>Email על מנת שההתראות יישלחו.',
    // List View Help Text
    'LBL_HELP_RECORDS' => 'המודול {{plural_module_name}} מאפשר לעקוב אחר מכירות פרטניות, מתחילתן עד סופן. כל רשומה של {{module_name}} מייצגת הזדמנות מכירה, וכוללת נתונים רלוונטיים לגבי המכירה ולגבי רשומות חשובות נוספות כגון {{quotes_module}}, {{contacts_module}} ועוד. בדרך כלל, התהליך של {{module_name}} יעבור כמה שלבי מכירה, עד שיסומן בתור "זכייה סגורה" (Closed Won) או "הפסד סגור" (Closed Lost). אפשר למנף {{plural_module_name}} עוד יותר באמצעות מודול {{forecasts_singular_module}} של Sugar, על מנת להבין ולחזות מגמות מכירה וכדי למקד את העבודה בהגשמה של מיכסות מכירה.',

    // Record View Help Text
    'LBL_HELP_RECORD' => 'המודול {{plural_module_name}} מאפשר לעקוב אחר מכירות פרטניות ושורות השייכות לאותן מכירות, מתחילתן עד סופן. כל רשומה של {{module_name}} מייצגת הזדמנות מכירה, וכוללת נתונים רלוונטיים לגבי המכירה ולגבי רשומות חשובות אחרות, כגון {{quotes_module}},‏ {{contacts_module}} ועוד.

- כדי לערוך את שדות הרשומה, יש ללחוץ על שדה מסוים או על לחצן ה&#39;עריכה&#39;.
- אפשר לצפות בקישורים לרשומות נוספות או לשנות אותם בפאנלי המשנה, על-ידי שינוי מצב החלונית בפינה השמאלית התחתונה השמאלית ל"תצוגת נתונים".
- אפשר ליצור ולהציג תגובות משתמשים ולתעד את היסטוריית השינויים של אותו מודול {{activitystream_singular_module}} על-ידי שינוי מצב החלונית בפינה השמאלית התחתונה ל"זרימת פעילות".
- אפשר לעקוב אחר רשומה זו או לסמן אותה כמועדפות באמצעות הסמלים לצד שמה.
- פעולות נוספות זמינות בתפריט הנפתח &#39;פעולות&#39; לצד הלחצן &#39;עריכה&#39;.',

    // Create View Help Text
    'LBL_HELP_CREATE' => 'המודול {{plural_module_name}} מאפשר לעקוב אחר מכירות פרטניות ושורות השייכות לאותן מכירות מתחילתן עד סופן. כל רשומה של {{module_name}} מייצגת הזדמנות מכירה, וכוללת נתונים רלוונטיים לגבי המכירה ולגבי רשומות חשובות אחרות, כגון {{quotes_module}}‏, {{contacts_module}} ועוד.

כדי ליצור {{module_name}}:
1. ספק ערכים עבור השדות כרצונך.
 - יש למלא את השדות המסומנים כשדות חובה לפני השמירה.
 - לחץ על &#39;הצג עוד&#39; כדי להציג שדות נוספים, במידת הצורך.
2. לחץ על &#39;שמור&#39; כדי לסיים את הרשומה החדשה ולחזור לדף הקודם.',

// END ENT/ULT

    //Marketo
    'LBL_MKTO_SYNC' => 'סנכרן עם Marketo®',
    'LBL_MKTO_ID' => 'Marketo® ליד ID',

    'LBL_DASHLET_TOP10_SALES_OPPORTUNITIES_NAME' => '10 שורות פרטי רווח הטובים ביותר',
    'LBL_TOP10_OPPORTUNITIES_CHART_DESC' => 'מציג 10 שורות פרטי רווח הטובים ביותר בתרשים בועות',
    'LBL_TOP10_OPPORTUNITIES_MY_OPP' => 'ההזדמנויות שלי',
    'LBL_TOP10_OPPORTUNITIES_MY_TEAMS_OPP' => "ההזדמנויות של הצוות שלי",

    'LBL_PIPELINE_ERR_CLOSED_SALES_STAGE' => 'לא ניתן לשנות את {{fieldName}} מאחר שאין פריטי שורה פתוחים ב-{{moduleSingular}} זה.',
    'TPL_ACTIVITY_TIMELINE_DASHLET' => 'ציר הזמן של ההזדמנות',

    'LBL_CASCADE_SERVICE_WARNING' => ' לא ניתן להגדרה בין אף אחד מפריטי השורה האלה של הכנסה מאחר שהם אינם שירותים. האם ברצונך להמשיך עם פעולת היצירה?',
    'LBL_CASCADE_DURATION_WARNING' => ' לא ניתן להגדרה בין אף אחד מפריטי השורה האלה של הכנסה מאחר שמשכי הזמן שלהם נעולים. האם ברצונך להמשיך עם פעולת היצירה?',

    // AI Predict
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_NAME' => 'תחזית לסגירת הזדמנות',
    'LBL_AI_OPPORTUNITY_CLOSE_PREDICTION_DESC' => 'הצג פרטי תחזית לגבי הזדמנות ספציפית',
    'LBL_AI_WINRATE' => 'שיעור זכיות',
    'LBL_AI_WONOPP' => 'זכייה בהזדמנויות',
    'LBL_AI_CLOSINGTIME' => 'זמן סגירה',
    'LBL_AI_CLOSEDOPP' => 'הזדמנויות שנסגרו',
    'LBL_AI_LEADTIMESPAN' => 'הזמן בין יצירת הזדמנות לזכייה סגורה',
];
