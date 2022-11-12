<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use App\Helpers\ChannelChecker;
use App\Helpers\SmsUtility;

class MessageController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "channel"=>"required",
            "number"=>"required|numeric|digits:10",
            "message"=>"required|max:160"
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }
        $channel = ChannelChecker::check($request->input('channel'));
        if( ! $channel){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid Channel.'
            ]);
        }
        if($channel->balance->balance < 1){
            return response()->json([
                'success'=>false,
                'message'=>'Insufficient balance.'
            ]);
        }


        $api = (new SmsUtility)->get_api();
        if(!$api){
            return response()->json([
                'success'=>false,
                'message'=>'Internal server error.'
            ]);
        }
        $res = (new SmsUtility)->send_sms($request->input('number'), $request->input('message'));

        if(str_contains($res->body(), $api->validator)){
            $success = true;
            (new SmsUtility)->reset_attempt($api);
        } else {
            $success = false;
            (new SmsUtility)->retryOrChange();
        }

        SmsUtility::log_message($request->input('number'), $request->input('message'), $channel, $api, $success);

        return response()->json([
            'success'=>$success,
            'response'=>$res->body()
        ]);


        return $request->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
