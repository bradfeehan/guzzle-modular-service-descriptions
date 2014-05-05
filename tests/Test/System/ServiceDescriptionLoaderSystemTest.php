<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\System;

use BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader;
use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 */
class ServiceDescriptionLoaderSystemTest extends SystemTestCase
{

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

        $this->assertInstanceOf(
            'Guzzle\\Service\\Description\\Operation',
            $operation
        );

        $this->assertSame('MyOperation', $operation->getName());

        $parameter = $operation->getParam('my_integer_parameter');
        $this->assertSame('my_integer_parameter', $parameter->getName());
        $this->assertSame('integer', $parameter->getType());
        $this->assertSame('An integer parameter', $parameter->getDescription());
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
