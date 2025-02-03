<?php

declare(strict_types=1);

namespace Kopernikus\TimrReportManager\Assertions;

use PHPUnit\Framework\TestCase;

trait ArrayContainsValueTrait
{
    public static function assertArrayOnlyContainsTrue(array $haystack): void
    {
        static::assertArrayOnlyContainsSameValue(true, $haystack);
    }

    public static function assertArrayOnlyContainsFalse(array $haystack)
    {
        static::assertArrayOnlyContainsSameValue(false, $haystack);
    }

    public static function assertArrayOnlyContainsSameValue(mixed $expectedValue, array $haystack): void
    {
        $haystack = array_unique($haystack);
        TestCase::assertTrue(static::areOnlySameValuesInArray($expectedValue, $haystack), message: 'The array contains of different values, yet sameness was expected');
    }

    private static function areOnlySameValuesInArray(mixed $expectedValue, array $haystack): bool
    {
        $haystack = array_unique($haystack);

        if (count($haystack) !== 1) {
            return false;
        }

        return reset($haystack) === $expectedValue;
    }
}
