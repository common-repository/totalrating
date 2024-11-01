<?php

namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Models\Rating;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class ExportCsv
 *
 * @package TotalRating\Tasks\Rating
 * @method static Response invoke(array $ratings)
 * @method static Response invokeWithFallback($fallback, array $ratings)
 */
class ExportCsv extends Task
{
    /**
     * @var Rating[]
     */
    protected $ratings;

    /**
     * constructor.
     *
     * @param  Rating[]  $ratings
     */
    public function __construct(array $ratings)
    {
        $this->ratings = $ratings;
    }

    /**
     * @inheritDoc
     */
    protected function validate()
    {
        return true;
    }

    /**
     * @return Response
     * @throws Exception
     */
    protected function execute()
    {
        $lines = ['sep=;'];

        $headers = [
            'id'         => esc_html__('ID', 'totalrating'),
            'user'       => esc_html__('User', 'totalrating'),
            'entityType' => esc_html__('Entity type', 'totalrating'),
            'entityName' => esc_html__('Entity name', 'totalrating'),
            'entityUrl'  => esc_html__('Entity URL', 'totalrating'),
            'attribute'  => esc_html__('Attribute', 'totalrating'),
            'point'      => esc_html__('Point', 'totalrating'),
            'value'      => esc_html__('Value', 'totalrating'),
            'comment'    => esc_html__('Comment', 'totalrating'),
            'created_at' => esc_html__('Created at', 'totalrating'),
            'status'     => esc_html__('Status', 'totalrating'),
            'ip'         => esc_html__('IP', 'totalrating'),
            'agent'      => esc_html__('User agent', 'totalrating'),
            'context'    => esc_html__('Context', 'totalrating'),
        ];

        $lines[] = implode('; ', $headers);

        foreach ($this->ratings as $rating) {
            $export = [];

            foreach ($rating->toExport('csv') as $key => $value) {
                $export[$key] = str_replace(';', ' ', $value);
            }

            $lines[] = implode('; ', $export);
        }

        $content = '';

        foreach ($lines as $line) {
            $content .= $line.PHP_EOL;
        }

        return ResponseFactory::file($content, 'ratings-export-'.date('Y-m-d').'.csv', 'text/csv');
    }
}
