<?php

namespace Ringli\Workflow;

/**
 * Responsible for functionality related to a workflow status.
 */
class Status
{
    /**
     * Informational scenarios.
     */
    public const RUNNING = 'running';

    /**
     * Successful scenarios.
     */
    public const SUCCESS = 'success';

    /**
     * Failure scenarios.
     */
    public const FAILING = 'failing';
    public const FAILED = 'failed';
}
