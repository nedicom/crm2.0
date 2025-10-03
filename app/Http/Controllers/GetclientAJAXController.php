<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GetclientAJAXController extends Controller
{
    /**
     * @param Request $request
     * @return string|null
     */
    function getclient(Request $request)
    {
        if ($request->get('query')) {
            $query = $request->get('query');
            $data = DB::table('clients_models')
                ->where('name', 'LIKE', "%{$query}%")
                ->get();

            $output = '<ul class="list-group">';
            foreach ($data as $row) {
                $output .= '<li class="list-group-item clientList clientAJAX"
                    name="'. $row->name .'" address="'. $row->address .'" consult="'. $row->consult . '" attract="'. $row->attract .'"phone="'. $row->phone .'" value="'. $row->id .'"><a href="#" class="text-decoration-none">'. $row->name .'</a></li>';
            }
            $output .= '</ul>';

            return $output;
        }

        return null;
   }
}
