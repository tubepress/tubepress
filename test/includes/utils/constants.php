<?php

class TubePressConstantsTestUtils
{
    public static function getConstantsForClass($className)
    {
        $ref = new ReflectionClass($className);
        return array_values($ref->getConstants());
    }
}