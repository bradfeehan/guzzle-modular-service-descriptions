<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\System\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DefaultGuzzleConfigLoader;
use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 */
class DefaultGuzzleConfigLoaderSystemTest extends SystemTestCase
{

    public function testLoadSimpleJsonFile()
    {
        $fixture = $this->getFixturePath('simple/description.json');
        $data = $this->loader()->load($fixture);

        $this->assertInternalType('array', $data);

        $expected = array(
            'name' => 'Simple service description in JSON',
            'description' => 'A JSON service description with a single file',
        );

        $this->assertSame($expected, $data);
    }

    /**
     * Retrieves a new DefaultGuzzleConfigLoader instance
     *
     * @return \BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DefaultGuzzleConfigLoader
     */
    private function loader()
    {
        return new DefaultGuzzleConfigLoader();
    }
}
