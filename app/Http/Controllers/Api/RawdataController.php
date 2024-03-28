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
        // $validated = $request->validate([
        //     'crew'          => ['max:100'],
        //     'start_date'    => ['date', 'required'],
        //     'end_date'      => ['date', 'required']
        // ]);
        $current_date = "2022-01-14";
        


        $crew       = $request->get("crew");
        $next_week  = "";
        if($request->get("next_week") === "on") {
            $date = new DateTime($current_date);
            $date->modify('next monday');
            $next_week = $date->format('Y-m-d');
        }

        $rowdata = Rawdata::where("crew_fullname",$crew)
                            ->where("date",">=", $next_week)
                                ->get();
        return response()->json($rowdata);
    }

    public function store(Request $request)
    {
 

        if(!$request->hasFile('file')) {
            return response()->json(['upload_file_not_found'], 400);
        }
        $file = $request->file('file');
        if(!$file->isValid()) {
            return response()->json(['invalid_file_upload'], 400);
        }
        $path = public_path("uploads/files/");
        $file->move($path, $file->getClientOriginalName());
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        $extension = $file->getClientOriginalExtension();
        
        $html_table_id = $request->post("table_id");
        $array = [];
        if($extension === "html") {
            
            $dom = new Dom;
            $dom->loadFromFile(public_path("uploads/files/" . $filename));
            unlink(public_path("uploads/files/" . $filename));
            $contents = $dom->find("#" . $html_table_id);

            $tr_elements = $contents->find("tbody")[0]->find("tr");
            $prev_date = "";
            foreach($tr_elements as $key => $tr_element) {
                if(!$key) continue;
                $prev_date = $tr_element->find("td")[1]->innerhtml ?: $prev_date;
                $sub_array = [
                    "date"                  => $prev_date,
                    "crew_fullname"         => "",
                    "rev_col"               => $tr_element->find("td")[2]->innerhtml,
                    "dc"                    => $tr_element->find("td")[3]->innerhtml,
                    "check_in_local"        => $tr_element->find("td")[4]->innerhtml,
                    "check_out_local"       => $tr_element->find("td")[5]->innerhtml,
                    "check_in_zulu"         => $tr_element->find("td")[6]->innerhtml,
                    "check_out_zulu"        => $tr_element->find("td")[7]->innerhtml,
                    "activity"              => $tr_element->find("td")[8]->innerhtml,
                    "remark"                => $tr_element->find("td")[9]->innerhtml,
                    "from_location"         => $tr_element->find("td")[10]->innerhtml,
                    "to_location"           => $tr_element->find("td")[11]->innerhtml,
                    "departure_time_local"  => $tr_element->find("td")[12]->innerhtml,

                    "arrival_time_local"    => $tr_element->find("td")[13]->innerhtml,
                    "departure_time_zulu"   => $tr_element->find("td")[14]->innerhtml,
                    "arrival_time_zulu"     => $tr_element->find("td")[15]->innerhtml,
                    "ac_hotel"              => $tr_element->find("td")[16]->innerhtml,
                    "blh"                   => $tr_element->find("td")[17]->innerhtml,
                    "flight_time"           => $tr_element->find("td")[18]->innerhtml,
                    "night_time"            => $tr_element->find("td")[19]->innerhtml,
                    "dur"                   => $tr_element->find("td")[20]->innerhtml,
                    "ext"                   => $tr_element->find("td")[21]->innerhtml,
                    "pax_booked"            => $tr_element->find("td")[22]->innerhtml
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

                    if(in_array($sub_array_key,[
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
        $rowdata = DB::table("rawdatas")->upsert(
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
