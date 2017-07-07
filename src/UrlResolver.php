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
use Pdp\{
    Parser,
    PublicSuffixListManager,
    Uri\Url as ParsedUrl
};

final class UrlResolver implements ResolverInterface
{
    private $schemes;
    private $urlSpecification;
    private $parser;

    public function __construct(
        array $schemes = [],
        Parser $parser = null
    ) {
        $this->schemes = $schemes;
        $this->urlSpecification = new UrlSpecification($schemes);

        $this->parser = $parser ?? new Parser(
            (new PublicSuffixListManager)->getList()
        );
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
                    new QueryString((string) $destination),
                    $this->parser
                );

            case (new FragmentSpecification)->isSatisfiedBy($destination):
                return (string) $origin->withFragment(
                    new Fragment((string) $destination),
                    $this->parser
                );

            case (new AbsolutePath)->isSatisfiedBy($destination):
                return (string) $origin->withPath(
                    new Path((string) $destination),
                    $this->parser
                );

            case (new RelativePathSpecification)->isSatisfiedBy($destination):
                $originFolder = $this
                    ->parser
                    ->parseUrl((string) $origin)
                    ->path ?? '/';

                return (string) $origin->withPath(
                    (new Path($originFolder))
                        ->pointingTo(new RelativePath((string) $destination)),
                    $this->parser
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
        $parsed = $this->parser->parseUrl($url);
        $path = new Path($parsed->path);

        return (string) new ParsedUrl(
            $parsed->scheme,
            $parsed->user,
            $parsed->pass,
            $parsed->host,
            $parsed->port,
            (string) $path->folder(),
            '',
            ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isFolder(string $url): bool
    {
        $this->validateUrl($url);
        $parsed = $this->parser->parseUrl($url);

        return (new Path($parsed->path))->isFolder();
    }

    /**
     * {@inheritdoc}
     */
    public function file(string $url): string
    {
        $this->validateUrl($url);
        $parsed = $this->parser->parseUrl($url);

        return (string) new ParsedUrl(
            $parsed->scheme,
            $parsed->user,
            $parsed->pass,
            $parsed->host,
            $parsed->port,
            $parsed->path,
            $parsed->query,
            ''
        );
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
