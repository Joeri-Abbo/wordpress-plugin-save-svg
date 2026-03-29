<?php

require_once dirname(__DIR__) . '/lib/vendor/autoload.php';

// WordPress function stubs — must be defined before safe-svg.php is loaded
function add_filter() {}
function add_action() {}
function apply_filters( $tag, $value ) { return $value; }
function get_post_mime_type() { return ''; }
function get_attached_file() { return ''; }
function get_option() { return false; }
function wp_enqueue_style() {}
function plugins_url() { return ''; }

// safe-svg.php requires the includes and defines safeSvg class
// It also calls `new safeSvg()` at the bottom; that's fine with the stubs above
require_once dirname(__DIR__) . '/safe-svg.php';
