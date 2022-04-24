<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\Url\{
    Url,
    Authority,
    Scheme as UrlScheme,
    Path as UrlPath,
    Query as UrlQuery,
    Fragment as UrlFragment,
};
use Innmind\Immutable\Str;

final class UrlResolver implements Resolver
{
    /** @var list<string> */
    private array $schemes;

    /**
     * @no-named-arguments
     */
    public function __construct(string ...$schemes)
    {
        $this->schemes = $schemes;
    }

    public function __invoke(Url $origin, Url $destination): Url
    {
        $resolved = $this->resolve($origin, $destination);

        if (!$resolved->authority()->equals(Authority::none()) && $resolved->scheme()->equals(UrlScheme::none())) {
            $resolved = $resolved->withScheme(
                UrlScheme::of($this->schemes[0] ?? 'http'),
            );
        }

        return $resolved;
    }

    private function resolve(Url $origin, Url $destination): Url
    {
        if (!$destination->authority()->equals(Authority::none())) {
            return $destination;
        }

        if (!$destination->path()->equals(UrlPath::none()) && $destination->path()->absolute()) {
            return $origin
                ->withPath($destination->path())
                ->withQuery($destination->query())
                ->withFragment($destination->fragment());
        }

        if ($destination->path()->equals(UrlPath::none()) && !$destination->query()->equals(UrlQuery::none())) {
            return $origin
                ->withQuery($destination->query())
                ->withFragment($destination->fragment());
        }

        if ($destination->path()->equals(UrlPath::none()) && !$destination->fragment()->equals(UrlFragment::none())) {
            return $origin->withFragment($destination->fragment());
        }

        $destinationPath = Str::of($destination->path()->toString());
        $originPath = $origin->path();

        if (!$originPath->directory()) {
            $originPath = $this->up($originPath);
        }

        if ($destinationPath->startsWith('./')) {
            $destinationPath = $destinationPath->substring(2);
        }

        if ($destinationPath->startsWith('../')) {
            $destinationPath = $destinationPath->substring(3);
            $originPath = $this->up($originPath);
        }

        if ($destinationPath->empty()) {
            return $origin
                ->withPath($originPath)
                ->withQuery($destination->query())
                ->withFragment($destination->fragment());
        }

        return $origin
            ->withPath($originPath->resolve(UrlPath::of($destinationPath->toString())))
            ->withQuery($destination->query())
            ->withFragment($destination->fragment());
    }

    private function up(UrlPath $path): UrlPath
    {
        $path = $path->toString();
        $up = \dirname($path);

        return UrlPath::of(\rtrim($up, '/').'/');
    }
}
