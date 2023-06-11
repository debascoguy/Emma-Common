<?php

namespace Emma\Common\Singleton\Interfaces;

interface SingletonInterface
{
    /**
     * @return object
     */
    public static function getInstance(): object;

}