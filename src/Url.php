<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Url\{
    Url as Structure,
    Query,
    Fragment as Frag,
    Path as UrlPath,
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
            $this->string->pregReplace(
                '/^[a-zA-Z]*:?\/\//',
                $scheme->toString() . '://',
            )->toString(),
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
        $url = Structure::of($this->toString())
            ->withQuery(
                Query::of($query->withoutQuestionMark()),
            )
            ->withoutFragment();

        return new self($url->toString());
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
        $url = Structure::of($this->toString())->withFragment(
            Frag::of($fragment->withoutHash()),
        );

        return new self($url->toString());
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
        $url = Structure::of($this->toString())
            ->withPath(
                UrlPath::of($path->toString()),
            )
            ->withoutQuery()
            ->withoutFragment();

        return new self($url->toString());
    }

    public function schemeLess(): bool
    {
        if ($this->string->startsWith('//')) {
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
        if ($this->string->startsWith('./')) {
            return true;
        }

        if ($this->string->startsWith('../')) {
            return true;
        }

        return !$this->string->matches('/^(\/|\?|#)/');
    }

    public function queryString(): bool
    {
        return $this->string->startsWith('?');
    }

    public function absolutePath(): bool
    {
        return $this->string->startsWith('/');
    }

    public function fragment(): bool
    {
        return $this->string->startsWith('#');
    }

    public function toString(): string
    {
        return $this->string->toString();
    }
}
