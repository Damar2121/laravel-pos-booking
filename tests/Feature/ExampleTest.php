<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that root redirects to Filament admin panel.
     */
    public function test_the_application_redirects_to_admin(): void
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertRedirect('/admin');
    }

    /**
     * Test that admin login page is accessible.
     */
    public function test_admin_login_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }
}
