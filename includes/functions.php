<?php
declare(strict_types=1);

/**
 * Averroes International School CMS
 * Core Application Bootstrap & Functions Gateway
 *
 * This file boots core configurations, security helpers,
 * file upload processors, and data repositories.
 */

// Load Configurations & Database
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

// Load Modular Helpers
require_once __DIR__ . '/helpers/security.php';
require_once __DIR__ . '/helpers/upload.php';
require_once __DIR__ . '/helpers/format.php';

// Load Modular Data Repositories
require_once __DIR__ . '/repositories/SettingRepository.php';
require_once __DIR__ . '/repositories/NavigationRepository.php';
require_once __DIR__ . '/repositories/ContentRepository.php';
require_once __DIR__ . '/repositories/NewsletterRepository.php';
