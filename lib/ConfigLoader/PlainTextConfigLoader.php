<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

/**
 * A configuration loader that loads plain text files
 *
 * This configuration loader returns the entire content of the file
 * as a string. Files should be named with a .txt extension.
 */
class PlainTextConfigLoader implements ConfigLoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return array(
            'txt',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load($config, array $options = array())
    {
        return rtrim(file_get_contents($config), PHP_EOL);
    }
}
