<?php

namespace Einenlum\PhpStackDetector\StackDetector;

use Einenlum\PhpStackDetector\DirectoryCrawler\AdapterInterface;
use Einenlum\PhpStackDetector\Exception\ResourceNotFoundException;
use Einenlum\PhpStackDetector\Stack;
use Einenlum\PhpStackDetector\StackDetectorInterface;
use Einenlum\PhpStackDetector\StackType;

class WordpressDetector implements StackDetectorInterface
{
    public function __construct(private AdapterInterface $adapter)
    {
    }

    public function getStack(string $baseUri, ?string $subDirectory): ?Stack
    {
        if (!$this->adapter->directoryExists($baseUri, $subDirectory)) {
            return null;
        }

        if (!$this->adapter->directoryExists(
            $baseUri,
            $subDirectory,
            'wp-includes'
        )) {
            return null;
        }

        try {
            $versionFileContent = $this->adapter->getFileContent(
                $baseUri,
                $subDirectory,
                'wp-includes',
                'version.php'
            );
        } catch (ResourceNotFoundException $e) {
            $versionFileContent = null;
        }

        if (null !== $versionFileContent) {
            $version = $this->parseFileAndGetVersion($versionFileContent);

            return new Stack(
                StackType::WORDPRESS,
                $version,
            );
        }

        try {
            $varsFileContent = $this->adapter->getFileContent(
                $baseUri,
                $subDirectory,
                'wp-includes',
                'vars.php'
            );
        } catch (ResourceNotFoundException $e) {
            $varsFileContent = null;
        }

        if (null !== $varsFileContent) {
            $version = $this->parseFileAndGetVersion($varsFileContent);

            return new Stack(
                StackType::WORDPRESS,
                $version,
            );
        }

        return new Stack(
            StackType::WORDPRESS,
            null
        );
    }

    private function parseFileAndGetVersion(string $fileContent): ?string
    {
        foreach (explode("\n", $fileContent) as $line) {
            if (mb_strpos($line, '$wp_version') !== false) {
                if (preg_match('/^\$wp_version\s?=\s?[\'"](.*)[\'"];/', $line, $matches)) {
                    return $matches[1];
                }
            }
        }

        return null;
    }
}
