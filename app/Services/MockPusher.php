<?php

namespace App\Services;

/**
 * Mock Pusher service for when Pusher credentials are not configured
 * This prevents binding resolution exceptions and allows the application to work
 * without real-time features when Pusher is not properly configured.
 */
class MockPusher
{
    /**
     * Mock trigger method that does nothing
     *
     * @param string $channel
     * @param string $event
     * @param array $data
     * @param string|null $socket_id
     * @return bool
     */
    public function trigger($channel, $event, $data = [], $socket_id = null)
    {
        // Log that pusher is not configured (optional)
        \Log::info('Pusher not configured - Mock trigger called', [
            'channel' => $channel,
            'event' => $event,
            'data' => $data
        ]);

        // Always return true to simulate success
        return true;
    }

    /**
     * Mock any other method that might be called on Pusher
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        \Log::info("Pusher not configured - Mock method '$method' called", [
            'arguments' => $arguments
        ]);

        return true;
    }
}