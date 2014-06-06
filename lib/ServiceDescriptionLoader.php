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
use RuntimeException;

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
            $nestPath = $this->getNestPath($file->getPathname(), $path);

            $content = $this->configLoader->load($file->getPathname());
            $config = $this->merge($config, $this->nest($content, $nestPath));
        }

        return $config;
    }

    /**
     * Merges two arrays together recursively
     *
     * @param array $a The first array
     * @param array $b The second array
     *
     * @return array
     */
    protected function merge(array $a, array $b)
    {
        return array_replace_recursive($b, $a);
    }

    /**
     * Determines the path to nest content for a file's path name
     *
     * @param string $path The absolute path to the file
     * @param string $base The absolute path to the base directory of
     *                     the service description this file belongs to
     *
     * @return string
     */
    protected function getNestPath($path, $base)
    {
        $ds = preg_quote(DIRECTORY_SEPARATOR);

        return preg_replace(
            // patterns to remove
            array(
                // strip the leading path (make it relative to the
                // root of the service description)
                '#^' . preg_quote($base, '#') . $ds . '#',

                // Ignore trailing __index.foo
                '/^(.*?)(:?' . $ds . '?__index)?\\.(:?\\w+)$/',

                // Remove path components ending with .group
                '#\\w+\\.group(' . $ds . '|$)#',

                // Remove any trailing slash
                '#' . $ds . '+$#',

                // Translate any remaining backslash delimiters (Windows)
                '#\\\\#'
            ),
            // replacements (corresponding with patterns above)
            array(
                '',
                '\\1',
                '',
                '',
                '/'
            ),
            $path
        );
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

    /**
     * Sorts the config so that dependencies are defined before use
     *
     * Guzzle requires things to be defined before they're used as the
     * target of an "extends" declaration. This method sorts the config
     * so that this is the case. This is called a topological sort.
     *
     * @param array $config The configuration to sort
     *
     * @return array The sorted configuration
     */
    protected function sortConfig(array $config)
    {
        // Sort operations
        if (isset($config['operations'])) {
            $operations = $config['operations'];
            $nodes = array();

            // Create empty dependencies array
            foreach (array_keys($operations) as $key) {
                $nodes[$key] = array(
                    'in'  => array(), // inbound dependencies
                    'out' => array(), // outbound dependencies
                );
            }

            // Populate dependencies
            foreach ($operations as $key => $operation) {
                if (isset($operation['extends'])) {
                    // This key depends on the thing being extended
                    $nodes[$key]['in'][] = $operation['extends'];

                    // The thing being extended depends on this key
                    $nodes[$operation['extends']]['out'][] = $key;
                }
            }

            // Build up $independent
            $independent = array();
            foreach ($nodes as $key => $node) {
                if (empty($node['in'])) {
                    $independent[] = $key;
                }
            }

            // While we have nodes with no inbound edges, remove it
            // from the graph and add it to the end of the list
            $sorted = array();
            while (!empty($independent)) {
                $key = array_shift($independent);
                $sorted[$key] = $operations[$key];

                // Go over this node's dependencies
                foreach ($nodes[$key]['out'] as $dependency) {
                    $nodes[$dependency]['in'] = array_diff(
                        $nodes[$dependency]['in'],
                        array($key)
                    );

                    // If the dependency has all of its inbound deps...
                    if (empty($nodes[$dependency]['in'])) {
                        // ...it's ready to be processed, add it to $independent.
                        $independent[] = $dependency;
                    }
                }

                $nodes[$key]['out'] = array();
            }

            foreach ($nodes as $node) {
                if (!empty($node['in']) or !empty($node['out'])) {
                    throw new RuntimeException(
                        "Couldn't sort graph, cycle detected!"
                    );
                }
            }

            $config['operations'] = $sorted;
        }

        return $config;
    }

    /**
     * {@inheritdoc}
     *
     * Overridden to sort the $config array based on dependencies
     */
    protected function build($config, array $options)
    {
        return parent::build($this->sortConfig($config), $options);
    }
}
