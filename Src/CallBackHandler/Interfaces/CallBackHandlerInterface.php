<?php
namespace Emma\Common\Interfaces;

interface CallBackHandlerInterface 
{

    public static function create($callback, array $args = [], bool $isCallableVerified = false);

    public static function get($callBack, array $args = [], bool $isCallableVerified = false);

    /**
     * @param array $args
     * This function can be used after calling the fn 'create'. 
     * That is, you can use this if NOT using the static direct function 'get'
     */
    public function call(array $args = []);

}