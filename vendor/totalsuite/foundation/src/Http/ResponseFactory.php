<?php

namespace TotalRatingVendors\TotalSuite\Foundation\Http;
! defined( 'ABSPATH' ) && exit();



class ResponseFactory
{
    /**
     * @param mixed $content
     * @param int $status
     *
     * @return Response
     */
    public static function json($content, $status = 200)
    {
        return (new Response())->withJson(['success' => $status <= 400, 'data' => $content], $status)
                               ->withAddedHeader('Content-Type', 'application/json');
    }

    /**
     * @param mixed $content
     * @param int $status
     * @param array $headers
     *
     * @return Response
     */
    public static function html($content, $status = 200, $headers = [])
    {
        $response = new Response($status);
        $response->write($content);

        foreach ($headers as $name => $value) {
            $response = $response->withAddedHeader($name, $value);
        }

        return $response;
    }

    /**
     * @param mixed $data
     * @param string $filename
     * @param string $type
     *
     * @return Message|Response
     */
    public static function file($data, $filename, $type = 'text/html')
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        return (new Response())->write($data)
                               ->withAddedHeader('Cache-Control', 'public')
                               ->withAddedHeader('Content-Type', $type)
                               ->withAddedHeader('Content-Transfer-Encoding', 'Binary')
                               ->withAddedHeader('Content-Length', strlen($data))
                               ->withAddedHeader('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
    }
}
