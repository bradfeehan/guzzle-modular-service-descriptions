<?php

namespace BradFeehan\GuzzleModularServiceDescriptions;

use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\ConfigLoaderInterface;
use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DefaultGuzzleConfigLoader;
use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\DelegatingConfigLoader;
use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\PlainTextConfigLoader;
use BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\YamlConfigLoader;
use BradFeehan\GuzzleModularServiceDescriptions\Utility\FileFilterIterator;
use Guzzle\Service\Description\ServiceDescriptionLoader as GuzzleServiceDescriptionLoader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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
     * The configuration loader to use
     *
     * @var \BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\ConfigLoaderInterface
     */
    private $configLoader;


    /**
     * Allows setting the configuration loader to use
     *
     * @param null|\BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader\ConfigLoaderInterface $configLoader
     */
    public function __construct(ConfigLoaderInterface $configLoader = null)
    {
        if (!$configLoader) {
            $configLoader = new DelegatingConfigLoader(array(
                new DefaultGuzzleConfigLoader(),
                new PlainTextConfigLoader(),
                new YamlConfigLoader(),
            ));
        }

        $this->configLoader = $configLoader;
    }

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
     * @return array
     */
    protected function loadModular($path)
    {
        $config = array();

        // Loop over files in $path (recursing into sub-directories)
        foreach ($this->filesIn($path) as $file) {
            // Determine the relative path of the current file by
            // stripping $path from the beginning of the absolute path
            $nestPath = preg_replace(
                // patterns to remove
                array(
                    // strip the leading path (make it relative to the
                    // root of the service description)
                    '#^' . preg_quote($path, '#') . '/#',

                    // Ignore trailing __index.foo
                    '/^(.*?)(:?\\/?__index)?\\.(:?\\w+)$/',

                    // Remove path components ending with .group
                    '#/\\w+\\.group/#',
                ),
                // replacements (corresponding with patterns above)
                array(
                    '',
                    '\\1',
                    '/',
                ),
                $file->getPathname()
            );

            $content = $this->configLoader->load($file->getPathname());
            $config = array_merge_recursive(
                $config,
                $this->nest($content, $nestPath)
            );
        }

        return $config;
    }

    /**
     * Gets all files in a directory, recursing into sub-directories
     *
     * This returns an iterator which can be used to iterate over every
     * file under the given directory. It filters out anything that's
     * not a regular file, so directories, symlinks, etc. won't be
     * iterated over.
     *
     * @param string $path The path to iterate over
     *
     * @return \BradFeehan\GuzzleModularServiceDescriptions\Utility\FileFilterIterator
     */
    protected function filesIn($path)
    {
        return new FileFilterIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path)
            )
        );
    }

    /**
     * Nests an array under a particular path
     *
     * As an example:
     *
     *   nest(array('foo' => 'bar'), 'baz/qux')
     *
     * This will return:
     *
     *   array(
     *       'baz' => array(
     *           'qux' => array(
     *               'foo' => 'bar'
     *           )
     *       )
     *   )
     *
     * @param mixed  $value The value to put at the given path
     * @param string $path  The slash-separated path to put the value
     *
     * @return array
     */
    private function nest($value, $path)
    {
        if ($path) {
            $elements = explode('/', $path);

            foreach (array_reverse($elements) as $element) {
                $value = array($element => $value);
            }
        }

        return $value;
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
