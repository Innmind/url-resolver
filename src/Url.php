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
    /**
     * Pattern taken from symfony/validator
     *
     * Done so the validator dependency can be removed
     *
     * @see https://github.com/symfony/symfony/blob/4.2/src/Symfony/Component/Validator/Constraints/UrlValidator.php#L25
     */
    private const PATTERN = '~^
            (%s)://                                 # protocol
            (([\.\pL\pN-]+:)?([\.\pL\pN-]+)@)?      # basic auth
            (
                ([\pL\pN\pS\-\.])+(\.?([\pL\pN]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                    |                                                 # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                    # an IP address
                    |                                                 # or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # an IPv6 address
            )
            (:[0-9]+)?                              # a port (optional)
            (?:/ (?:[\pL\pN\-._\~!$&\'()*+,;=:@]|%%[0-9A-Fa-f]{2})* )*      # a path
            (?:\? (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%%[0-9A-Fa-f]{2})* )?   # a query (optional)
            (?:\# (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%%[0-9A-Fa-f]{2})* )?   # a fragment (optional)
        $~ixu';

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

    public function valid(string ...$schemes): bool
    {
        if (\count($schemes) === 0) {
            $schemes = '[a-zA-Z]+';
        } else {
            $schemes = \implode('|', $schemes);
        }

        $regex = \sprintf(self::PATTERN, $schemes);

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
