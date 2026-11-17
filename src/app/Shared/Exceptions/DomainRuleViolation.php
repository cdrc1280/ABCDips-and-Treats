<?php

namespace App\Shared\Exceptions;

use RuntimeException;

/**
 * A business rule blocked the operation. Rendered in bootstrap/app.php as an error toast
 * + back() for Inertia requests, and as a 422 JSON message for JSON requests.
 */
final class DomainRuleViolation extends RuntimeException {}
