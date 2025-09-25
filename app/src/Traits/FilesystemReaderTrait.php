<?php
declare(strict_types=1);

namespace App\Traits;

trait FilesystemReaderTrait
{
    function getOrderedFilenamesList(string $dir, bool $withDirs = false): array
    {
        if (! is_dir($dir)) {
            throw new \InvalidArgumentException("Directory does not exist: $dir");
        }
        $files = new \FilesystemIterator($dir, \FilesystemIterator::UNIX_PATHS | \FilesystemIterator::SKIP_DOTS);
        $files = iterator_to_array($files);

        if (! $withDirs) {
            $files = array_filter($files, static fn($f) => $f->isFile());
        }

        uasort($files, static fn($f1, $f2) => strnatcasecmp($f1->getFilename(), $f2->getFilename()));

        $files = array_map(static fn($f) => $f->getFilename(), $files);

        return $files;
    }
}
