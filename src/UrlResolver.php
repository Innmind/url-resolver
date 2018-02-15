<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\{
    Exception\ResolutionException,
    Exception\UrlException,
    Specification\Url as UrlSpecification,
    Specification\QueryString as QueryStringSpecification,
    Specification\SchemeLess,
    Specification\Fragment as FragmentSpecification,
    Specification\AbsolutePath,
    Specification\RelativePath as RelativePathSpecification
};
use Innmind\Url\{
    Url as Structure,
    Path as UrlPath,
    NullQuery,
    NullFragment
};

final class UrlResolver implements ResolverInterface
{
    private $schemes;
    private $urlSpecification;
    private $parser;

    public function __construct(array $schemes = [])
    {
        $this->schemes = $schemes;
        $this->urlSpecification = new UrlSpecification($schemes);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(string $origin, string $destination): string
    {
        $destination = $this->createUrl($destination);

        if ($this->urlSpecification->isSatisfiedBy($destination)) {
            return (string) $destination;
        }

        $origin = $this->createUrl($origin);

        if (!$this->urlSpecification->isSatisfiedBy($origin)) {
            throw new UrlException(sprintf(
                'The origin variable is not a url (given: %s)',
                $origin
            ));
        }

        switch (true) {
            case (new QueryStringSpecification)->isSatisfiedBy($destination):
                return (string) $origin->withQueryString(
                    new QueryString((string) $destination)
                );

            case (new FragmentSpecification)->isSatisfiedBy($destination):
                return (string) $origin->withFragment(
                    new Fragment((string) $destination)
                );

            case (new AbsolutePath)->isSatisfiedBy($destination):
                return (string) $origin->withPath(
                    new Path((string) $destination)
                );

            case (new RelativePathSpecification)->isSatisfiedBy($destination):
                $originFolder = (string) Structure::fromString((string) $origin)->path();

                return (string) $origin->withPath(
                    (new Path($originFolder))
                        ->pointingTo(new RelativePath((string) $destination))
                );
        }

        throw new ResolutionException(sprintf(
            'The destination url (%s) can\'t be resolved as a valid url',
            $destination
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function folder(string $url): string
    {
        $this->validateUrl($url);
        $parsed = Structure::fromString($url);
        $path = new Path((string) $parsed->path());

        return (string) $parsed
            ->withPath(new UrlPath((string) $path->folder()))
            ->withQuery(new NullQuery)
            ->withFragment(new NullFragment);
    }

    /**
     * {@inheritdoc}
     */
    public function isFolder(string $url): bool
    {
        $this->validateUrl($url);
        $parsed = Structure::fromString($url);
        $path = new Path((string) $parsed->path());

        return $path->isFolder();
    }

    /**
     * {@inheritdoc}
     */
    public function file(string $url): string
    {
        $this->validateUrl($url);
        $parsed = Structure::fromString($url);

        return (string) $parsed->withFragment(new NullFragment);
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
        if (!(new UrlSpecification)->isSatisfiedBy(new Url($url))) {
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

        if ((new SchemeLess)->isSatisfiedBy($url)) {
            $url = $url->appendScheme(
                new Scheme($this->schemes[0] ?? 'http')
            );
        }

        return $url;
    }
}
