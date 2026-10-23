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
        $response = $this->get('/')
            ->assertOk()
            ->assertSee('Temukan gaya yang terasa seperti diri sendiri.')
            ->assertSee('Jelajahi katalog');

        $response->assertSee(route('catalog.index'), false);
    }
}
