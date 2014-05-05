<?php

namespace BradFeehan\GuzzleModularServiceDescriptions\Utility;

use FilterIterator;
use SplFileInfo;

/**
 * A FilterIterator that filters out non-file objects
 */
class FileFilterIterator extends FilterIterator
{

    /**
     * {@inheritdoc}
     *
     * Accept only file objects
     */
    public function accept()
    {
        $current = $this->current();

        if ($current instanceof SplFileInfo) {
            return $current->isFile() && $current->isReadable();
        }

        // Unknown type of object
        return false;
    }
}
