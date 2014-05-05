<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

use Symfony\Component\Yaml\Yaml;

/**
 * A configuration loader that loads YAML files
 */
class YamlConfigLoader implements ConfigLoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return array(
            'yaml',
            'yml',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function load($config, array $options = array())
    {
        return Yaml::parse(file_get_contents($config));
    }
}
