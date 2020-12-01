<?php

/*
 * An exception to throw if we are missing a required environment variable.
 */

class ExceptionMissingRequiredEnvironmentVariable extends Exception
{
    private string $m_missingEnvironmentVariable;


    public function __construct()
    {
        parent::__construct("Missing required environment variable: " . $this->m_missingEnvironmentVariable);
    }


    public function getEnvironmentVariableName() : string { return $this->m_missingEnvironmentVariable; }
}