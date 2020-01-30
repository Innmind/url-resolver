<?php
declare(strict_types = 1);

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\DestinationUrlCannotBeResolved;
use Innmind\Url\{
    Url,
    Authority,
    Scheme as UrlScheme,
    Path as UrlPath,
};

final class UrlResolver implements Resolver
{
    /** @var list<string> */
    private array $schemes;

    public function __construct(string ...$schemes)
    {
        $this->schemes = $schemes;
    }

    public function __invoke(Url $origin, Url $destination): Url
    {
        if (!$destination->authority()->equals(Authority::none())) {
            if ($destination->scheme()->equals(UrlScheme::none())) {
                $destination = $destination->withScheme(
                    UrlScheme::of($this->schemes[0] ?? 'http'),
                );
            }

            return $destination;
        }

        if (!$destination->path()->equals(UrlPath::none()) && $destination->path()->absolute()) {
            return $origin
                ->withPath($destination->path())
                ->withQuery($destination->query())
                ->withFragment($destination->fragment());
        }

        $destination = $this->createUrl($destination->toString());
        $origin = $this->createUrl($origin->toString());

        switch (true) {
            case $destination->queryString():
                return $origin->withQueryString(
                    new QueryString($destination->toString()),
                )->toUrl();

            case $destination->fragment():
                return $origin->withFragment(
                    new Fragment($destination->toString()),
                )->toUrl();

            case $destination->relativePath():
                $originFolder = Url::of($origin->toString())->path()->toString();

                return $origin->withPath(
                    (new Path($originFolder))
                        ->pointingTo(new RelativePath($destination->toString())),
                )->toUrl();
        }

        throw new DestinationUrlCannotBeResolved($destination->toString());
    }

    /**
     * Create a Url object from the given string
     */
    private function createUrl(string $url): UrlRepresentation
    {
        $url = new UrlRepresentation($url);

        if ($url->schemeLess()) {
            $url = $url->appendScheme(
                new Scheme($this->schemes[0] ?? 'http')
            );
        }

        return $url;
    }
}
