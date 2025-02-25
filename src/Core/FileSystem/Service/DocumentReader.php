<?php

namespace App\Core\FileSystem\Service;

/**
 *  Class that reads a document line by line
 */
class DocumentReader
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Reads a document line by line
     * @return \Generator
     */
    public function getLines(): \Generator
    {
        $file = new \SplFileObject($this->filePath);

        while (!$file->eof()) {
            $line = $file->fgets();
            if (!empty(trim($line))) {
                yield $line;
            }
        }
    }

}
