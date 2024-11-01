<?php
namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Actions\Tracking;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Action;
use TotalRatingVendors\TotalSuite\Foundation\Contracts\TrackingStorage;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Options;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;

class TrackFeatures extends Action
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * TrackFeatures constructor.
     */
    public function __construct()
    {
        $this->options = Plugin::get(TrackingStorage::class);
    }


    public function execute()
    {
        $features = $this->options->get('features', []);

        $features[] = [
            'action' => $this->request->getParsedBodyParam('action'),
            'label'  => $this->request->getParsedBodyParam('label'),
            'date'   => date(DATE_ATOM)
        ];

        $this->options->set('features', $features)->save();

        wp_send_json_success();
    }

    public function authorize(): bool
    {
        return true;
    }
}