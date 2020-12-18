<?php

/*
 * An exception to throw if a database query failed.
 */

class ExceptionQueryFailed extends Exception
{
    private string $m_query;
    private string $m_errorMessage;


    public function __construct(string $query, string $errorMessage)
    {
        parent::__construct("Database query failed");
    }


    public function getQuery() : string { return $this->m_query; }
    public function getErrorMessage() : string { return $this->m_errorMessage; }
}