<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Tasks;
use App\Helpers\LeadStatus\LeadStatus;
use Carbon\Carbon;

class TaskAjaxController extends Controller
{
    public function setstatustask(Request $request)
    {
        if ( !($request->get('status') == 0) ) {
            $id = $request->get('id');
            $task = Tasks::find($id);
            $status = $request->get('status');
            if($status == 'waiting'){$statuscard = 'ожидает';}
            elseif($status=='timeleft'){$statuscard='просрочена';}
            elseif($status=='inwork'){$statuscard='в работе';}
            elseif($status=='finished'){
                $statuscard='выполнена';
                $task -> donetime = Carbon::now();                
            }
            else {$statuscard='код не сработал';}
            $task->status = $statuscard;            
            $task->save();

            if ($task->lead_id) { LeadStatus::ChangeLeadStatus($task);} 
            
            return ($statuscard);
        }

        if ( !($request->get('dayofweek') == 0) ) {

            $id = $request->get('id');
            $date = $request->get('date');
            $date2 = date_create($date);

            $dayofweekcalendar = $request->get('dayofweek');
            $dayofweektask = date('w', strtotime($date));
            $day = $dayofweekcalendar - $dayofweektask;
            $newdate = date_add($date2, date_interval_create_from_date_string($day." days"));
            $newdate2 = date_format($newdate, DATE_RFC2822);

            $task = Tasks::find($id);
            $task->date = $newdate;
            $task->save();
        }

        if ( !($request->get('dayofmonth') == 0) ) {

            $id = $request->get('id');
            $date = $request->get('date');
            $date2 = date_create($date);

            $dayofmonth = $request->get('dayofmonth');
            $dayofmonthtask = date('d', strtotime($date));
            $day = $dayofmonth - $dayofmonthtask;
            $newdate = date_add($date2, date_interval_create_from_date_string($day." days"));
            $newdate2 = date_format($newdate, DATE_RFC2822);

            $task = Tasks::find($id);
            $task->date = $newdate;
            $task->save();
            return ($newdate2);
        }

        if( !($request->get('hourofday') == 0) ){
            $id = $request->get('id');
            $hourofday = $request->get('hourofday');
            $newhour = Carbon::now('Europe/London')->startOfDay()->addHours($hourofday);
            $task = Tasks::find($id);
            $task->date = $newhour;
            $task->save();
            return ($newhour);
        }

        if(($request->get('checkedvipolnena') == 'true') ){
            $id = $request->get('id');
            $task = Tasks::find($id);
            $task->status = 'выполнена';
            $task->donetime = Carbon::now();
            $task->new = 0;
            $task->save();

            if ($task->lead_id) { LeadStatus::ChangeLeadStatus($task);} 
            
            $checkedvipolnena = $request->get('checkedvipolnena');
            return ($checkedvipolnena);
        }

        if(($request->get('checkedvipolnena') == 'false') ){
            $id = $request->get('id');
            $task = Tasks::find($id);
            $task->status = 'ожидает';
            $task->save();

            if ($task->lead_id) { LeadStatus::ChangeLeadStatus($task);} 

            $checkedvipolnena = $request->get('checkedvipolnena');
            return ($checkedvipolnena);
        }

        else{};
    }
}
