<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\ResolutionException;
use Innmind\UrlResolver\Exception\UrlException;
use Innmind\UrlResolver\Specification\Url as UrlSpecification;
use Innmind\UrlResolver\Specification\QueryString as QueryStringSpecification;
use Innmind\UrlResolver\Specification\SchemeLess;
use Innmind\UrlResolver\Specification\Fragment as FragmentSpecification;
use Innmind\UrlResolver\Specification\AbsolutePath;
use Innmind\UrlResolver\Specification\RelativePath as RelativePathSpecification;
use Pdp\Parser;
use Pdp\PublicSuffixListManager;
use Pdp\Uri\Url as ParsedUrl;

class UrlResolver implements ResolverInterface
{
    protected $urlSpecification;
    protected $parser;

    public function __construct(
        array $schemes = [],
        Parser $parser = null
    ) {
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
                    ->path;

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
    protected function validateUrl(string $url)
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
