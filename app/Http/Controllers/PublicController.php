<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Box;

class PublicController extends Controller
{
    public function viewUri(Request $request)
    {
        $viewUri = $request->viewUri;

        $boxQuery = Box::where('uri', '=', $viewUri)
            ->where(function($query){
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', date('Y-m-d H:i:s'));
            });

        if($boxQuery->count()>0){
            $box = $boxQuery->get();

            $content = null;
            if($box->count()>0){
                $thisBox = $box->first();
                $content = $thisBox->content;
                $contentType = $thisBox->content_type;

                if($thisBox->expires_other == "after-open"){
                    $thisBox->expires_at = date("Y-m-d H:i:s");
                    $thisBox->save();
                }
            }

            return view('view-box', [
                'box' => $thisBox,
                'content' => $content,
                'contentType' => $contentType,
                'viewUri' => $viewUri
            ]);

        }

        return redirect('/');
    }

    public function search(Request $request)
    {
        return view('search');
    }
}
