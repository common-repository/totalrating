<?php

namespace TotalRating\Actions\Ratings;
! defined( 'ABSPATH' ) && exit();


use TotalRating\Capabilities\UserCanExportData;
use TotalRating\Filters\FilterExportRatings;
use TotalRating\Tasks\Rating\ExportCsv;
use TotalRating\Tasks\Rating\ExportJson;
use TotalRating\Tasks\Rating\GetRatings;
use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;

class Export extends Action
{
    /**
     * @param $format
     *
     * @return mixed
     * @throws Exception
     */
    public function execute($format)
    {
        $filters             = $this->request->getParsedBody();
        $filters['per_page'] = null;
        $ratings             = GetRatings::invoke($filters, false)
                                         ->all();

        return $this->export($ratings, $format);
    }

    /**
     * @param array $ratings
     * @param       $format
     *
     * @return mixed
     */
    protected function export(array $ratings, $format)
    {
        $export = FilterExportRatings::apply($format);

        if ($export instanceof Response) {
            return $export;
        }

        switch ($format) {
            case 'json' :
            {
                return ExportJson::invoke($ratings);
            }
            case 'csv' :
            {
                return ExportCsv::invoke($ratings);
            }
            default :
            {
                Exception::throw('Invalid export format');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function authorize(): bool
    {
        return UserCanExportData::check();
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return [
            'format' => [
                'expression'        => '(?<format>([\w]+))',
                'sanitize_callback' => function ($format) {
                    return (string)$format;
                },
            ],
        ];
    }
}
