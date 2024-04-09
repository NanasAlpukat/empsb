<?php

namespace App\Http\Controllers\api;

use App\Exports\HistoriesExport;
use App\Http\Controllers\Controller;
use App\Http\Resources\historyResource;
use App\Models\history;
use App\Models\student;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class historyController extends Controller
{
    public function excelHistory(){
      
        return Excel::download(new HistoriesExport, 'histories.xlsx');
         
    }

    public function showHistory(Student $student){
        $history = history::where('student_id',$student->id)->paginate(10);
        return historyResource::collection($history);
    }

    public function history(History $history){
        $history = history::where('id',$history->id)->first();
        return new historyResource($history);
    }

        public function dropHistory(History $history){
            $history = history::findOrFail($history->id);
    
            try{
                $history->delete();
                return response()->json([
                    'meassage' => 'Data berhasil hapus'
                ],200);
            }catch(QueryException $e){
                return response()->json([
    
                    'message'=>$e->errorInfo
                ],500);
            }
        }  
}
