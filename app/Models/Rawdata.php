<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rawdata extends Model
{
    use HasFactory;

    protected $fillable = [
            "date","crew_fullname","dc","check_in_local","check_out_local","check_in_zulu",
                "check_out_zulu","activity","remark","from_location","to_location","departure_time_local",
                    "arrival_time_local","departure_time_zulu","arrival_time_zulu","ac_hotel","blh",
                        "flight_time","night_time","dur","ext","pax_booked"];

}
