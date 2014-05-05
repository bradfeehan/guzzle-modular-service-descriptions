<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

use Guzzle\Service\AbstractConfigLoader;

/**
 * A config loader that delegates to Guzzle
 *
 * Guzzle supports JSON and PHP configuration files by default. This
 * parser delegates to Guzzle's AbstractConfigLoader implementation to
 * load configuration files of these types.
 */
class DefaultGuzzleConfigLoader
    extends AbstractConfigLoader
    implements ConfigLoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        return array(
            'js',
            'json',
            'php',
        );
    }

    /**
     * {@inheritdoc}
     *
     * Returns the configuration data untouched.
     */
    protected function build($config, array $options)
    {
        return $config;
    }
}
