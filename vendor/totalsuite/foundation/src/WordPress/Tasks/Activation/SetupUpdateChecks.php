<?php

namespace TotalRatingVendors\TotalSuite\Foundation\WordPress\Tasks\Activation;
! defined( 'ABSPATH' ) && exit();


use TotalRatingVendors\TotalSuite\Foundation\Helpers\Html;
use TotalRatingVendors\TotalSuite\Foundation\License;
use TotalRatingVendors\TotalSuite\Foundation\Task;
use TotalRatingVendors\TotalSuite\Foundation\WordPress\Plugin;

class SetupUpdateChecks extends Task
{
    /**
     * @var string $productId
     */
    protected $productId;

    protected function validate()
    {
        return true;
    }

    protected function execute()
    {
        $this->productId = Plugin::env('product.id');

        // Check Updates
        add_filter('site_transient_update_plugins', [$this, 'checkUpdates']);

        // Update messages
        add_action("in_plugin_update_message-{$this->productId}/plugin.php", [$this, 'updateMessages'], 10, 2);
    }

    /**
     * @param object $plugins
     *
     * @return object
     */
    public function checkUpdates($plugins)
    {
        $pluginPath = "{$this->productId}/plugin.php";

        if (isset($plugins->response[$pluginPath])) {
            $license = License::instance();

            if ($license->hasExpired()) {
                $plugins->response[$pluginPath]->package = null;
            } elseif ($license->isRegistered()) {
                $plugins->response[$pluginPath]->package = $license->getDownloadUrl($this->productId);
            }
        }

        return $plugins;
    }

    /**
     * @param $plugin
     */
    public function updateMessages($plugin)
    {
        $license = License::instance();

        if ($plugin['slug'] === $this->productId) {
            if ($license->isRegistered() && !$license->hasExpired()) {
                printf(' ' .
                    __('You’re receiving %s updates because you have an active license.', Plugin::env('textdomain', 'totalsuite')),
                    $license->getType()
                );
            } elseif ($license->hasExpired()) {
                $link = Html::create('a',
                    [
                        'href' => $license->getRenewalUrl(),
                        'target' => '_blank'
                    ],
                    __('Renew your license.', Plugin::env('textdomain', 'totalsuite')));

                printf(' ' . __('Your license has expired, to receive updates, please %s', Plugin::env('textdomain', 'totalsuite')), $link);
            }
        }
    }

}