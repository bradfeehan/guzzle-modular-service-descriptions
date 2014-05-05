<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Unit\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\UnitTestCase;

class FilesystemConfigLoaderTest extends UnitTestCase
{

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\FilesystemConfigLoader::load
     */
    public function testLoad()
    {
        $loader = $this->mock()
            ->shouldReceive('load')->passthru()
            ->shouldReceive('getFileContent')
                ->with('$filename')
                ->andReturn('$content')
            ->shouldReceive('parse')
                ->with('$content')
                ->andReturn('$parsed')
            ->getMock();

        $this->assertSame('$parsed', $loader->load('$filename'));
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\FilesystemConfigLoader::getFileContent
     */
    public function testGetFileContent()
    {
        $loader = $this->mock()
            ->shouldReceive('getFileContent')->passthru()
            ->getMock();

        $this->assertSame(
            file_get_contents(__FILE__),
            $loader->getFileContent(__FILE__)
        );
    }
}
