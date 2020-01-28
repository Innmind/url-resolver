<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\DomainException;
use Innmind\Immutable\Str;

final class Path
{
    private Str $string;

    public function __construct(string $value)
    {
        if (!(new UrlRepresentation($value))->absolutePath()) {
            throw new DomainException($value);
        }

        $this->string = Str::of($value);
    }

    /**
     * Resolve the wished directory
     */
    public function pointingTo(RelativePath $path): self
    {
        $folder = $this->clean();

        if (!$this->isFolder()) {
            $folder = $this->folder();
        }

        if ($path->startsWithSelfReference()) {
            $path = $path->removeSelfReference();
        }

        if ($path->startsWithParentFolderReference()) {
            $path = $path->removeParentFolderReference();
            $folder = $folder->folder();
        }

        return new self($folder->toString() . $path->toString());
    }

    /**
     * Return the folder for this path
     */
    public function folder(): self
    {
        $folder = dirname($this->toString());

        return new self(
            $folder === '/' ? '/' : $folder . '/'
        );
    }

    /**
     * Chek if the path is a folder
     */
    public function isFolder(): bool
    {
        return $this->clean()->string->endsWith('/');
    }

    /**
     * Return a path without any query string nor fragment
     */
    public function clean(): self
    {
        return new self(
            $this->string->pregReplace('(\?.*|#.*)', '')->toString(),
        );
    }

    public function toString(): string
    {
        return $this->string->toString();
    }
}
