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

declare(strict_types=1);

namespace Sugarcrm\Sugarcrm\Security\HttpClient;

class Method
{
    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/GET
     */
    public const GET = 'GET';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/HEAD
     */
    public const HEAD = 'HEAD';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/POST
     */
    public const POST = 'POST';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/PUT
     */
    public const PUT = 'PUT';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/DELETE
     */
    public const DELETE = 'DELETE';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/CONNECT
     */
    public const CONNECT = 'CONNECT';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/OPTIONS
     */
    public const OPTIONS = 'OPTIONS';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/TRACE
     */
    public const TRACE = 'TRACE';

    /**
     * @var string
     * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods/PATCH
     */
    public const PATCH = 'PATCH';
}
