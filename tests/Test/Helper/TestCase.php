<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Helper;

use PHPUnit_Framework_TestCase;

/**
 * Base class for all test cases for this project
 */
abstract class TestCase extends PHPUnit_Framework_TestCase
{

    /**
     * Passes the current test without having to make an assertion
     *
     * PHPUnit marks tests as "incomplete" if they don't make any
     * assertions. But sometimes you just want to assert that no
     * exception was thrown, or that your mock objects receive all of
     * their expected method calls.
     *
     * This method allows the test to be marked as passing by making a
     * dummy assertion that always passes.
     */
    protected function pass()
    {
        $this->assertTrue(true);
    }
}
