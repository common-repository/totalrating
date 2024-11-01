<?php


namespace TotalRating\Tasks\Rating;
! defined( 'ABSPATH' ) && exit();



use TotalRating\Models\Rating;
use TotalRatingVendors\TotalSuite\Foundation\Exceptions\Exception;
use TotalRatingVendors\TotalSuite\Foundation\Http\Response;
use TotalRatingVendors\TotalSuite\Foundation\Http\ResponseFactory;
use TotalRatingVendors\TotalSuite\Foundation\Task;

/**
 * Class ExportJson
 *
 * @package TotalRating\Tasks\Rating
 * @method static Response invoke(array $ratings)
 * @method static Response invokeWithFallback($fallback, array $ratings)
 */
class ExportJson extends Task
{
    /**
     * @var Rating[]
     */
    protected $ratings;

    /**
     * constructor.
     *
     * @param Rating[] $ratings
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
        $exportable = [];

        foreach ($this->ratings as $rating) {
            $exportable[] = $rating->toExport('json');
        }

        return ResponseFactory::file($exportable, 'ratings-export-' . date('Y-d-m') . '.json', 'text/json');
    }
}
