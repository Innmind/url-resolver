<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Specification\AbsolutePath,
    Exception\DomainException,
};
use Innmind\Immutable\Str;

class Path extends Str
{
    public function __construct(string $value)
    {
        if (!(new AbsolutePath)->isSatisfiedBy(new Url($value))) {
            throw new DomainException($value);
        }

        parent::__construct($value);
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

        if ((string) $path->substring(0, 2) === './') {
            $path = $path->substring(2);
        }

        if ((string) $path->substring(0, 3) === '../') {
            $path = $path->substring(3);
            $folder = $folder->folder();
        }

        return new self((string) $folder . (string) $path);
    }

    /**
     * Return the folder for this path
     *
     * @return self
     */
    public function folder(): self
    {
        $folder = dirname((string) $this);

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
        return (string) $this->clean()->substring(-1) === '/';
    }

    /**
     * Return a path without any query string nor fragment
     *
     * @return self
     */
    public function clean(): self
    {
        return new self(
            (string) $this->pregReplace('(\?.*|#.*)', '')
        );
    }
}
