<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Helper;

use UnexpectedValueException;

/**
 * Base class for all unit test cases
 *
 * All subclasses of this class should be in tests/Test/Unit.
 */
abstract class UnitTestCase extends TestCase
{

    /**
     * Determines the name of the class to mock with $this->mock()
     *
     * This implementation tries to guess the class name to use. If
     * it's wrong, it should be re-implemented in the child class.
     *
     * @return string
     */
    protected function getMockedClass()
    {
        $className = preg_replace(
            array(
                // Patterns
                '#^BradFeehan\\\\GuzzleModularServiceDescriptions\\\\Test\\\\Unit#',
                '#Test$#',
            ),
            array(
                // Replacements (order corresponds to patterns above)
                'BradFeehan\\\\GuzzleModularServiceDescriptions',
                '',
            ),
            get_class($this)
        );

        if (!class_exists($className)) {
            throw new UnexpectedValueException(
                "Couldn't guess mock class for " . get_class($this) .
                ", guess of '$className' isn't defined"
            );
        }

        return $className;
    }

    /**
     * Retrieves a mocked instance of $this->getMockedClass()
     *
     * @return \Mockery\MockInterface
     */
    protected function mock()
    {
        return \Mockery::mock($this->getMockedClass());
    }
}
