<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\System;

use BradFeehan\GuzzleModularServiceDescriptions\ServiceDescriptionLoader;
use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 */
class ServiceDescriptionLoaderSystemTest extends SystemTestCase
{

    public function testLoadingModularServiceDescriptionDoesntThrowExceptions()
    {
        $loader = $this->loader();
        $loader->load($this->getFixturePath('modular/json/simple'));
        $this->pass();
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
}
