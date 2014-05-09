<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\Unit;

use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\UnitTestCase;

class ServiceDescriptionLoaderTest extends UnitTestCase
{

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader::merge
     */
    public function testMergeSimpleArrays()
    {
        $result = $this->merge(
            array('foo' => 'bar'),
            array('baz' => 'qux')
        );

        $this->assertSame(
            array(
                'baz' => 'qux',
                'foo' => 'bar',
            ),
            $result
        );
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader::merge
     */
    public function testMergeSimpleArraysWithDuplicateKeysResultsInOnlyOneValue()
    {
        $result = $this->merge(
            array(
                'name' => 'foo'
            ),
            array(
                'name' => 'bar'
            )
        );

        $this->assertArrayHasKey('name', $result);
        $this->assertInternalType('string', $result['name']);
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader::merge
     */
    public function testMergeNestedArrays()
    {
        $result = $this->merge(
            array(
                'operations' => array(
                    'abc' => array(
                        'def' => 'ghi',
                    ),
                ),
            ),
            array(
                'operations' => array(
                    'abc' => array(
                        'def' => 'jkl',
                        'mno' => 'pqr',
                    ),
                    'stu' => 'vwx',
                ),
            )
        );

        $this->assertSame(
            array(
                'operations' => array(
                    'abc' => array(
                        'def' => 'ghi',
                        'mno' => 'pqr',
                    ),
                    'stu' => 'vwx',
                ),
            ),
            $result
        );
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader::merge
     * @dataProvider dataGetNestPath
     */
    public function testGetNestPath($path, $base, $expectedOutput)
    {
        $mock = $this->mock()
            ->shouldReceive('getNestPath')->passthru()
            ->getMock();

        $output = $mock->getNestPath($path, $base);

        $this->assertSame($expectedOutput, $output);
    }

    public function dataGetNestPath()
    {
        return array(
            array(
                '/foo/bar/baz',
                '/foo/bar',
                'baz',
            ),
            array(
                '/foo/bar/baz/qux/__index.foo',
                '/foo/bar',
                'baz/qux',
            ),
            array(
                '/foo/bar/baz/qux/foo.group/__index.lol',
                '/foo/bar',
                'baz/qux',
            ),
            array(
                '/foo/bar/baz/qux/abc.group/def.group/__index.lol',
                '/foo/bar',
                'baz/qux',
            ),
        );
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader::sortConfig
     * @expectedException RuntimeException
     */
    public function testSortConfigInvalid()
    {
        $mock = $this->mock()
            ->shouldReceive('sortConfig')->passthru()
            ->getMock();

        $output = $mock->sortConfig(array(
            'operations' => array(
                'operation1' => array('extends' => 'operation2'),
                'operation2' => array('extends' => 'operation1'),
            ),
        ));
    }

    /**
     * @covers BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader::sortConfig
     * @dataProvider dataSortConfig
     */
    public function testSortConfig($input, $expectedOutput)
    {
        $mock = $this->mock()
            ->shouldReceive('sortConfig')->passthru()
            ->getMock();

        $output = $mock->sortConfig($input);

        $this->assertSame($expectedOutput, $output);
    }

    public function dataSortConfig()
    {
        return array(
            array(
                // input
                array(
                    'abc' => 'def',
                    'operations' => array(
                        'operation2' => array(
                            'extends' => 'operation1',
                        ),
                        'operation1' => array(
                            'foo' => 'bar',
                            'baz' => 'qux',
                        ),
                    ),
                ),
                // expected output
                array(
                    'abc' => 'def',
                    'operations' => array(
                        'operation1' => array(
                            'foo' => 'bar',
                            'baz' => 'qux',
                        ),
                        'operation2' => array(
                            'extends' => 'operation1',
                        ),
                    ),
                ),
            ),
        );
    }


    /**
     * Calls the "merge" function on a mock of the system under test
     */
    private function merge(array $a, array $b)
    {
        $mock = $this->mock()
            ->shouldReceive('merge')->passthru()
            ->getMock();

        return $mock->merge($a, $b);
    }
}
