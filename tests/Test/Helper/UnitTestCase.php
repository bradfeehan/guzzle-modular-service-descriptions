<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Helper;

use ReflectionClass;
use ReflectionObject;
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
        $mock = \Mockery::mock($this->getMockedClass());
        $mock->shouldAllowMockingProtectedMethods();
        return $mock;
    }

    /**
     * Retrieves a new instance of $this->getMockedClass()
     *
     * @param array $arguments An array of arguments to pass to the
     *                         constructor (optional)
     *
     * @return object
     */
    protected function instance(array $arguments = array())
    {
        $class = new ReflectionClass($this->getMockedClass());
        return $class->newInstanceArgs($arguments);
    }

    /**
     * Invokes a private or protected method on any object
     *
     * @param object $instance   The object to invoke the method on
     * @param string $methodName The name of the method to invoke
     * @param array  $arguments  An array of arguments to pass to the
     *                           method (optional)
     *
     * @return mixed The return value of the method
     */
    protected function callPrivateMethod($instance, $methodName, array $arguments = array())
    {
        $object = new ReflectionObject($instance);
        $method = $object->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($instance, $arguments);
    }
}
