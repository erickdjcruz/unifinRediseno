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

interface Inbound
{
    /**
     * Function to verify if a real connection is made
     *
     * @return bool
     */
    public function testSettings();

    /**
     * Returns a list of mailbox data
     *
     * @return array
     */
    public function getMailboxes(): array;

    /**
     * Search a mailbox for messages. Criteria must be passed as an array.
     *
     * @param array $criteria
     * @return array|bool|null
     */
    public function search(array $criteria);

    /**
     * Delete a message on the server
     * @param int $uid
     */
    public function deleteMessage(int $uid);

    /**
     * Gets a message object from Uid. Mailbox needs to be selected beforehand.
     * Caches the message so we don't download it multiple times.
     * @param $uid
     * @return Message
     */
    public function getMessageFromId(int $uid);

    /**
     * Get Subject line from email
     * @param int $uid
     * @return string
     */
    public function getSubject(int $uid): string;

    /**
     * Get From line from email
     * @param int $uid
     * @return string
     */
    public function getFrom(int $uid): string;

    /**
     * Get To line from email
     * @param int $uid
     * @return string
     */
    public function getTo(int $uid): string;

    /**
     * Get CC line from email
     * @param int $uid
     * @return string
     */
    public function getCc(int $uid): string;

    /**
     * Get BCC line from email
     * @param int $uid
     * @return string
     */
    public function getBcc(int $uid): string;

    /**
     * Get Reply-To line from email
     * @param int $uid
     * @return string
     */
    public function getReplyTo(int $uid): string;

    /**
     * Get From email address from email
     * @param int $uid
     * @return array
     */
    public function getFromAddress(int $uid): array;

    /**
     * Get To email addresses from email
     * @param int $uid
     * @return array
     */
    public function getToAddresses(int $uid): array;

    /**
     * Get CC email addresses from email
     * @param int $uid
     * @return array
     */
    public function getCcAddresses(int $uid): array;

    /**
     * Get BCC email addresses from email
     * @param int $uid
     * @return array
     */
    public function getBccAddresses(int $uid): array;

    /**
     * Get Reply-To email addresses from email
     * @param int $uid
     * @return array
     */
    public function getReplyToAddresses(int $uid): array;

    /**
     * Get Body from email
     * @param $uid
     * @return array containing the following:
     *      'plain' => The plaintext (no HTML) version of the email
     *      'html' => The HTML version of the email
     */
    public function getBody($uid): array;

    /**
     * Get Attachment data from email
     * @param $uid
     * @return array containing an entry for each attachment of the email. Each
     * entry contains the following:
     *      'contentType' => The Content-Type header value
     *      'type' => The type part of the Content-Type header value
     *      'subtype' => The subtype part of the Content-Type header value
     *      'contentDisposition' => The Content-Disposition header value
     *      'contentId' => The Content-ID header value
     *      'encoding' => The encoding of the attachment
     *      'charset' => The charset of the attachment
     *      'fileName' => The filename of the attachment
     *      'content' => The raw content of the attachment
     */
    public function getAttachments($uid): array;
}
