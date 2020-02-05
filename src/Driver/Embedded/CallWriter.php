<?php
namespace dokuwiki\plugin\yuriigantt\src\Driver\Embedded;

use dokuwiki\plugin\yuriigantt\src\Driver\Embedded\Handler;

/**
 * We need it to workaround interface limitations
 */
class CallWriter extends \dokuwiki\Parsing\Handler\CallWriter
{
    public function __construct(Handler $handler)
    {
        $this->Handler = $handler;
    }
}
