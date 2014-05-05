<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

use Symfony\Component\Yaml\Yaml;

/**
 * A configuration loader that loads YAML files
 */
class YamlConfigLoader extends FilesystemConfigLoader
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
    protected function parse($data)
    {
        return Yaml::parse($data);
    }
}
