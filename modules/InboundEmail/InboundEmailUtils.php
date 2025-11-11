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

/**
 * InboundEmailUtils provides utility functions to assist with handling
 * InboundEmail processes
 */
class InboundEmailUtils
{
    /**
     * Decodes a string of text with the specified encoding
     *
     * @param string $text the text to decode
     * @param string $encoding the encoding scheme
     * @return string the decoded text
     */
    public static function handleTransferEncoding($text, $encoding)
    {
        switch (strtolower((string)$encoding)) {
            case 'base64':
                return base64_decode($text);
            case 'quoted-printable':
                return quoted_printable_decode($text);
            default:
                return $text;
        }
    }

    /**
     * Handles translating text from original encoding into UTF-8
     *
     * @param string text test to be re-encoded
     * @param string charset original character set
     * @return string utf8 re-encoded text
     */
    public static function handleCharsetTranslation($text, $charset)
    {
        global $locale;

        // If no charset was provided, attempt to detect it
        $charset = !empty($charset) ? $charset : $locale->detectCharset($text, true);
        if (empty($charset)) {
            $GLOBALS['log']->debug('InboundEmailUtils::handleCharsetTranslation() called without $charset');
            return $text;
        }

        // typical headers have no charset - let destination pick (since it's all ASCII anyways)
        if (strtolower($charset) == 'default' || strtolower($charset) == 'utf-8') {
            return $text;
        }

        return $locale->translateCharset($text, $charset);
    }

    /**
     * Correctly formats CID-embedded inline image references in an email HTML
     * body for storage in Sugar
     *
     * @param string $html the email HTML body to update
     * @param array $inlineImages mapping of {Old cid => New cid} for all inline CID images
     * @return string the HTML body with updated image references
     */
    public static function updateInlineImageHtml($html, $inlineImages)
    {
        // For each inline image in the email HTML, update all tags that reference
        // the image by CID
        foreach ($inlineImages as $imageId => $imageName) {
            $references = [];
            $pattern = '#<[^>]*src="?cid:' . preg_quote($imageId, '#') . '"?[^>]*>#';

            if (preg_match_all($pattern, $html, $references)) {
                // For each reference, update the class and src attributes to the
                // correct format
                foreach ($references as $oldReference) {
                    $newReference = preg_replace(
                        [
                            '#[ ]*class="[^"]*"[ ]*#',
                            '#src="?cid:' . preg_quote($imageId, '#') . '"?#',
                        ],
                        [
                            ' ',
                            'class="image" src="cid:' . $imageName . '"',
                        ],
                        $oldReference
                    );

                    $html = str_replace($oldReference, $newReference, $html);
                }
            }
        }
        return $html;
    }
}
