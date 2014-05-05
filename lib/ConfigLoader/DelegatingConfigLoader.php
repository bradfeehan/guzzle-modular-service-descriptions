<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

/**
 * A configuration loader that delegates to other config loaders
 */
class DelegatingConfigLoader implements ConfigLoaderInterface
{

    /**
     * Stores the config loaders to delegate to
     *
     * @var array
     */
    private $loaders = array();


    /**
     * Creates a new instance for a given set of loaders
     *
     * @param array $loaders The configuration loaders to delegate to
     */
    public function __construct(array $loaders = array())
    {
        $this->loaders = $loaders;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedExtensions()
    {
        $extensions = array();

        foreach ($this->loaders as $loader) {
            $extensions = array_merge(
                $extensions,
                $loader->getSupportedExtensions()
            );
        }

        return array_unique($extensions);
    }

    /**
     * {@inheritdoc}
     */
    public function load($config, array $options = array())
    {
        $extension = pathinfo($config, PATHINFO_EXTENSION);

        foreach ($this->loaders as $loader) {
            if (in_array($extension, $loader->getSupportedExtensions())) {
                return $loader->load($config, $options);
            }
        }

        throw new InvalidArgumentException(
            "Couldn't load configuration file '$config': No " .
            "configuration loader found for extension '$extension'"
        );
    }
}
