<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Http;
! defined( 'ABSPATH' ) && exit();


use InvalidArgumentException;
use TotalRatingVendors\Psr\Http\Message\MessageInterface;
use TotalRatingVendors\Psr\Http\Message\ResponseInterface;
use TotalRatingVendors\Psr\Http\Message\StreamInterface;
use TotalRatingVendors\Psr\Http\Message\UriInterface;
use RuntimeException;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\Http\HeadersInterface;

use function function_exists;

/**
 * Response
 *
 * This class represents an HTTP response. It manages
 * the response status, headers, and body
 * according to the PSR-7 standard.
 */
class Response extends Message implements ResponseInterface
{
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;

    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NONAUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;
    const HTTP_ALREADY_REPORTED = 208;
    const HTTP_IM_USED = 226;

    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_UNUSED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENT_REDIRECT = 308;

    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    const HTTP_REQUEST_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_IM_A_TEAPOT = 418;
    const HTTP_MISDIRECTED_REQUEST = 421;
    const HTTP_UNPROCESSABLE_ENTITY = 422;
    const HTTP_LOCKED = 423;
    const HTTP_FAILED_DEPENDENCY = 424;
    const HTTP_UPGRADE_REQUIRED = 426;
    const HTTP_PRECONDITION_REQUIRED = 428;
    const HTTP_TOO_MANY_REQUESTS = 429;
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;
    const HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE = 444;
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;
    const HTTP_CLIENT_CLOSED_REQUEST = 499;

    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES = 506;
    const HTTP_INSUFFICIENT_STORAGE = 507;
    const HTTP_LOOP_DETECTED = 508;
    const HTTP_NOT_EXTENDED = 510;
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;
    const HTTP_NETWORK_CONNECTION_TIMEOUT_ERROR = 599;
    /**
     * EOL characters used for HTTP response.
     *
     * @var string
     */
    const EOL = "\r\n";
    /**
     * Status codes and reason phrases
     *
     * @var array
     */
    protected static $messages = [
        //Informational 1xx
        Response::HTTP_CONTINUE                           => 'Continue',
        Response::HTTP_SWITCHING_PROTOCOLS                => 'Switching Protocols',
        Response::HTTP_PROCESSING                         => 'Processing',
        //Successful 2xx
        Response::HTTP_OK                                 => 'OK',
        Response::HTTP_CREATED                            => 'Created',
        Response::HTTP_ACCEPTED                           => 'Accepted',
        Response::HTTP_NONAUTHORITATIVE_INFORMATION       => 'Non-Authoritative Information',
        Response::HTTP_NO_CONTENT                         => 'No Content',
        Response::HTTP_RESET_CONTENT                      => 'Reset Content',
        Response::HTTP_PARTIAL_CONTENT                    => 'Partial Content',
        Response::HTTP_MULTI_STATUS                       => 'Multi-Status',
        Response::HTTP_ALREADY_REPORTED                   => 'Already Reported',
        Response::HTTP_IM_USED                            => 'IM Used',
        //Redirection 3xx
        Response::HTTP_MULTIPLE_CHOICES                   => 'Multiple Choices',
        Response::HTTP_MOVED_PERMANENTLY                  => 'Moved Permanently',
        Response::HTTP_FOUND                              => 'Found',
        Response::HTTP_SEE_OTHER                          => 'See Other',
        Response::HTTP_NOT_MODIFIED                       => 'Not Modified',
        Response::HTTP_USE_PROXY                          => 'Use Proxy',
        Response::HTTP_UNUSED                             => '(Unused)',
        Response::HTTP_TEMPORARY_REDIRECT                 => 'Temporary Redirect',
        Response::HTTP_PERMANENT_REDIRECT                 => 'Permanent Redirect',
        //Client Error 4xx
        Response::HTTP_BAD_REQUEST                        => 'Bad Request',
        Response::HTTP_UNAUTHORIZED                       => 'Unauthorized',
        Response::HTTP_PAYMENT_REQUIRED                   => 'Payment Required',
        Response::HTTP_FORBIDDEN                          => 'Forbidden',
        Response::HTTP_NOT_FOUND                          => 'Not Found',
        Response::HTTP_METHOD_NOT_ALLOWED                 => 'Method Not Allowed',
        Response::HTTP_NOT_ACCEPTABLE                     => 'Not Acceptable',
        Response::HTTP_PROXY_AUTHENTICATION_REQUIRED      => 'Proxy Authentication Required',
        Response::HTTP_REQUEST_TIMEOUT                    => 'Request Timeout',
        Response::HTTP_CONFLICT                           => 'Conflict',
        Response::HTTP_GONE                               => 'Gone',
        Response::HTTP_LENGTH_REQUIRED                    => 'Length Required',
        Response::HTTP_PRECONDITION_FAILED                => 'Precondition Failed',
        Response::HTTP_REQUEST_ENTITY_TOO_LARGE           => 'Request Entity Too Large',
        Response::HTTP_REQUEST_URI_TOO_LONG               => 'Request-URI Too Long',
        Response::HTTP_UNSUPPORTED_MEDIA_TYPE             => 'Unsupported Media Type',
        Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE    => 'Requested Range Not Satisfiable',
        Response::HTTP_EXPECTATION_FAILED                 => 'Expectation Failed',
        Response::HTTP_IM_A_TEAPOT                        => 'I\'m a teapot',
        Response::HTTP_MISDIRECTED_REQUEST                => 'Misdirected Request',
        Response::HTTP_UNPROCESSABLE_ENTITY               => 'Unprocessable Entity',
        Response::HTTP_LOCKED                             => 'Locked',
        Response::HTTP_FAILED_DEPENDENCY                  => 'Failed Dependency',
        Response::HTTP_UPGRADE_REQUIRED                   => 'Upgrade Required',
        Response::HTTP_PRECONDITION_REQUIRED              => 'Precondition Required',
        Response::HTTP_TOO_MANY_REQUESTS                  => 'Too Many Requests',
        Response::HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE    => 'Request Header Fields Too Large',
        Response::HTTP_CONNECTION_CLOSED_WITHOUT_RESPONSE => 'Connection Closed Without Response',
        Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS      => 'Unavailable For Legal Reasons',
        Response::HTTP_CLIENT_CLOSED_REQUEST              => 'Client Closed Request',
        //Server Error 5xx
        Response::HTTP_INTERNAL_SERVER_ERROR              => 'Internal Server Error',
        Response::HTTP_NOT_IMPLEMENTED                    => 'Not Implemented',
        Response::HTTP_BAD_GATEWAY                        => 'Bad Gateway',
        Response::HTTP_SERVICE_UNAVAILABLE                => 'Service Unavailable',
        Response::HTTP_GATEWAY_TIMEOUT                    => 'Gateway Timeout',
        Response::HTTP_VERSION_NOT_SUPPORTED              => 'HTTP Version Not Supported',
        Response::HTTP_VARIANT_ALSO_NEGOTIATES            => 'Variant Also Negotiates',
        Response::HTTP_INSUFFICIENT_STORAGE               => 'Insufficient Storage',
        Response::HTTP_LOOP_DETECTED                      => 'Loop Detected',
        Response::HTTP_NOT_EXTENDED                       => 'Not Extended',
        Response::HTTP_NETWORK_AUTHENTICATION_REQUIRED    => 'Network Authentication Required',
        Response::HTTP_NETWORK_CONNECTION_TIMEOUT_ERROR   => 'Network Connect Timeout Error',
    ];
    /**
     * Status code
     *
     * @var int
     */
    protected $status = Response::HTTP_OK;
    /**
     * Reason phrase
     *
     * @var string
     */
    protected $reasonPhrase = '';
    /**
     * @var CookieJar
     */
    protected $cookies;

