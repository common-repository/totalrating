<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Http;
! defined( 'ABSPATH' ) && exit();



use TotalRatingVendors\TotalSuite\Foundation\Support\Collection;

/**
 * Class ServerContext
 *
 * @package TotalRatingVendors\TotalSuite\Foundation\Http
 */
class ServerContext extends Collection
{
    /**
     * @param array $settings
     *
     * @return Collection|static
     */
    public static function create(array $settings = [])
    {
        //Validates if default protocol is HTTPS to set default port 443
        if ((isset($settings['HTTPS']) && $settings['HTTPS'] !== 'off') || ((isset($settings['REQUEST_SCHEME']) && $settings['REQUEST_SCHEME'] === 'https'))) {
            $defscheme = 'https';
            $defport   = 443;
        } else {
            $defscheme = 'http';
            $defport   = 80;
        }

        $data = array_merge(
            [
                'SERVER_PROTOCOL'      => 'HTTP/1.1',
                'REQUEST_METHOD'       => 'GET',
                'REQUEST_SCHEME'       => $defscheme,
                'SCRIPT_NAME'          => '',
                'REQUEST_URI'          => '',
                'QUERY_STRING'         => '',
                'SERVER_NAME'          => 'localhost',
                'SERVER_PORT'          => $defport,
                'HTTP_HOST'            => 'localhost',
                'HTTP_ACCEPT'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8',
                'HTTP_ACCEPT_CHARSET'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.3',
                'HTTP_USER_AGENT'      => 'TotalSuite',
                'REMOTE_ADDR'          => '127.0.0.1',
                'REQUEST_TIME'         => time(),
                'REQUEST_TIME_FLOAT'   => microtime(true),
            ],
            $settings
        );

        return new static($data);
    }


}