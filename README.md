# RateLimiter
RateLimiter class for enforcing rate limits on user requests.

## Support Me

This software is developed during my free time and I will be glad if somebody will support me.

Everyone's time should be valuable, so please consider donating.

[https://buymeacoffee.com/oxcakmak](https://buymeacoffee.com/oxcakmak)

### Installation

```php
require_once 'RateLimiter.php';
```

### Usage
```php
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
```
