<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Join extends Model
{
    public $timestamps = false;

    // フォローしているユーザのIDを取得
    public function joinChannels(Int $user_id)
    {
        return $this->where('user_id', $user_id)->get('channel_id');
    }

    public function storeJoin(Int $channel_id, Int $user_id){
        $this->channel_id = $channel_id;
        $this->user_id = $user_id;
        $this->save();

        return;
    }
}
