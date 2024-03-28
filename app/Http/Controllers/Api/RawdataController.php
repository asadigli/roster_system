<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Rawdata;

use PHPHtmlParser\Dom;
use DB;
use DateTime;

class RawdataController extends Controller
{
    
   
    public function index(Request $request)
    {
        $current_date = "2022-01-14";

        $from_location  = $request->get("from_location");
        $crew_name      = $request->get("crew_name");
        $next_week      = $show_only_standby = "";
        if($request->get("next_week") === "on") {
            $date = new DateTime($current_date);
            $date->modify('next monday');
            $next_week = $date->format('d-m-Y');
        }

        if($request->get("show_only_standby") === "on") {
            $show_only_standby = "SBY";
        }

        $rowdataQuery = Rawdata::where("crew_fullname",$crew_name);
        if ($next_week) {
            $rowdataQuery->where("date",">=", $next_week);
        }
        
        if ($from_location) {
            $rowdataQuery->where("from_location", $from_location);
        }

        if ($show_only_standby) {
            $rowdataQuery->where("activity", $show_only_standby);
        }

        $rowdata = $rowdataQuery->orderBy('date', 'asc')->get();
        return response()->json($rowdata, 200);
    }

    public function store(Request $request)
    {
 

        if(!$request->hasFile('file')) {
            return response()->json(["message" => "upload_file_not_found"], 400);
        }
        $file = $request->file("file");
        if(!$file->isValid()) {
            return response()->json(["message" => "invalid_file_upload"], 400);
        }
        $path = public_path("uploads/files/");
        $file->move($path, $file->getClientOriginalName());
        $file           = $request->file("file");
        $filename       = $file->getClientOriginalName();

        $extension      = $file->getClientOriginalExtension();

        if(!in_array($extension,["html"])) {
            return response()->json(["message" => "unsupported_file_format"], 400);
        }
        $html_table_id  = $request->post("table_id");
        $crew_name      = $request->post("crew_name");

        if(!$html_table_id) {
            return response()->json(["message" => "id_attribute_required"], 400);
        }

        if(!$crew_name) {
            return response()->json(["message" => "crew_name_required"], 400);
        }

        $array = [];

        /*
            HTML file parsing
        */
        if($extension === "html") {
            
            $dom = new Dom;
            $dom->loadFromFile(public_path("uploads/files/" . $filename));

            // Removing temp file after parsing data
            unlink(public_path("uploads/files/" . $filename));
            $contents = $dom->find("#" . $html_table_id);

            $tr_elements = $contents->find("tbody")[0]->find("tr");
            $prev_date = "";
            foreach($tr_elements as $key => $tr_element) {
                if(!$key) continue;
                // Setting date to those which are dateless on table and are between two dates
                $prev_date = $tr_element->find("td")[1]->innerhtml ?: $prev_date;
                $sub_array = [
                    "date"                  => $prev_date,
                    "crew_fullname"         => $crew_name,
                    "rev_col"               => $tr_element->find("td")[2]->innerhtml,
                    "dc"                    => $tr_element->find("td")[3]->innerhtml,
                    "check_in_local"        => $tr_element->find("td")[4]->innerhtml,
                    "check_out_local"       => $tr_element->find("td")[5]->innerhtml,
                    "check_in_zulu"         => $tr_element->find("td")[6]->innerhtml,
                    "check_out_zulu"        => $tr_element->find("td")[7]->innerhtml,
                    "activity"              => $tr_element->find("td")[8]->innerhtml,
                    "remark"                => $tr_element->find("td")[9]->innerhtml,
                    "from_location"         => $tr_element->find("td")[11]->innerhtml,
                    "to_location"           => $tr_element->find("td")[15]->innerhtml,
                    "departure_time_local"  => $tr_element->find("td")[12]->innerhtml,

                    "arrival_time_local"    => $tr_element->find("td")[16]->innerhtml,
                    "departure_time_zulu"   => $tr_element->find("td")[13]->innerhtml,
                    "arrival_time_zulu"     => $tr_element->find("td")[17]->innerhtml,
                    "ac_hotel"              => $tr_element->find("td")[19]->innerhtml,
                    "blh"                   => $tr_element->find("td")[20]->innerhtml,
                    "flight_time"           => $tr_element->find("td")[21]->innerhtml,
                    "night_time"            => $tr_element->find("td")[22]->innerhtml,
                    "dur"                   => $tr_element->find("td")[23]->innerhtml,
                    "ext"                   => $tr_element->find("td")[24]->innerhtml,
                    "pax_booked"            => $tr_element->find("td")[26]->innerhtml
                ];

                foreach($sub_array as $sub_array_key => &$sub_array_item) {
                    /*
                        Cleaning extra spaces from the content
                    */
                    $sub_array_item = trim(preg_replace("/\s|&nbsp;/", ' ',strip_tags($sub_array_item))) ?: NULL;
                    
                    /*
                        Setting a static date (month + year) for date of operation 
                    */
                    if($sub_array_key === "date") {
                        $sub_array_item = preg_replace('/[^0-9.]+/', '', $sub_array_item) . "-01-2022";
                    }


                    /*
                        We manage to convert number format to time format by putting ":" between
                    */
                    if($sub_array_item && str_contains($sub_array_item,":") && in_array($sub_array_key,[
                        "check_in_local",
                        "check_out_local",
                        "check_in_zulu",
                        "check_out_zulu",
                        "arrival_time_local",
                        "departure_time_zulu",
                        "arrival_time_zulu",
                        "arrival_time_zulu",
                        "flight_time"
                    ])) {
                        $sub_array_item = substr((string)$sub_array_item, 0, -2) . ":" . substr((string)$sub_array_item, -2);
                    }
                }
                $sub_array["token"] = md5($sub_array["date"] . $sub_array["check_in_local"] . $sub_array["crew_fullname"]);

                $array[] = $sub_array;
            }
        }

        /* 
            Using the 'Insert Duplicate' feature, we effectively manage and control duplications within our DB table
        */
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

        return response()->json(["message" => "uploaded"], 201);
    }

}
