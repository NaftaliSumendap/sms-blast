<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SmsHistory;  

class SmsHistoryController extends Controller
{
    public function show($id)
    {
        $history = SmsHistory::findOrFail($id);
        $details = json_decode($history->details, true) ?? [];

        return view('history_detail', compact('history', 'details'));
    }

}
