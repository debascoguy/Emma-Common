<?php

namespace Emma\Common\Factory;

abstract class AbstractFactory implements FactoryInterface
{
    /**
     * @param array|string $param
     * @return mixed
     */
    abstract public function make(array|string $param): mixed;

}