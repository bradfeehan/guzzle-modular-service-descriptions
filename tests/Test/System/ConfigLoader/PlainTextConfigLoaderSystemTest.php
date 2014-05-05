<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Test\System\ConfigLoader;

use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\PlainTextConfigLoader;
use BradFeehan\GuzzleModularServiceDescriptions\Test\Helper\SystemTestCase;

/**
 * @coversNothing
 */
class PlainTextConfigLoaderSystemTest extends SystemTestCase
{

    public function testLoadSimpleTextFile()
    {
        $fixture = $this->getFixturePath('simple/description.txt');
        $data = $this->loader()->load($fixture);

        $this->assertInternalType('string', $data);
        $this->assertSame('This is a simple text value.', $data);
    }

    /**
     * Retrieves a new PlainTextConfigLoader instance
     *
     * @return \BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\PlainTextConfigLoader
     */
    private function loader()
    {
        return new PlainTextConfigLoader();
    }
}
