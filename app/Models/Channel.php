<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    public function getchannel()
    {
        return DB::table('channels')->get();
    }

    public function channelStore(Array $data)
    {
        $this->channel_name = $data['text'];
        $this->save();

        return;
    }

    public function getChannels(Array $channel_ids)
    {
        return $this->whereIn('id', $channel_ids)->orderBy('created_at', 'DESC')->paginate(50);
    }

    public function getChannelsRev(Array $channel_ids)
    {
        return $this->whereNotIn('id', $channel_ids)->orderBy('created_at', 'DESC')->paginate(50);
    }

    public function getChannelName(Int $channel_id)
    {
        return $this->where('id', $channel_id)->first()->channel_name;
    }
}
