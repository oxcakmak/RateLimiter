<?php
/**
 *
 * RateLimiter class for enforcing rate limits on user requests.
 *
 * This class utilizes session-based identification to track user requests
 * within a specified time window. It checks for all common request methods
 * (POST, GET, PUT) to prevent circumventing the limit.
 */
class RateLimiter
{
    private string $sessionId;
    private int $limit;
    private int $duration;

    /**
     * RateLimiter constructor.
     *
     * @param int $limit The maximum allowed requests within the duration.
     * @param int $duration The time window (in seconds) for the rate limit.
     * @throws RuntimeException If session handling is not enabled.
     */
    public function __construct(int $limit, int $duration)
    {
        if (session_status() === PHP_SESSION_NONE) {
            throw new RuntimeException('Session handling is not enabled.');
        }

        $this->sessionId = session_id();
        $this->limit = $limit;
        $this->duration = $duration;
    }

    /**
     * Checks if the current request exceeds the defined rate limit.
     *
     * @return bool True if the request is within the limit, false otherwise.
     */
    public function check(): bool
    {
        $key = "rate_limit_" . $this->sessionId;

        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = time(); // Set timestamp for first request
            return true;
        }

        $elapsedTime = time() - $_SESSION[$key];

        if ($elapsedTime >= $this->duration) {
            $_SESSION[$key] = time(); // Reset timestamp for new limit cycle
            return true;
        }

        $remaining = $this->limit - (count($_POST) + count($_GET) + count($_PUT));

        return $remaining > 0;
    }
}

// Example usage
try {
    $limiter = new RateLimiter(5, 60); // Allow 5 requests per minute

    if (!$limiter->check()) {
        http_response_code(429); // Set response code to "Too Many Requests"
        echo "You have exceeded the rate limit. Please try again later.";
        exit; // Stop script execution
    }

    // Your application logic here...

} catch (RuntimeException $e) {
    // Handle session-related errors
    http_response_code(500);
    echo "Internal Server Error: " . $e->getMessage();
    exit;
}
?>
