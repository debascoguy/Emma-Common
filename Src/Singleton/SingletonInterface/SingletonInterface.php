<?php

namespace Emma\Common\Singleton\SingletonInterface;

interface SingletonInterface
{
    /**
     * @return object
     */
    public static function getInstance();

}