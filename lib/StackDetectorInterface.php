<?php

declare(strict_types=1);

namespace Einenlum\PhpStackDetector;

interface StackDetectorInterface
{
    public function getStack(string $baseUri, ?string $subDirectory): ?Stack;
}
