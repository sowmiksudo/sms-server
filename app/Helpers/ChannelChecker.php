<?php

namespace App\Helpers;

use App\Models\Channel;


class ChannelChecker {

    public static function check($name){
        $channel = Channel::where("name", $name)->get();
        if($channel->count() > 0){
            return $channel->first();
        } else {
            return false;
        }
    }

}
