<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Unit\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\UnitTestCase;

class PlainTextConfigLoaderTest extends UnitTestCase
{

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\PlainTextConfigLoader::getSupportedExtensions
     */
    public function testGetSupportedExtensions()
    {
        $loader = $this->mock()
            ->shouldReceive('getSupportedExtensions')->passthru()
            ->getMock();

        $this->assertInternalType('array', $loader->getSupportedExtensions());
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\PlainTextConfigLoader::parse
     */
    public function testParse()
    {
        $loader = $this->mock()
            ->shouldReceive('parse')->passthru()
            ->getMock();

        $this->assertSame("foo\nbar\nbaz", $loader->parse("foo\nbar\nbaz\n"));
    }
}
