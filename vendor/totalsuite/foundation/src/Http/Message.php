<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Http;
! defined( 'ABSPATH' ) && exit();



use InvalidArgumentException;
use TotalRatingVendors\Psr\Http\Message\MessageInterface;
use TotalRatingVendors\Psr\Http\Message\StreamInterface;

/**
 * Class Message
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Http
 */
abstract class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $protocolVersion = '1.1';

    /**
     * @var Headers
     */
    protected $headers;

    /**
     * @var StreamInterface
     */
    protected $body;

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     *
     * @return MessageInterface|Message
     */
    public function withProtocolVersion($version): MessageInterface
    {
        static $validProtocolVersions = ['1.0', '1.1', '2.0', '2'];

        if (!in_array($version, $validProtocolVersions, true)) {
            throw new InvalidArgumentException(
                'Invalid HTTP version. Must be one of: ' . implode(', ', $validProtocolVersions)
            );
        }

        $clone                  = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers->all();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasHeader($name): bool
    {
        return $this->headers->has($name);
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getHeader($name): array
    {
        return $this->headers->get($name, []);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getHeaderLine($name): string
    {
        return implode(',', $this->headers->get($name, []));
    }

    /**
     * @param string          $name
     * @param string|string[] $value
     *
     * @return Message
     */
    public function withHeader($name, $value): Message
    {
        $clone = clone $this;
        $clone->headers->set($name, $value);

        return $clone;
    }

    /**
     * @param string          $name
     * @param string|string[] $value
     *
     * @return Message
     */
    public function withAddedHeader($name, $value): Message
    {
        $clone = clone $this;
        $clone->headers->add($name, $value);

        return $clone;
    }

    /**
     * @param string $name
     *
     * @return MessageInterface
     */
    public function withoutHeader($name): MessageInterface
    {
        $clone = clone $this;
        $clone->headers->remove($name);

        return $clone;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     *
     * @return MessageInterface
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        $clone       = clone $this;
        $clone->body = $body;

        return $clone;
    }
}