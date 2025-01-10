<?php

namespace App\Helpers\LeadStatus;

use App\Models\Leads;
use App\Models\Enums\Leads\Status;

class LeadStatus
{
    public static function ChangeLeadStatus($task)
    {
        if ($task->lead_id) {
            $lead = Leads::with('lazytasks')->with('lazycons')->with('lazyphone')->find($task->lead_id);
            if (!$lead->lazytasks->count()) {
                $lead->status = Status::Lazy->value;
                $lead->save();
            } 
            else{
                if (!$lead->lazycons->count()) {
                    $lead->status = Status::In_Working->value;
                    $lead->save();
                }
                if (!$lead->lazyphone->count()) {
                    $lead->status = Status::In_Working->value;
                    $lead->save();
                }
            }
        } 
    }
}
