<?php

namespace Emma\Common\Factory;

abstract class ObjectFactory
{
    abstract public function make(object|string $entity): static;
}