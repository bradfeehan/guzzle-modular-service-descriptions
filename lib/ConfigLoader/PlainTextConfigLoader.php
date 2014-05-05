<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

/**
 * A configuration loader that loads plain text files
 *
 * This configuration loader returns the entire content of the file
 * as a string. Files should be named with a .txt extension.
 */
class PlainTextConfigLoader extends FilesystemConfigLoader
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
    protected function parse($data)
    {
        return rtrim($data, PHP_EOL);
    }
}
