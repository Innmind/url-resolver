<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Exception\DestinationUrlCannotBeResolved,
    Exception\OriginIsNotAValidUrl,
    Exception\DomainException,
};
use Innmind\Url\{
    Url as Structure,
    Path as UrlPath,
};

final class UrlResolver implements Resolver
{
    /** @var list<string> */
    private array $schemes;

    public function __construct(string ...$schemes)
    {
        $this->schemes = $schemes;
    }

    public function resolve(string $origin, string $destination): string
    {
        $destination = $this->createUrl($destination);

        if ($destination->valid(...$this->schemes)) {
            return $destination->toString();
        }

        $origin = $this->createUrl($origin);

        if (!$origin->valid(...$this->schemes)) {
            throw new OriginIsNotAValidUrl($origin->toString());
        }

        switch (true) {
            case $destination->queryString():
                return $origin->withQueryString(
                    new QueryString($destination->toString()),
                )->toString();

            case $destination->fragment():
                return $origin->withFragment(
                    new Fragment($destination->toString()),
                )->toString();

            case $destination->absolutePath():
                return $origin->withPath(
                    new Path($destination->toString()),
                )->toString();

            case $destination->relativePath():
                $originFolder = Structure::of($origin->toString())->path()->toString();

                return $origin->withPath(
                    (new Path($originFolder))
                        ->pointingTo(new RelativePath($destination->toString())),
                )->toString();
        }

        throw new DestinationUrlCannotBeResolved($destination->toString());
    }

    public function folder(string $url): string
    {
        $this->validateUrl($url);
        $parsed = Structure::of($url);
        $path = new Path($parsed->path()->toString());

        return $parsed
            ->withPath(UrlPath::of($path->folder()->toString()))
            ->withoutQuery()
            ->withoutFragment()
            ->toString();
    }

    public function file(string $url): string
    {
        $this->validateUrl($url);
        $parsed = Structure::of($url);

        return $parsed->withoutFragment()->toString();
    }

    /**
     * Check if the given url is indeed one
     *
     * @throws DomainException If it's not one
     */
    private function validateUrl(string $url): void
    {
        if (!(new Url($url))->valid(...$this->schemes)) {
            throw new DomainException($url);
        }
    }

    /**
     * Create a Url object from the given string
     */
    private function createUrl(string $url): Url
    {
        $url = new Url($url);

        if ($url->schemeLess()) {
            $url = $url->appendScheme(
                new Scheme($this->schemes[0] ?? 'http')
            );
        }

        return $url;
    }
}
