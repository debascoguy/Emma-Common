<?php

namespace Emma\Stdlib;

use Emma\App\Model\Entity\EmmaEntity;
use Emma\App\Model\Entity\EmmaEntityDataType;
use Emma\Common\Singleton\Singleton;
use Emma\Common\Utils\StringManagement;

/**
 * @Author: Ademola Aina
 * Date: 9/19/2017
 * Time: 8:31 PM
 */
class Hydrator
{
    use Singleton;

    /**
     * @param $objectSource
     * @param $includeParentProperties
     * @param $fieldValuecallBack
     * @return array
     * Note: any User Defined field-value CallBack will get parameter as follows:
     * extract() : during extraction, a copy of the [VALUE, OBJECT ( -> as context)]
     * hydrate() : during hydration, a copy of the [VALUE, DATA ( -> as context)]
     */
    public static function toArray($objectSource, $includeParentProperties = false, $fieldValuecallBack = []): array
    {
        $self = self::getInstance();
        $container = [];
        if (is_array($objectSource)){
            foreach($objectSource as $object) {
                $container[] = $self->extract($object, $includeParentProperties, $fieldValuecallBack);
            }
            return $container;
        }
        else{
            return $self->extract($objectSource, $includeParentProperties, $fieldValuecallBack);
        }
    }

    /**
     * @param array $data
     * @param $object
     * @param array $fieldValuecallBack
     * Note: any User Defined field-value CallBack will get parameter as follows:
     * extract() : during extraction, a copy of the [VALUE, OBJECT ( -> as context)]
     * hydrate() : during hydration, a copy of the [VALUE, DATA ( -> as context)]
     * @return object
     */
    public static function toObject(array $data, $object, $fieldValuecallBack = [])
    {
        $self = self::getInstance();
        return $self->hydrate($data, $object, $fieldValuecallBack);
    }
    
    /**
    * Class casting
    *
    * @param string|object $destination
    * @param object $sourceObject
    * @param array $fieldValuecallBack
     * Note: any User Defined field-value CallBack will get parameter as follows:
     * extract() : during extraction, a copy of the [VALUE, OBJECT ( -> as context)]
     * hydrate() : during hydration, a copy of the [VALUE, DATA ( -> as context)]
    * @return object
    */
    public static function cast($destination, $sourceObject)
    {
        if (is_string($destination)) {
            $destination = new $destination();
        }
        
        $sourceReflection = new \ReflectionObject($sourceObject);
        $destinationReflection = new \ReflectionObject($destination);
        $sourceProperties = $sourceReflection->getProperties();
        foreach ($sourceProperties as $sourceProperty) {
            $sourceProperty->setAccessible(true);
            $name = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);
            if ($destinationReflection->hasProperty($name)) {
                $propDest = $destinationReflection->getProperty($name);
                $propDest->setAccessible(true);
                $propDest->setValue($destination, $value);
            } else {
                $destination->$name = $value;
            }
        }
        return $destination;
    }
    
    /**
     * @param object $object
     * @param bool|true $includeParentProperties
     * @param array $fieldValuecallBack
     * Note: any User Defined field-value CallBack will get parameter as follows:
     * extract() : during extraction, a copy of the [VALUE, OBJECT ( -> as context)]
     * hydrate() : during hydration, a copy of the [VALUE, DATA ( -> as context)]
     * @return array
     * @throws \BadMethodCallException
     */
    public function extract($object, $includeParentProperties = true, $fieldValuecallBack = []): array
    {
        if (!is_object($object)) {
            throw new \BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object',
                __METHOD__
            ));
        }

        $objectReflection = new \ReflectionObject($object);
        $objectClass = $objectReflection->getName();
        $properties = $objectReflection->getProperties();
        $values = [];
        foreach ($properties as $p) {
            if ($includeParentProperties || $p->getDeclaringClass()->getName() == $objectClass) {
                $p->setAccessible(true);
                $name = $p->getName();
                $value = $p->getValue($object);
                $values[$name] = $this->invokeCallBackHandler($name, $value, $object, $fieldValuecallBack);
            }            
        }
        return $values;
    }

    /**
     * @param string $fieldName
     * @param $value
     * @param $context
     * @param array $fieldValuecallBack
     * @return mixed
     */
    public function invokeCallBackHandler(string $fieldName, $value, $context, array $fieldValuecallBack = [])
    {
        if (isset($fieldValuecallBack[$fieldName]) && is_callable($fieldValuecallBack[$fieldName])) {
            $value = call_user_func($fieldValuecallBack[$fieldName], $value, $context);
        }
        return $value;
    }

    /**
     * @param array $data
     * @param object $object
     * @param array $fieldValuecallBack
     * @return object
     */
    public function hydrate(array $data, object $object, array $fieldValuecallBack = [])
    {
        if (!is_object($object)) {
            throw new \BadMethodCallException(sprintf(
                '%s expects the provided $object to be a PHP object)',
                __METHOD__
            ));
        }

        $objectReflection = new \ReflectionObject($object);
        foreach ($data as $prop => $value) {
            if ($objectReflection->hasProperty($prop)) {
                $property = $objectReflection->getProperty($prop);
                $property->setAccessible(true);
                $name = $property->getName();
                $value = $this->invokeCallBackHandler($name, $value, $data, $fieldValuecallBack);
                $property->setValue($object, $value);
            }
        }
        return $object;
    }

    /**
     * @param array $data
     * @param $object
     */
    public static function hydrateSelf(array $data, &$object)
    {
        $self = self::getInstance();
        $object = $self->hydrate($data, $object);
    }   
}