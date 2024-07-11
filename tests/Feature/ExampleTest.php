<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $this->get('/items/A/add-to-cart?quantity=2');
        $this->assertNotEmpty(session()->all());
        $this->get('/items/A/add-to-cart?quantity=2');
        dump(session()->all());
    }
}
