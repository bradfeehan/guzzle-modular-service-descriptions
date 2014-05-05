<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\System\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\YamlConfigLoader;
use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 */
class YamlConfigLoaderSystemTest extends SystemTestCase
{

    public function testLoadSimpleTextFile()
    {
        $fixture = $this->getFixturePath('simple/description.yaml');
        $data = $this->loader()->load($fixture);

        $this->assertInternalType('array', $data);

        $expected = array(
            'name' => 'Simple service description in YAML',
            'description' => 'A YAML service description with a single file',
        );

        $this->assertSame($expected, $data);
    }

    /**
     * Retrieves a new YamlConfigLoader instance
     *
     * @return \BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\YamlConfigLoader
     */
    private function loader()
    {
        return new YamlConfigLoader();
    }
}
