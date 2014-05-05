<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Helper;

use UnexpectedValueException;

/**
 * Base class for all system test cases
 *
 * All subclasses of this class should be in tests/Test/System.
 */
abstract class SystemTestCase extends TestCase
{

    /**
     * The absolute path to the tests/fixtures directory
     *
     * @var string
     */
    private static $fixturePath;


    /**
     * Sets the path to the tests/fixtures directory
     *
     * This should be called before running any tests that require
     * fixtures to be loaded.
     *
     * @param string $path The path to the fixtures directory
     */
    public static function setFixturePath($path)
    {
        self::$fixturePath = rtrim($path, '/');
    }


    /**
     * Determines the absolute path to a particular fixture
     *
     * If the fixture name is omitted, returns the path where fixtures
     * are stored under, as set by self::setFixturePath().
     *
     * @param string $fixtureName The name of the fixture to retrieve
     *
     * @return string
     */
    protected function getFixturePath($fixtureName = '')
    {
        if (!self::$fixturePath) {
            throw new UnexpectedValueException('No fixture path set');
        }

        $ds = DIRECTORY_SEPARATOR;
        return self::$fixturePath . $ds . ltrim($fixtureName, '/');
    }
}
