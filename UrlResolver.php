<?php

namespace Innmind\UrlResolver;

use Innmind\UrlResolver\Exception\ResolutionException;
use Innmind\UrlResolver\Exception\UrlException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints\Url;
use Pdp\Parser;
use Pdp\PublicSuffixListManager;
use Pdp\Uri\Url as ParsedUrl;

class UrlResolver implements ResolverInterface
{
    protected $parser;
    protected $validator;
    protected $constraint;

    public function __construct(
        array $protocols,
        Parser $parser = null,
        ValidatorInterface $validator = null
    ) {
        if (empty($protocols)) {
            $protocols = ['http', 'https'];
        }

        if ($parser === null) {
            $parser = new Parser(
                (new PublicSuffixListManager)->getList()
            );
        }

        if ($validator === null) {
            $validator = Validation::createValidator();
        }

        $this->constraint = new Url(['protocols' => $protocols]);
        $this->parser = $parser;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($origin, $destination)
    {
        $origin = (string) $origin;
        $destination = $this->appendProtocol((string) $destination);
        $violations = $this
            ->validator
            ->validate(
                $destination,
                [$this->constraint]
            );

        if ($violations->count() === 0) {
            return $destination;
        }

        $origin = $this->appendProtocol($origin);

        $violations = $this
            ->validator
            ->validate(
                $origin,
                [$this->constraint]
            );

        if ($violations->count() > 0) {
            throw new UrlException(sprintf(
                'The origin variable is not a url (given: %s)',
                $origin
            ));
        }

        switch (true) {
            case substr($destination, 0, 1) === '?':
                return $this->buildQueryString($origin, $destination);
            case substr($destination, 0, 1) === '#':
                return $this->buildFragment($origin, $destination);
            case substr($destination, 0, 1) === '/':
                return $this->buildAbsoluteUrl($origin, $destination);
            case substr($destination, 0, 2) === './':
                $destination = substr($destination, 2);
                if ($destination === false) {
                    $destination = '';
                }
            case substr($destination, 0, 1) !== '/':
            case substr($destination, 0, 3) === '../':
                return $this->buildRelativeUrl($origin, $destination);
        }

        throw new ResolutionException(sprintf(
            'The destination url (%s) can\'t be resolved as a valid url',
            $destination
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function folder($url)
    {
        $this->validateUrl($url);
        $parsed = $this->parser->parseUrl($url);

        $folder = dirname($parsed->path);

        if (substr($folder, -1) !== '/') {
            $folder .= '/';
        }

        return sprintf(
            '%s://%s%s%s',
            $parsed->scheme,
            (string) $parsed->host,
            $this->getPort($parsed),
            $folder
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isFolder($url)
    {
        $this->validateUrl($url);
        $parsed = $this->parser->parseUrl($url);

        return substr($parsed->path, -1) === '/';
    }

    /**
     * {@inheritdoc}
     */
    public function file($url)
    {
        $this->validateUrl($url);
        $parsed = $this->parser->parseUrl($url);

        return sprintf(
            '%s://%s%s%s%s',
            $parsed->scheme,
            (string) $parsed->host,
            $this->getPort($parsed),
            $parsed->path,
            $parsed->query ? '?' . $parsed->query : ''
        );
    }

    /**
     * Return the port as string for the given parsed url
     *
     * @param ParsedUrl $url
     *
     * @return string
     */
    protected function getPort(ParsedUrl $url)
    {
        if ($url->port === null) {
            return '';
        } else if ($url->scheme === 'http' && (int) $url->port === 80) {
            return '';
        } else if ($url->scheme === 'https' && (int) $url->port === 443) {
            return '';
        } else {
            return ':' . $url->port;
        }
    }

    /**
     * Build a url from a relative destination
     *
     * @param string $origin
     * @param string $destination
     *
     * @return string
     */
    protected function buildRelativeUrl($origin, $destination)
    {
        if (!$this->isFolder($origin)) {
            $origin = $this->folder($origin);
        }

        if (substr($destination, 0, 3) === '../') {
            $origin = $this->folder($origin);
            $destination = substr($destination, 3);
        }

        return $origin . $destination;
    }

    /**
     * Resolve a query string
     *
     * @param string $origin
     * @param string $query
     *
     * @return string
     */
    protected function buildQueryString($origin, $query)
    {
        $parsed = $this->parser->parseUrl($origin);

        return sprintf(
            '%s://%s%s%s%s',
            $parsed->scheme,
            (string) $parsed->host,
            $this->getPort($parsed),
            $parsed->path,
            $query
        );
    }

    /**
     * Resolve to a fragment url
     *
     * @param string $origin
     * @param string $fragment
     *
     * @return string
     */
    protected function buildFragment($origin, $fragment)
    {
        $parsed = $this->parser->parseUrl($origin);

        return sprintf(
            '%s://%s%s%s%s%s',
            $parsed->scheme,
            (string) $parsed->host,
            $this->getPort($parsed),
            $parsed->path,
            $parsed->query ? '?' . $parsed->query : '',
            $fragment
        );
    }

    /**
     * Resolve an absolute path
     *
     * @param string $origin
     * @param string $path
     *
     * @return string
     */
    protected function buildAbsoluteUrl($origin, $path)
    {
        $parsed = $this->parser->parseUrl($origin);

        return sprintf(
            '%s://%s%s%s',
            $parsed->scheme,
            (string) $parsed->host,
            $this->getPort($parsed),
            $path
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
    protected function validateUrl($url)
    {
        $violations = $this->validator->validate($url, [$this->constraint]);

        if ($violations->count() > 0) {
            throw new UrlException(sprintf(
                'The string "%s" is not a valid url',
                $url
            ));
        }
    }

    /**
     * Append the first supported protocol when the given string start with "//"
     *
     * @param string $url
     *
     * @return string
     */
    protected function appendProtocol($url)
    {
        if (substr($url, 0, 2) === '//') {
            $url = sprintf(
                '%s:%s',
                $this->constraint->protocols[0],
                $url
            );
        }

        return $url;
    }
}
