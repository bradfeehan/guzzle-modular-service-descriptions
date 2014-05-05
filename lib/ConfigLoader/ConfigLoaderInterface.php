<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\ConfigLoader;

use Guzzle\Service\ConfigLoaderInterface as GuzzleConfigLoaderInterface;

/**
 * Loads raw configuration data from files with particular extensions
 *
 * This is a more specific form of Guzzle's ConfigLoaderInterface,
 * requiring implementing classes to explicitly specify the file
 * extensions that they support.
 *
 * Guzzle's ConfigLoaderInterface doesn't specify what type the config
 * loader should return, so implementing classes typically return
 * highly structured data (i.e. objects).
 *
 * This interface is stricter, in that it requires implementing classes
 * to always return a primitive representing the contents of the loaded
 * file.
 */
interface ConfigLoaderInterface extends GuzzleConfigLoaderInterface
{

    /**
     * Retrieves the file extensions that this config loader supports
     *
     * @return array<string> An array of strings
     */
    public function getSupportedExtensions();
}
