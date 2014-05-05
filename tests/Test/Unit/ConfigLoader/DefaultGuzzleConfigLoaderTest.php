<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Unit\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\UnitTestCase;

class DefaultGuzzleConfigLoaderTest extends UnitTestCase
{

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DefaultGuzzleConfigLoader::getSupportedExtensions
     */
    public function testGetSupportedExtensions()
    {
        $loader = $this->mock()
            ->shouldReceive('getSupportedExtensions')->passthru()
            ->getMock();

        $this->assertInternalType('array', $loader->getSupportedExtensions());
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DefaultGuzzleConfigLoader::build
     */
    public function testBuild()
    {
        $result = $this->callPrivateMethod(
            $this->instance(),
            'build',
            array('$config', array())
        );

        $this->assertSame('$config', $result);
    }
}
