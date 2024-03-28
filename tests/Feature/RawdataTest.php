<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;

class RawdataTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_rawdatas(): void
    {
        $response = $this->get('/api/rawdatas');

        $response->assertStatus(TRUE);
    }

    public function test_rawdatas_action(): void
    {
        $htmlFile = UploadedFile::fake()->create(public_path("sample.html"), 100); // Create a fake HTML file
        $tableId = 'ctl00_Main_activity_table'; 
        $crewName = 'Jan de Bosman';

        $response = $this->post('/api/rawdatas', [
            'file' => $htmlFile,
            'table_id' => $tableId,
            'crew_fullname' => $crewName,
        ]);


        $response->assertStatus(201);
    }
}
