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

namespace Sugarcrm\Sugarcrm\Util\Streams;

use php_user_filter;

class EncodeFilter extends php_user_filter
{
    /**
     * @var \stdClass|mixed
     */
    public $bucket;
    private $data;
    private static $token = '~!#@&';

    public function onCreate(): bool
    {
        $this->data = '';
        return true;
    }

    public function filter($in, $out, &$consumed, $closing): int
    {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $this->data .= $bucket->data;
            $this->bucket = $bucket;
            $consumed += $bucket->datalen;
        }

        if ($closing) {
            if (!isset($this->bucket)) {
                $this->bucket = new \stdClass();
            }

            $str = str_replace('<?', self::$token, $this->data);

            $this->bucket->data = $str;
            $this->bucket->datalen = strlen($this->data);

            if (!empty($this->bucket->data)) {
                stream_bucket_append($out, $this->bucket);
            }

            $this->data = '';

            return PSFS_PASS_ON;
        }

        return PSFS_FEED_ME;
    }
}
