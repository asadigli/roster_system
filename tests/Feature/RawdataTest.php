<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;

use DB;

class RawdataTest extends TestCase
{

    public function test_rawdatas_insert(): void
    {   
        $array = [
            [
                "date" => "15-01-2022",
                "crew_fullname" => "Jan de Bosman",
                "rev_col" => null,
                "dc" => null,
                "check_in_local" => "06:00",
                "check_out_local" => "05:00",
                "check_in_zulu" => "18:00",
                "check_out_zulu" => "17:00",
                "activity" => "SBY",
                "remark" => "SBY",
                "from_location" => "KRP",
                "to_location" => "KRP",
                "token" => "f7210fb77ba3f04b3c931e3593c5b2b2",
                "departure_time_local" => "06:00",
                "arrival_time_local" => "18:00",
                "departure_time_zulu" => "05:00",
                "arrival_time_zulu" => "17:00",
                "ac_hotel" => null,
                "blh" => "00:00",
                "flight_time" => null,
                "night_time" => null,
                "dur" => "03:00",
                "ext" => null,
                "pax_booked" => null,
            ],
            [
                "date" => "18-01-2022",
                "crew_fullname" => "Jan de Bosman",
                "rev_col" => null,
                "dc" => null,
                "check_in_local" => null,
                "check_out_local" => null,
                "check_in_zulu" => null,
                "check_out_zulu" => null,
                "activity" => "OFF",
                "remark" => "OFF",
                "from_location" => "KRP",
                "to_location" => "KRP",
                "token" => "b0120805897b09b2edf3c36bbb3f017c",
                "departure_time_local" => "00:00",
                "arrival_time_local" => "24:00",
                "departure_time_zulu" => "23:00",
                "arrival_time_zulu" => "23:00",
                "ac_hotel" => null,
                "blh" => null,
                "flight_time" => null,
                "night_time" => null,
                "dur" => null,
                "ext" => null,
                "pax_booked" => null
            ]
        ];
        DB::table("rawdatas")->upsert(
            $array,
            ["token"], 
            [
                "date","crew_fullname","rev_col","dc",
                "check_in_local","check_out_local","check_in_zulu",
                "check_out_zulu","activity","remark","from_location",
                "to_location","departure_time_local","arrival_time_local",
                "departure_time_zulu","arrival_time_zulu","ac_hotel","blh",
                "flight_time","night_time","dur","ext","pax_booked"
            ]
        );

        $response = $this->get('/api/rawdatas');

        $response->assertStatus(200);


        // $response->assertStatus(201);
    }
}
