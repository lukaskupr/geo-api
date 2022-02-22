<?php
declare(strict_types=1);

/**
 * Locale set-up
 */
setlocale(LC_ALL, 'cs_CZ.utf8');
date_default_timezone_set(getenv('TZ') ?: 'Europe/Prague');

/**
 * Composer auto-loader
 */
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Common setup
 */
require_once __DIR__ . DIRECTORY_SEPARATOR . 'bootstrap.php';
