<?php

class TubePressChainTestUtils
{
    public static function assertCommandCannotHandle($command, $context)
    {
        TubePressUnitTest::assertFalse($command->execute($context));
    }
    
    public static function assertCommandCanHandle($command, $context)
    {
        TubePressUnitTest::assertTrue($command->execute($context));
    }

    public static function assertCommandReturnValueEquals($returnValue, $command, $content)
    {
        self::assertCommandCanHandle($command, $context);
        self::assertEquals($returnValue, $context->getReturnValue());
    }
}
