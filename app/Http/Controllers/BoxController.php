<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\BoxGroups;
use App\Models\Group;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Http\Response;
use phpDocumentor\Reflection\Types\Boolean;

class BoxController extends Controller
{
    protected $redirectTo = "/";

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function newBox($fields = array())
    {
        $box = new Box();
        return view('boxes.new', [
//            'fields' => $fields,
            'contentTypes' => $box->getContentTypes(),
            'groups' => Group::whereUser(Auth::user()->id)->orderBy('name')->get(),
            'boxExpireOptions' => $box->getExpireOptions()
        ]);
    }

    public function editBox(Request $request)
    {
        $boxQuery = Box::where('user', '=', Auth::user()->id)
            ->whereId($request->id)
            ->get();

        if($boxQuery->count()>0){
            $box = $boxQuery->first();

            $boxGroup = BoxGroups::whereBox($box->id)->first();
            if(empty($boxGroup)){
                $groupId = null;
            }else{
                $groupId = $boxGroup->group;
            }

            $fields = array(
                'id' => $box->id,
                'uri' => $box->uri,
                'content' => $box->content,
                'contentType' => $box->content_type,
                'expiresAt' => $box->expires_at,
                'contentTypes' => $box->getContentTypes(),
                'groups' => Group::whereUser(Auth::user()->id)->orderBy('name')->get(),
                'groupId' => $groupId,
                'boxExpireOptions' => $box->getExpireOptions()
            );
            return view('boxes.new', $fields);
        }
        flash('Box not found')->error();
        return redirect()->route('listBox');

    }

    public function saveBox(Request $request)
    {
        if(empty($request->input('id'))){
            $box = new Box();
        }else{
            $box = Box::where('user', '=', Auth::user()->id)
                ->where('id', '=', $request->id)->first();
//            $box->uri = $request->input('uri');
//            $box->content = $request->input('content');
//            $box->content_type = $request->input('content_type');
//            $box->updated_at = date("Y-m-d H:i:s");
//            $box->setExpirationDate($request->input('expires_at'));
        }
        if($box->validate( $request->input(), !empty($request->input('id')) )){
            $box->save();
        }else{
            $fields = array(
                'uri' => $request->input('uri'),
                'content' => $request->input('content'),
                'contentType' => $request->input('content_type'),
                'expiresAt' => $request->input('expires_at'),
                'contentTypes' => $box->getContentTypes()
            );
            if(!empty($request->input('id'))){
                $fields['id'] = $request->input('id');
            }
            flash('Something went wrong and I could not save')->error();
            return view('boxes.new', $fields);
        }
        if(empty($request->input('id'))){
            flash('Box created')->success();
        }else{
            flash('Box changes saved')->success();
        }

        return redirect()->route('listBox');
    }

    public function listBox(Request $request)
    {
        $boxes = Box::where('user', '=', Auth::user()->id)
            ->orderBy('created_at', 'desc')->get();

        if($boxes->count() === 0){
            return redirect()->route('newBox');
        }

        $groups = Group::whereUser(Auth::user()->id)
            ->orderBy('name')->get();
        $groupsNames = [];
        foreach($groups as $group){
            $groupsNames[$group->id] = $group->name;
        }

        $boxGroups = BoxGroups::whereUser(Auth::user()->id)
            ->get();
        $boxGroupsList = [];
        foreach($boxGroups as $boxGroup){
            $boxGroupsList[$boxGroup->box] = $boxGroup->group;
        }
        return view('boxes.list', [
           'boxes' => $boxes,
            'groupsNames' => $groupsNames,
            'boxGroupsList' => $boxGroupsList
        ]);
    }

    public function deleteBox(Request $request)
    {
        $deleteResult = Box::where('user', '=', Auth::user()->id)
            ->where('id', '=', $request->id)->delete();

        flash('Box deleted')->success();
        return redirect()->route('listBox');
    }

    public function addGroup(Request $request)
    {
        if(Auth::user() && !empty($request->input('groupName'))){
            $group = new Group();
            $group->name = $request->input('groupName');
            $group->color = '';
            $group->user = Auth::user()->id;
            $group->save();
        }
        flash('Group added')->success();
        return redirect()->route('listBox');
    }

    public function deleteGroup(Request $request)
    {
        if(Auth::user() && !empty($request->groupId)){
            $deleteBoxGroups = BoxGroups::whereUser(Auth::user()->id)
                ->whereGroup($request->groupId)
                ->delete();
            $deleteGroup = Group::whereUser(Auth::user()->id)
                ->whereId($request->groupId)
                ->delete();
        }
        flash('Group deleted')->success();
        return redirect()->route('listBox');
    }

    public function addBoxToGroup(Request $request)
    {
        if(Auth::user()
            && !empty($request->input('group'))
            && !empty($request->input('box')))
        {
            BoxGroups::whereUser(Auth::user()->id)
                ->whereBox($request->input('box'))
                ->delete();

            if($request->input('group') != 'no-group'){
                $boxGroup = new BoxGroups();
                $boxGroup->box = $request->input('box');
                $boxGroup->group = $request->input('group');
                $boxGroup->user = Auth::user()->id;
                $boxGroup->save();
            }

            return $this->returnResponse(true);
        }
        return $this->returnResponse(false);
    }

    public function checkWord(Request $request)
    {
        if(Auth::user()){
            $box = Box::where('uri', '=', $request->word)->get();
            if($box->count()==0){
                return $this->returnResponse(true);
            }
        }
        return $this->returnResponse(false);
    }

    public function returnResponse($value = false)
    {
        if($value === true){
            return (new Response(json_encode([true]), 200, ['Content-Type' => 'application/vnd.api+json']));
        }

        return (new Response(json_encode([false]), 400, ['Content-Type' => 'application/vnd.api+json']));
    }

}
