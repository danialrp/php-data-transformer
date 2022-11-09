<?php

namespace DanialPanah\DataTransformer\Tests;

use PHPUnit\Framework\TestCase;

class PhpDataTransformerTestCase extends TestCase
{
    protected function assertArrayContains($value, array $array, string $message = null): void
    {
        if (in_array($value, $array))
            $this->assertTrue(true);
        else
            $this->fail($message ?? "asserted array contains '{$value}'");

    }

    protected function assertArrayNotContains($value, array $array, string $message = null): void
    {
        if (in_array($value, $array))
            $this->fail($message ?? "asserted array NOT contains '{$value}'");
        else
            $this->assertTrue(true);

    }

    protected function assertArrayInArray(array $search, array $highStack): void
    {
        $highStackStr = implode(', ', $highStack);

        if (array_diff($search, $highStack))
            $this->fail($message ?? "asserted array NOT in '{$highStackStr}'");
        else
            $this->assertTrue(true);
    }
}