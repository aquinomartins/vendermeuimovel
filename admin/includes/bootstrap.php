<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/app/helpers/auth.php';
require_once dirname(__DIR__, 2) . '/app/helpers/csrf.php';
require_once dirname(__DIR__, 2) . '/app/helpers/sanitize.php';
require_once dirname(__DIR__, 2) . '/app/helpers/upload.php';
require_once dirname(__DIR__, 2) . '/app/models/Settings.php';
require_once dirname(__DIR__, 2) . '/app/models/Pages.php';
require_once dirname(__DIR__, 2) . '/app/models/Sections.php';
require_once dirname(__DIR__, 2) . '/app/models/Leads.php';

ensure_session_started();