    /**
     * @param int                   $status  The response status code.
     * @param HeadersInterface|null $headers The response headers.
     * @param StreamInterface|null  $body    The response body.
     */
    public function __construct(
        $status = Response::HTTP_OK,
        HeadersInterface $headers = null,
        StreamInterface $body = null
    ) {
        $this->status  = $this->filterStatus($status);
        $this->headers = $headers ?: new Headers();
        $this->body    = $body ?: new Body(fopen('php://temp', 'rb+'));
        $this->cookies = new CookieJar();
    }

    /**
     * Filter HTTP status code.
     *
     * @param int $status HTTP status code.
     *
     * @return int
     *
     * @throws InvalidArgumentException If an invalid HTTP status code is provided.
     */
    protected function filterStatus($status): int
    {
        if (!is_int(
                $status
            ) || $status < self::HTTP_CONTINUE || $status > self::HTTP_NETWORK_CONNECTION_TIMEOUT_ERROR) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        }

        return $status;
    }

    /**
     * This method is applied to the cloned object
     * after PHP performs an initial shallow-copy. This
     * method completes a deep-copy by creating new objects
     * for the cloned object's internal reference pointers.
     */
    public function __clone()
    {
        $this->headers = clone $this->headers;
    }

    /**
     * Write data to the response body.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * Proxies to the underlying stream and writes the provided data to it.
     *
     * @param string $data
     *
     * @return Response
     */
    public function write($data): Response
    {
        $this->getBody()->write($data);

        return $this;
    }

    /**
     * Redirect.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Redirect
     * response to the client.
     *
     * @param string|UriInterface $url    The redirect destination.
     * @param int|null            $status The redirect HTTP status code.
     *
     * @return Response
     */
    public function withRedirect($url, $status = null): Response
    {
        $responseWithRedirect = $this->withHeader('Location', (string)$url);

        if ($status === null && $this->getStatusCode() === self::HTTP_OK) {
            $status = self::HTTP_FOUND;
        }

        if ($status !== null) {
            return $responseWithRedirect->withStatus($status);
        }

        return $responseWithRedirect;
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * If a Location header is set and the status code is 200, then set the status
     * code to 302 to mimic what PHP does. See https://github.com/slimphp/Slim/issues/1730
     *
     * @param string          $name  Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     *
     * @return Response
     *
     * @throws InvalidArgumentException For invalid header names or values.
     */
    public function withHeader($name, $value): Message
    {
        $clone = clone $this;
        $clone->headers->set($name, $value);

        if ($this->body instanceof NonBufferedBody) {
            header(sprintf('%s: %s', $name, $clone->getHeaderLine($name)));
        }

        if ($clone->getStatusCode() === self::HTTP_OK && strtolower($name) === 'location') {
            $clone = $clone->withStatus(self::HTTP_FOUND);
        }

        return $clone;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->status;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int    $code         The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *                             provided status code; if none is provided, implementations MAY
     *                             use the defaults as suggested in the HTTP specification.
     *
     * @return Response
     *
     * @throws InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = ''): Response
    {
        $code = $this->filterStatus($code);

        if (!is_string($reasonPhrase) && !method_exists($reasonPhrase, '__toString')) {
            throw new InvalidArgumentException('ReasonPhrase must be a string');
        }

        $clone         = clone $this;
        $clone->status = $code;
        if ($reasonPhrase === '' && isset(static::$messages[$code])) {
            $reasonPhrase = static::$messages[$code];
        }

        if ($reasonPhrase === '') {
            throw new InvalidArgumentException('ReasonPhrase must be supplied for this code');
        }

        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }

    /**
     * Json.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * This method prepares the response object to return an HTTP Json
     * response to the client.
     *
     * @param mixed $data            The data
     * @param int   $status          The HTTP status code.
     * @param int   $encodingOptions Json encoding options
     *
     * @return Response
     *
     * @throws RuntimeException
     */
    public function withJson($data, $status = null, $encodingOptions = 0): MessageInterface
    {
        $response = $this->withBody(new Body(fopen('php://temp', 'rb+')));
        $response->getBody()->write($json = json_encode($data, $encodingOptions));

        // Ensure that the json encoding passed successfully
        if ($json === false) {
            throw new RuntimeException(json_last_error_msg(), json_last_error());
        }

        $responseWithJson = $response->withHeader('Content-Type', 'application/json');
        if (isset($status)) {
            return $responseWithJson->withStatus($status);
        }
        return $responseWithJson;
    }

    /**
     * @param        $name
     * @param        $value
     * @param int    $expires
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     * @param string $samesite
     *
     * @return Response
     */
    public function withCookie(
        $name,
        $value,
        $expires = 0,
        $path = '/',
        $domain = '',
        $secure = false,
        $httponly = false,
        $samesite = ''
    ) {
        $clone = clone $this;
        $clone->setCookie($name, $value, $expires, $path, $domain, $secure, $httponly, $samesite);
        return $clone;
    }

    /**
     * @param        $name
     * @param        $value
     * @param int    $expires
     * @param string $path
     * @param string $domain
     * @param bool   $secure
     * @param bool   $httponly
     * @param string $samesite
     *
     * @return Response
     */
    public function setCookie(
        $name,
        $value,
        $expires = 0,
        $path = '/',
        $domain = '',
        $secure = false,
        $httponly = false,
        $samesite = ''
    ) {
        $this->cookies->set($name, $value, $expires, $path, $domain, $secure, $httponly, $samesite);
        return $this;
    }

    /**
     * Is this response empty?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return in_array(
            $this->getStatusCode(),
            [self::HTTP_NO_CONTENT, self::HTTP_RESET_CONTENT, self::HTTP_NOT_MODIFIED]
        );
    }

    /**
     * Is this response informational?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isInformational(): bool
    {
        return $this->getStatusCode() >= self::HTTP_CONTINUE && $this->getStatusCode() < self::HTTP_OK;
    }

    /**
     * Is this response OK?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return $this->getStatusCode() === self::HTTP_OK;
    }

    /**
     * Is this response successful?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->getStatusCode() >= self::HTTP_OK && $this->getStatusCode() < self::HTTP_MULTIPLE_CHOICES;
    }

    /**
     * Is this response a redirect?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return in_array(
            $this->getStatusCode(),
            [
                self::HTTP_MOVED_PERMANENTLY,
                self::HTTP_FOUND,
                self::HTTP_SEE_OTHER,
                self::HTTP_TEMPORARY_REDIRECT,
                self::HTTP_PERMANENT_REDIRECT,
            ]
        );
    }

    /**
     * Is this response a redirection?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isRedirection(): bool
    {
        return $this->getStatusCode() >= self::HTTP_MULTIPLE_CHOICES && $this->getStatusCode() < self::HTTP_BAD_REQUEST;
    }

    /**
     * Is this response forbidden?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isForbidden(): bool
    {
        return $this->getStatusCode() === self::HTTP_FORBIDDEN;
    }

    /**
     * Is this response not Found?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->getStatusCode() === self::HTTP_NOT_FOUND;
    }

    /**
     * Is this a bad request?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isBadRequest(): bool
    {
        return $this->getStatusCode() === self::HTTP_BAD_REQUEST;
    }

    /**
     * Is this response a client error?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->getStatusCode() >= self::HTTP_BAD_REQUEST && $this->getStatusCode(
            ) < self::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * Is this response a server error?
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->getStatusCode() >= self::HTTP_INTERNAL_SERVER_ERROR && $this->getStatusCode() < 600;
    }

    /**
     * Convert response to string.
     *
     * Note: This method is not part of the PSR-7 standard.
     *
     * @return string
     */
    public function __toString()
    {
        $output = sprintf(
            'HTTP/%s %s %s',
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getReasonPhrase()
        );
        $output .= static::EOL;

        foreach ($this->getHeaders() as $name => $values) {
            $output .= sprintf('%s: %s', $name, $this->getHeaderLine($name)) . static::EOL;
        }

        $output .= static::EOL;
        $output .= $this->getBody();

        return $output;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase(): string
    {
        if ($this->reasonPhrase) {
            return $this->reasonPhrase;
        }
        return static::$messages[$this->status] ?? '';
    }

    public function sendAndExit()
    {
        $this->send();
        exit();
    }

    public function send()
    {
        $this->sendHeaders()->sendBody();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function sendBody()
    {
        echo $this->body;
        return $this;
    }

    /**
     * @return $this
     */
    protected function sendHeaders()
    {
        // headers have already been sent by the developer
        if (headers_sent()) {
            return $this;
        }

        // headers
        header(
            sprintf('HTTP/%s %s %s', $this->protocolVersion, $this->status, $this->reasonPhrase),
            true,
            $this->status
        );

        foreach ($this->getHeaders() as $name => $values) {
            $replace = (0 === strcasecmp($name, 'Content-Type'));

            foreach ($values as $value) {
                header($name . ': ' . $value, $replace, $this->status);
            }
        }

        // cookies
        $this->cookies->send();

        return $this;
    }
}
