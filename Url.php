<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Immutable\StringPrimitive;
use Pdp\Parser;
use Pdp\Uri\Url as ParsedUrl;

final class Url extends StringPrimitive
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
     * @param Parser $parser Helper used to replace the query string
     *
     * @return self
     */
    public function withQueryString(QueryString $query, Parser $parser): self
    {
        $parsed = $parser->parseUrl((string) $this);

        return new self(
            (string) new ParsedUrl(
                $parsed->scheme,
                $parsed->user,
                $parsed->pass,
                $parsed->host,
                $parsed->port,
                $parsed->path,
                (string) $query->substring(1),
                ''
            )
        );
    }

    /**
     * Return a new url with the given fragment
     *
     * @param Fragment $fragment
     * @param Parser $parser Helper used to replace the fragment
     *
     * @return self
     */
    public function withFragment(Fragment $fragment, Parser $parser): self
    {
        $parsed = $parser->parseUrl((string) $this);

        return new self(
            (string) new ParsedUrl(
                $parsed->scheme,
                $parsed->user,
                $parsed->pass,
                $parsed->host,
                $parsed->port,
                $parsed->path,
                $parsed->query,
                (string) $fragment->substring(1)
            )
        );
    }
}
