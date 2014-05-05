<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Unit\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\UnitTestCase;

class YamlConfigLoaderTest extends UnitTestCase
{

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\YamlConfigLoader::getSupportedExtensions
     */
    public function testGetSupportedExtensions()
    {
        $loader = $this->mock()
            ->shouldReceive('getSupportedExtensions')->passthru()
            ->getMock();

        $this->assertInternalType('array', $loader->getSupportedExtensions());
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\YamlConfigLoader::parse
     */
    public function testParse()
    {
        $loader = $this->mock()
            ->shouldReceive('parse')->passthru()
            ->getMock();

        $this->assertSame(
            array('foo' => array('bar' => 'baz')),
            $loader->parse("foo:\n  bar: baz\n")
        );
    }
}
