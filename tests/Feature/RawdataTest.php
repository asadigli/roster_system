<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RawdataTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_rawdatas(): void
    {
        $response = $this->get('/api/rawdatas');

        $response->assertStatus(200);
    }

    public function test_rawdatas_action(): void
    {
        $response = $this->post('/api/rawdatas',[
            
        ]);

        $response->assertStatus(200);
    }
}
