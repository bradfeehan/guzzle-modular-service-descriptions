<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Unit\Utility;

use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\UnitTestCase;

class FileFilterIteratorTest extends UnitTestCase
{

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\Utility\FileFilterIterator::accept
     */
    public function testAccept()
    {
        $item = \Mockery::mock('SplFileInfo')
            ->shouldReceive('isFile')
                ->andReturn(true)
            ->shouldReceive('isReadable')
                ->andReturn(true)
            ->getMock();

        $iterator = $this->mock()
            ->shouldReceive('accept')->passthru()
            ->shouldReceive('current')
                ->andReturn($item)
            ->getMock();

        $this->assertTrue($iterator->accept());
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\Utility\FileFilterIterator::accept
     */
    public function testAcceptWithWrongClass()
    {
        $iterator = $this->mock()
            ->shouldReceive('accept')->passthru()
            ->shouldReceive('current')
                ->andReturn(\Mockery::mock())
            ->getMock();

        $this->assertFalse($iterator->accept());
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\Utility\FileFilterIterator::accept
     */
    public function testAcceptWithNonFile()
    {
        $item = \Mockery::mock('SplFileInfo')
            ->shouldReceive('isFile')
                ->andReturn(false)
            ->shouldReceive('isReadable')
                ->andReturn(true)
            ->getMock();

        $iterator = $this->mock()
            ->shouldReceive('accept')->passthru()
            ->shouldReceive('current')
                ->andReturn($item)
            ->getMock();

        $this->assertFalse($iterator->accept());
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\Utility\FileFilterIterator::accept
     */
    public function testAcceptWithNonReadableFile()
    {
        $item = \Mockery::mock('SplFileInfo')
            ->shouldReceive('isFile')
                ->andReturn(true)
            ->shouldReceive('isReadable')
                ->andReturn(false)
            ->getMock();

        $iterator = $this->mock()
            ->shouldReceive('accept')->passthru()
            ->shouldReceive('current')
                ->andReturn($item)
            ->getMock();

        $this->assertFalse($iterator->accept());
    }
}
