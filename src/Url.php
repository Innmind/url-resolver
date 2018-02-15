<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Url\{
    Url as Structure,
    Query,
    Fragment as Frag,
    Path as UrlPath,
    NullFragment,
    NullQuery
};
use Innmind\Immutable\Str;

final class Url extends Str
{
    /**
     * Append the given scheme to the url
     *
     * @param Scheme $scheme
     *
     * @return self
     */
    public function appendScheme(Scheme $scheme): self
    {
        return new self(
            (string) $this->pregReplace(
                '/^[a-zA-Z]*:?\/\//',
                (string) $scheme . '://'
            )
        );
    }

    /**
     * Return a new url with the given query string
     *
     * @param QueryString $query
     *
     * @return self
     */
    public function withQueryString(QueryString $query): self
    {
        $url = Structure::fromString((string) $this)
            ->withQuery(
                Query::fromString((string) $query->substring(1))
            )
            ->withFragment(new NullFragment);

        return new self(
            (string) $url
        );
    }

    /**
     * Return a new url with the given fragment
     *
     * @param Fragment $fragment
     *
     * @return self
     */
    public function withFragment(Fragment $fragment): self
    {
        $url = Structure::fromString((string) $this)->withFragment(
            new Frag((string) $fragment->substring(1))
        );

        return new self(
            (string) $url
        );
    }

    /**
     * Return a new url with the given path
     *
     * @param Path $path
     *
     * @return self
     */
    public function withPath(Path $path): self
    {
        $url = Structure::fromString((string) $this)
            ->withPath(
                new UrlPath((string) $path)
            )
            ->withQuery(new NullQuery)
            ->withFragment(new NullFragment);

        return new self(
            (string) $url
        );
    }
}
