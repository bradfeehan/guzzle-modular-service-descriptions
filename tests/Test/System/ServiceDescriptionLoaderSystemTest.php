<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\System;

use BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader;
use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\SystemTestCase;

/**
 * @covers BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader
 */
class ServiceDescriptionLoaderSystemTest extends SystemTestCase
{

    /**
     * The name of the class Operations should be
     */
    const OPERATION_CLASS = 'Guzzle\\Service\\Description\\Operation';

    public function testLoadingSimpleServiceDescription()
    {
        $description = $this->loadFixture('modular/json/simple');

        $this->assertInstanceOf(
            'Guzzle\\Service\\Description\\ServiceDescription',
            $description
        );

        $this->assertSame(
            'Simple JSON modular service description',
            $description->getName()
        );

        $this->assertSame(
            'A modular service description written in JSON with only one file',
            $description->getDescription()
        );
    }

    /**
     * @depends testLoadingSimpleServiceDescription
     */
    public function testLoadingNestedServiceDescription()
    {
        $description = $this->loadFixture('modular/json/nested');

        $this->assertInstanceOf(
            'Guzzle\\Service\\Description\\ServiceDescription',
            $description
        );

        $this->assertSame(
            'Nested JSON modular service description',
            $description->getName()
        );

        $this->assertSame(
            'A modular service description written in JSON, with nested files',
            $description->getDescription()
        );

        $operation = $description->getOperation('MyOperation');
        $this->assertInstanceOf(self::OPERATION_CLASS, $operation);

        $this->assertSame('MyOperation', $operation->getName());

        $parameter = $operation->getParam('my_integer_parameter');
        $this->assertSame('my_integer_parameter', $parameter->getName());
        $this->assertSame('integer', $parameter->getType());
        $this->assertSame('An integer parameter', $parameter->getDescription());
    }

    /**
     * @depends testLoadingNestedServiceDescription
     */
    public function testLoadingComplexServiceDescription()
    {
        $description = $this->loadFixture('modular/mixed/complex');

        $this->assertInstanceOf(
            'Guzzle\\Service\\Description\\ServiceDescription',
            $description
        );

        $this->assertSame(
            'Nested mixed-type modular service description',
            $description->getName()
        );

        $this->assertSame(
            'A modular service description with nested files in mixed formats',
            $description->getDescription()
        );

        // Should be four operations in total
        $this->assertSame(4, count($description->getOperations()));

        // Complex operation
        $complex = $description->getOperation('ComplexOperation');
        $this->assertInstanceOf(self::OPERATION_CLASS, $complex);
        $this->assertSame('ComplexOperation', $complex->getName());

        $parameter = $complex->getParam('my_string_parameter');
        $this->assertSame('my_string_parameter', $parameter->getName());
        $this->assertSame('string', $parameter->getType());
        $this->assertSame('A string parameter', $parameter->getDescription());

        // Grouped operation
        $grouped = $description->getOperation('GroupedComplexOperation');
        $this->assertInstanceOf(self::OPERATION_CLASS, $grouped);
        $this->assertSame('GroupedComplexOperation', $grouped->getName());

        // Nested, grouped operation
        $nested = $description->getOperation('NestedGroupedOperation');
        $this->assertInstanceOf(self::OPERATION_CLASS, $nested);
        $this->assertSame('NestedGroupedOperation', $nested->getName());

        // Nested, grouped operation in __index.json
        $index = $description->getOperation('NestedGroupedIndexOperation');
        $this->assertInstanceOf(self::OPERATION_CLASS, $index);
        $this->assertSame('NestedGroupedIndexOperation', $index->getName());
        $this->assertSame('DELETE', $index->getHttpMethod());
    }

    public function testLoadingNonModularServiceDescription()
    {
        $description = $this->loadFixture('simple/description.json');

        $this->assertInstanceOf(
            'Guzzle\\Service\\Description\\ServiceDescription',
            $description
        );

        $this->assertSame(
            'Simple service description in JSON',
            $description->getName()
        );

        $this->assertSame(
            'A JSON service description with a single file',
            $description->getDescription()
        );
    }

    public function testLoadingWithAlias()
    {
        $loader = $this->loader();
        $realFile = $this->getFixturePath('modular/json/simple');
        $loader->addAlias('the alias', $realFile);
        $description = $loader->load('the alias');

        $this->assertInstanceOf(
            'Guzzle\\Service\\Description\\ServiceDescription',
            $description
        );

        $this->assertSame(
            'Simple JSON modular service description',
            $description->getName()
        );

        $this->assertSame(
            'A modular service description written in JSON with only one file',
            $description->getDescription()
        );
    }

    /**
     * Retrieves a new instance of the service description loader
     *
     * @return \BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader
     */
    private function loader()
    {
        return new ServiceDescriptionLoader();
    }

    /**
     * Uses the service description loader to load a fixture
     *
     * The fixture should be specified relative to the fixtures
     * directory, e.g. "modular/json/simple".
     *
     * @param string $fixtureName The name of the fixture to load
     *
     * @return \Guzzle\Service\Description\ServiceDescription
     */
    private function loadFixture($fixtureName)
    {
        return $this->loader()->load($this->getFixturePath($fixtureName));
    }
}
