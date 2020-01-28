<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Exception\DestinationUrlCannotBeResolved,
    Exception\OriginIsNotAValidUrl,
};
use Innmind\Url\{
    Url as Structure,
    Path as UrlPath,
};

final class UrlResolver implements Resolver
{
    private array $schemes;

    public function __construct(array $schemes = [])
    {
        $this->schemes = $schemes;
    }

    /**
     * {@inheritdoc}
     */
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

        throw new DestinationUrlCannotBeResolved((string) $destination);
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function isFolder(string $url): bool
    {
        $this->validateUrl($url);
        $parsed = Structure::of($url);
        $path = new Path($parsed->path()->toString());

        return $path->isFolder();
    }

    /**
     * {@inheritdoc}
     */
    public function file(string $url): string
    {
        $this->validateUrl($url);
        $parsed = Structure::of($url);

        return $parsed->withoutFragment()->toString();
    }

    /**
     * Check if the given url is indeed one
     *
     * @param string $url
     *
     * @throws UrlException If it's not one
     *
     * @return void
     */
    private function validateUrl(string $url)
    {
        if (!(new Url($url))->valid(...$this->schemes)) {
            throw new UrlException(sprintf(
                'The string "%s" is not a valid url',
                $url
            ));
        }
    }

    /**
     * Create a Url object from the given string
     *
     * @param string $url
     *
     * @return Url
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
