<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SensorController extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function add_measures(): void
    {
        $response = $this->post('/api/sensors/aboba/', '');
        $response->assertStatus(404);
    }
}
