<?php
/**
 * Created by PhpStorm.
 * User: Ademola Aina
 */
namespace Emma\Common\Singleton;


trait Singleton
{
    private static $instance = null;

    /**
     * @return $this
     */
    public static function getInstance(): static
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}