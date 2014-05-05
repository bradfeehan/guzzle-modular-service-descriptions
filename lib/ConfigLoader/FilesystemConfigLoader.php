<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

/**
 * A configuration loader that loads files from disk
 */
abstract class FilesystemConfigLoader implements ConfigLoaderInterface
{

    /**
     * {@inheritdoc}
     */
    public function load($config, array $options = array())
    {
        return $this->parse($this->getFileContent($config));
    }

    /**
     * Loads the raw data from a file
     *
     * @param string $filename The name of the file to load data from
     *
     * @return string The content of the file
     */
    protected function getFileContent($filename)
    {
        return file_get_contents($filename);
    }

    /**
     * Parses the raw data from the file into structured data
     *
     * @param string $data The content of the file
     *
     * @return mixed
     */
    abstract protected function parse($data);
}
