<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Url\{
    Url as Structure,
    Query,
    Fragment as Frag,
    Path as UrlPath,
    NullFragment,
    NullQuery,
};
use Innmind\Immutable\Str;

final class Url
{
    private Str $string;

    public function __construct(string $url)
    {
        $this->string = Str::of($url);
    }

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
            (string) $this->string->pregReplace(
                '/^[a-zA-Z]*:?\/\//',
                $scheme->toString() . '://',
            ),
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
        $url = Structure::fromString($this->toString())
            ->withQuery(
                Query::fromString($query->withoutQuestionMark()),
            )
            ->withFragment(new NullFragment);

        return new self(
            (string) $url,
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
        $url = Structure::fromString($this->toString())->withFragment(
            new Frag($fragment->withoutHash()),
        );

        return new self(
            (string) $url,
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
        $url = Structure::fromString($this->toString())
            ->withPath(
                new UrlPath($path->toString()),
            )
            ->withQuery(new NullQuery)
            ->withFragment(new NullFragment);

        return new self(
            (string) $url,
        );
    }

    public function schemeLess(): bool
    {
        if ((string) $this->string->substring(0, 2) === '//') {
            return true;
        }

        return !$this->string->matches('/^[a-zA-Z]*:?\/\//');
    }

    public function matches(string $regex): bool
    {
        return $this->string->matches($regex);
    }

    public function relativePath(): bool
    {
        if ((string) $this->string->substring(0, 2) === './') {
            return true;
        }

        if ((string) $this->string->substring(0, 3) === '../') {
            return true;
        }

        return !$this->string->matches('/^(\/|\?|#)/');
    }

    public function queryString(): bool
    {
        return (string) $this->string->substring(0, 1) === '?';
    }

    public function absolutePath(): bool
    {
        return (string) $this->string->substring(0, 1) === '/';
    }

    public function fragment(): bool
    {
        return (string) $this->string->substring(0, 1) === '#';
    }

    public function toString(): string
    {
        return (string) $this->string;
    }
}
