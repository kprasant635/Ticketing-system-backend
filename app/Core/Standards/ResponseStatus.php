<?php

namespace App\Core\Standards;

/**
 * Class ResponseStatus
 * 
 * Defines the machine-readable 'code' field for all ELRS API responses.
 * These codes allow frontends to implement programmatic branching logic
 * without relying on brittle string comparisons of messages.
 */
class ResponseStatus
{
    // Success States
    public const OK = 'OK';
    public const CREATED = 'CREATED';
    public const ACCEPTED = 'ACCEPTED';
    public const DELETED = 'DELETED';
    
    // Multi-Item States
    public const PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
    public const BATCH_COMPLETE = 'BATCH_COMPLETE';

    // Generic Error States (When not using specific RFC 9457 titles)
    public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    public const UNAUTHORIZED = 'UNAUTHORIZED';
    public const SYSTEM_ERROR = 'SYSTEM_ERROR';
}
