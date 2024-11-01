<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Http;
! defined( 'ABSPATH' ) && exit();



use InvalidArgumentException;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Support\Arrayable;

class Cookie implements Arrayable
{
    const SAMESITE_NONE = 'none';
    const SAMESITE_LAX = 'lax';
    const SAMESITE_STRICT = 'strict';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $value = '';

    /**
     * @var int
     */
    protected $expires = 0;

    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var string
     */
    protected $domain = '';

    /**+
     * @var bool
     */
    protected $secure = false;

    /**
     * @var bool
     */
    protected $httpOnly = false;

    /**
     * @var string
     */
    protected $sameSite = 'lax';

    /**
     * Cookie constructor.
     *
     * @param string $name
     * @param string $value
     * @param int    $expires
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httpOnly
     * @param string $sameSite
     */
    public function __construct(
        $name,
        $value,
        $expires = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $httpOnly = false,
        $sameSite = 'lax'
    ) {
        $this->setName($name);
        $this->value    = $value;
        $this->expires  = $expires;
        $this->path     = $path;
        $this->domain   = $domain;
        $this->secure   = $secure;
        $this->httpOnly = $httpOnly;
        $this->setSameSite($sameSite);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        if (preg_match("/[=,; \t\r\n\013\014]/", $name)) {
            throw new InvalidArgumentException(
                sprintf('The cookie name "%s" contains invalid characters.', $name)
            );
        }

        if (empty($name)) {
            throw new InvalidArgumentException('The cookie name cannot be empty.');
        }

        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return Cookie
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return int
     */
    public function getExpires(): int
    {
        return $this->expires;
    }

    /**
     * @param int $expires
     *
     * @return Cookie
     */
    public function setExpires(int $expires)
    {
        $this->expires = $expires;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return Cookie
     */
    public function setPath(string $path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     *
     * @return Cookie
     */
    public function setDomain(string $domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * @param bool $secure
     *
     * @return Cookie
     */
    public function setSecure(bool $secure)
    {
        $this->secure = $secure;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * @param bool $httpOnly
     *
     * @return Cookie
     */
    public function setHttpOnly(bool $httpOnly)
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }

    /**
     * @return string
     */
    public function getSameSite(): string
    {
        return $this->sameSite;
    }

    /**
     * @param string $sameSite
     *
     * @return Cookie
     */
    public function setSameSite(string $sameSite)
    {
        if (!in_array($sameSite, ['', 'strict', 'lax'], true)) {
            throw new InvalidArgumentException('The cookie name cannot be empty.');
        }
        $this->sameSite = $sameSite;
        return $this;
    }

    /**
     * @return $this
     */
    public function forget()
    {
        $this->setExpires(-2628000);
        return $this;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toHeader();
    }

    /**
     * Convert to `Set-Cookie` header
     *
     * @return string
     */
    public function toHeader(): string
    {
        $cookieHeader = $this->name . '=' . urlencode($this->value);

        if ($this->expires !== 0) {
            $cookieHeader .= '; expires=' . gmdate('D, d M Y H:i:s T', $this->expires);
        }

        if (!empty($this->path)) {
            $cookieHeader .= '; path=' . $this->path;
        }

        if (!empty($this->domain)) {
            $cookieHeader .= '; domain=' . $this->domain;
        }

        if ($this->secure) {
            $cookieHeader .= '; secure';
        }

        if ($this->httpOnly) {
            $cookieHeader .= '; httponly';
        }

        if (in_array($this->sameSite, ['lax', 'strinc'], true)) {
            $cookieHeader .= '; sameSite=' . $this->samesite;
        }

        return $cookieHeader;
    }

    /**
     * @return void
     */
    public function send()
    {
        header('Set-Cookie:' . $this->toHeader(), false);
    }
}