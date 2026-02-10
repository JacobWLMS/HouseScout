<?php

namespace App\Exceptions;

use RuntimeException;

class EpcApiKeyMissingException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('EPC API key is not configured. Register at https://epc.opendatacommunities.org/ and add EPC_API_KEY to your .env file.');
    }
}
