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

class EmbedLinkService
{
    // regular expression from http://daringfireball.net/2010/07/improved_regex_for_matching_urls
    protected static $urlRegex = '#(?i)\b((?:https?://|www\d{0,3}\.|[a-z0-9.\-]+\.[a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))#i';

    protected static $imageRegex = '#(https?://[^\s]+(?=\.(jpe?g|png|gif)))(\.(jpe?g|png|gif))#i';

    /**
     * Get all embed data of links in a given string
     *
     * @param string $text
     * @return array
     */
    public function get($text)
    {
        $embeds = [];
        $urls = $this->findAllUrls($text);

        foreach ($urls as $url) {
            if ($this->isImage($url)) {
                $embed = [
                    'type' => 'image',
                    'src' => $url,
                ];
                array_push($embeds, $embed);
            }
        }

        return ['embeds' => $embeds];
    }

    /**
     * Find all URLs in a given string
     *
     * @param string $text
     * @return array
     */
    protected function findAllUrls($text)
    {
        preg_match_all(static::$urlRegex, $text, $matches);
        return $matches[0];
    }

    /**
     * Is it a link to an image?
     *
     * @param string $url
     * @return bool
     */
    protected function isImage($url)
    {
        return (preg_match(static::$imageRegex, $url) === 1);
    }
}
