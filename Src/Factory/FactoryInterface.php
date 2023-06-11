<?php

namespace Emma\Common\Factory;

interface FactoryInterface
{
    public function make(array|string $param): mixed;
}