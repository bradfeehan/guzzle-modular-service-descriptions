<?php

namespace BradFeehan\GuzzleModularServiceDescriptions;

use Guzzle\Service\Description\ServiceDescriptionLoader as GuzzleServiceDescriptionLoader;

/**
 * Loads modular service descriptions
 *
 * Modular service descriptions are implemented as a directory of files
 * which are merged together to create the final service description.
 *
 * Separating parts of the description into separate files makes it
 * much easier to maintain, especially with long or complex service
 * descriptions.
 */
class ServiceDescriptionLoader extends GuzzleServiceDescriptionLoader
{

    /**
     * {@inheritdoc}
     *
     * Overridden to support loading modular service descriptions.
     */
    protected function loadFile($filename)
    {
        // Save this so we can modify it and still delegate to parent
        $originalFilename = $filename;

        if (isset($this->aliases[$filename])) {
            $filename = $this->aliases[$filename];
        }

        if ($this->isModular($filename)) {
            // Description is modular, this class should handle it
            return $this->loadModular($filename);
        }

        // Description isn't modular, delegate to parent with the
        // original (unmodified) value of $filename
        return parent::loadFile($originalFilename);
    }

    /**
     * Loads a modular service description
     *
     * @param string $path Path to the service description to load
     *
     * @return \Guzzle\Service\Description\ServiceDescription
     */
    protected function loadModular($path)
    {
        // TODO
        return array();
    }

    /**
     * Determines if a service description on disk is modular or not
     *
     * This is useful to work out whether this class should handle a
     * given service description, or delegate to its parent
     * implementation.
     *
     * @param string $filename The path to the service description
     *
     * @return boolean
     */
    protected function isModular($filename)
    {
        // Currently, modular service descriptions are always
        // implemented as a directory. Guzzle normally doesn't handle
        // a directory as a service description, so if it's a directory
        // this class should handle it (because Guzzle sure can't).
        return is_dir($filename);
    }
}
