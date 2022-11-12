<?php

namespace App\Helpers;

use App\Models\Api;
use App\Models\Message;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class SmsUtility{

    public static function settings($key){
        return Setting::where('key', $key)->first();
    }

    public static function get_api(){
        $current_api = Setting::where('key', 'current_api')->first();
        $api = Api::findOr($current_api->value, fn () => false);
        if(!$api || $api->expired_at !== null){
            return false;
        }
        return $api;
    }

    public static function change_api(){
        $current_api = Setting::where('key', 'current_api')->first();
        $next = intval($current_api->value) + 1;
        $current_api->value = $next;
        $current_api->save();
    }

    public function send_sms($number, $message){
        $api = $this->get_api();
        if(!$api){
            return false;
        }

        $url = str_replace("{number}", $number, $api->url);
        $url = str_replace("{message}", urlencode($message), $url);

        $body = str_replace("{number}", $number, $api->body);
        $body = str_replace("{message}", urlencode($message), $body);

        $data = [
            "body" => $body
        ] ?? [];

        $response = Http::asJson()->withOptions(['verify'=>false])->withHeaders(json_decode($api->headers) ?? [])->send($api->method, $url, $data)->onError(function(){
            return 'Error: API call failed on server.';
        });
        return $response;
    }

    public static function log_message($number, $message, $channel, $api, $success){
        return Message::create([
            'channel_id'=>$channel->id,
            'api_id'=>$api->id,
            'number'=>$number,
            'message'=>$message,
            'delivered'=>$success
        ]);
    }

    public function retryOrChange(){
        $api = $this->get_api();
        if(!$api){
            return false;
        }
        if($api->retry_attempt <= $this->settings('max_retry')->value){
            $api->retry_attempt = $api->retry_attempt + 1;
            $api->save();
        } else {
            $api->expired_at = Carbon::now();
            $api->save();
            $this->change_api();
        }
        return true;
    }

    public function reset_attempt($api){
        $api = Api::find($api->id);
        $api->retry_attempt = 0;
        $api->save();
        return true;
    }
}
