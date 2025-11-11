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

class AttachmentsApiHelper extends SugarBeanApiHelper
{
    /**
     * Ensure attachments are added for beans that use 'attachment_list'
     * to store attachments using multiple nodes.
     *
     * @param SugarBean $bean
     * @param array $fieldList
     * @param array $options
     * @return array
     */
    public function formatForApi(SugarBean $bean, array $fieldList = [], array $options = [])
    {
        $result = parent::formatForApi($bean, $fieldList, $options);

        // If our bean has no ID, we've gotten here via the deleteRelatedRecord
        // API. In that case, attempting to load the related beans will fail
        // because our seed bean has no ID to match. This ends up making a query
        // that matches all notes with empty values in the field that is the
        // foreign key in the relationship, which is a big bottleneck.
        if (empty($bean->id) || !$bean->load_relationship('attachments')) {
            return $result;
        }

        $result['attachment_list'] = [];
        foreach ($bean->attachments->getBeans() as $note) {
            if ($note->id !== $bean->id && $attachment = $note->getAttachment()) {
                array_push($result['attachment_list'], $attachment);
            }
        }

        return $result;
    }

    /**
     * Create attachments for beans that use Notes to store multiple attachments
     * in their attachment_list
     *
     * @param SugarBean $bean
     * @param array $submittedData
     * @param array $options
     * @return array
     */
    public function populateFromApi(SugarBean $bean, array $submittedData, array $options = [])
    {
        $attachment_list = [];
        if (!empty($submittedData['attachment_list'])) {
            $attachment_list = $submittedData['attachment_list'];
            unset($submittedData['attachment_list']);
        }

        $data = parent::populateFromApi($bean, $submittedData, $options);

        if (!empty($attachment_list) && $data) {
            $bean->load_relationship('attachments');
            $attachments = [];
            if ($bean->id) {
                $attachments = $bean->attachments->getBeans();
            } else {
                $bean->id = create_guid();
                $bean->new_with_id = true;
            }
            foreach ($attachment_list as $info) {
                foreach ($attachments as $attachment) {
                    if ($attachment->id === $info['id']) {
                        continue 2;
                    }
                }
                $note = BeanFactory::getBean('Notes', $info['id']);
                if ($note->parent_id && $note->parent_type) {
                    // Note of an original record.
                    $attachment = clone $note;
                    $attachment->new_with_id = true;
                    $attachment->portal_flag = true;
                    $attachment->id = create_guid();
                    UploadFile::duplicate_file($note->id, $attachment->id);
                } else {
                    // A new note created on client.
                    $attachment = $note;
                }
                $bean->attachments->add($attachment);
            }
        }

        return $data;
    }
}
