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
        if (!(new Url($value))->absolutePath()) {
            throw new DomainException($value);
        }

        $this->string = Str::of($value);
    }

    /**
     * Resolve the wished directory
     *
     * @param RelativePath $path
     *
     * @return self
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
     *
     * @return self
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
     *
     * @return bool
     */
    public function isFolder()
    {
        return $this->clean()->string->endsWith('/');
    }

    /**
     * Return a path without any query string nor fragment
     *
     * @return self
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
