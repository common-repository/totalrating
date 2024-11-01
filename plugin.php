<?php
! defined( 'ABSPATH' ) && exit();


/*
 * Plugin Name: TotalRating Pro
 * Plugin URI: https://totalsuite.net/products/totalrating/
 * Description: TotalRating is a powerful WordPress plugin to create, integrate, and analyze ratings using highly customizable rating widgets. Convert collected ratings into valuable actions.
 * Version: 1.8.5
 * Author: TotalSuite
 * Author URI: https://totalsuite.net/
 * Text Domain: totalrating
 * Domain Path: languages
 * Requires at least: 4.8
 * Requires PHP: 7.0
 * Tested up to: 6.5.5
 */

// Environment
use TotalRating\Plugin;

$env = require 'environment.php';

// Bootstrap
$plugin = Plugin::instance($env);
