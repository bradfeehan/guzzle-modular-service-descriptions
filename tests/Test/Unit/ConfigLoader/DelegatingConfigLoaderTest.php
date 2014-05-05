<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Unit\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\UnitTestCase;

class DelegatingConfigLoaderTest extends UnitTestCase
{

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DelegatingConfigLoader::__construct
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DelegatingConfigLoader::getLoaders
     */
    public function testConstructSavesLoaders()
    {
        $loaders = array('$foo', '$bar');
        $delegator = $this->instance(array($loaders));
        $this->assertSame($loaders, $delegator->getLoaders());
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DelegatingConfigLoader::getSupportedExtensions
     */
    public function testGetSupportedExtensions()
    {
        $loader1 = $this->loader(array('foo', 'bar'));
        $loader2 = $this->loader(array('baz', 'qux'));

        $delegator = $this->mock()
            ->shouldReceive('getSupportedExtensions')->passthru()
            ->shouldReceive('getLoaders')
                ->andReturn(array($loader1, $loader2))
            ->getMock();

        $this->assertSame(
            array('foo', 'bar', 'baz', 'qux'),
            $delegator->getSupportedExtensions()
        );
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DelegatingConfigLoader::load
     */
    public function testLoad()
    {
        $loader1 = $this->loader(array('foo', 'bar'))
            ->shouldReceive('load')
                ->with('a_file.bar', array())
                ->andReturn('$parsed_data')
            ->getMock();

        $loader2 = $this->loader(array('baz', 'qux'));

        $delegator = $this->mock()
            ->shouldReceive('load')->passthru()
            ->shouldReceive('getLoaders')
                ->andReturn(array($loader1, $loader2))
            ->getMock();

        $this->assertSame(
            '$parsed_data',
            $delegator->load('a_file.bar')
        );
    }

    /**
     * Retrieves a mock configuration loader
     *
     * The supported extensions can be set using $extensions.
     *
     * @param array $extensions Supported extensions (optional)
     *
     * @return \BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\ConfigLoaderInterface
     */
    private function loader(array $extensions = array())
    {
        $interface = 'BradFeehan\\GuzzleModularServiceDescriptions\\' .
            'ConfigLoader\\ConfigLoaderInterface';

        return \Mockery::mock($interface)
            ->shouldReceive('getSupportedExtensions')
                ->andReturn($extensions)
            ->getMock();
    }
}
