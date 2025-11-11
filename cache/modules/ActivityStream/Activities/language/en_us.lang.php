<?php
// created: 2025-11-11 16:19:38
$mod_strings = array (
  'TPL_ACTIVITY_CREATE' => 'Created {{{str "TPL_ACTIVITY_RECORD" "Activities" object}}}{{#if object.module}} {{getModuleName object.module}}{{/if}}.',
  'TPL_ACTIVITY_POST' => '{{{value}}}{{{str "TPL_ACTIVITY_ON" "Activities" this}}}',
  'TPL_ACTIVITY_UPDATE' => 'Updated {{#if updateStr}}{{{updateStr}}} on {{/if}}{{#if object.module}}{{{str "TPL_ACTIVITY_RECORD" "Activities" object}}}{{else}}{{object.name}}{{/if}}.',
  'TPL_ACTIVITY_UPDATE_FIELD' => '<a rel="tooltip" title="Changed: {{before}} To: {{after}}">{{field_label}}</a>',
  'TPL_ACTIVITY_LINK' => 'Linked {{{str "TPL_ACTIVITY_RECORD" "Activities" subject}}} to {{{str "TPL_ACTIVITY_RECORD" "Activities" object}}}.',
  'TPL_ACTIVITY_UNLINK' => 'Unlinked {{{str "TPL_ACTIVITY_RECORD" "Activities" subject}}} to {{{str "TPL_ACTIVITY_RECORD" "Activities" object}}}.',
  'TPL_ACTIVITY_ATTACH' => 'Added file <a class="dragoff" target="sugar_attach" href="{{url}}" data-note-id="{{noteId}}">{{filename}}</a>{{{str "TPL_ACTIVITY_ON" "Activities" this}}}',
  'TPL_ACTIVITY_DELETE' => 'Deleted {{{str "TPL_ACTIVITY_RECORD" "Activities" object}}} {{getModuleName object.module}}.',
  'TPL_ACTIVITY_UNDELETE' => 'Restored {{{str "TPL_ACTIVITY_RECORD" "Activities" object}}} {{getModuleName object.module}}.',
  'TPL_ACTIVITY_RECORD' => '{{#if module}}<a href="#{{buildRoute module=module id=id}}">{{name}}</a>{{else}}{{name}}{{/if}}',
  'TPL_ACTIVITY_ON' => '{{#if object}} on {{{str "TPL_ACTIVITY_RECORD" "Activities" object}}}.{{/if}}{{#if module}} on {{getModuleName module}}.{{else}} {{/if}}',
  'TPL_COMMENT' => '{{{value}}}',
  'TPL_MORE_COMMENT' => '{{this}} more comment&hellip;',
  'TPL_MORE_COMMENTS' => '{{this}} more comments&hellip;',
  'TPL_SHOW_MORE_MODULE' => 'More posts...',
);