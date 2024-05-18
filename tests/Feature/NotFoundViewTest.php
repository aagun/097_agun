<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotFoundViewTest extends TestCase
{
    public function testNotFound()
    {
        $this->get('404')
            ->assertSeeText("The page you are looking not found.");

        $this->get('test-not-found')
            ->assertSeeText("The page you are looking not found.");
    }

}
