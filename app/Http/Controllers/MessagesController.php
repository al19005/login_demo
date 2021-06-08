<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Message;
use App\Models\Comment;
use App\Models\Follower;
use App\Models\Channel;
use App\Models\Join;

class messagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Message $message, Follower $follower, Channel $channel, Join $join)
    {
        $user = auth()->user();
        $follow_ids = $follower->followingIds($user->id);
        // followed_idだけ抜き出す
        $following_ids = $follow_ids->pluck('followed_id')->toArray();

        $channel_id = $request->input('channel_id');

        if (empty($channel_id)){
            $channel_id = 1;
        }

        $timelines = $message->getTimelines($user->id, $following_ids, $channel_id);

        $join_channels = $join->joinChannels($user->id);
        $join = $join_channels->pluck('channel_id')->toArray();

        $channels = $channel->getChannels($join);

        // $channels = $channel->getchannel();



        $channel_name = $channel->getChannelName($channel_id);

        return view('messages.index', [
            'user'      => $user,
            'timelines' => $timelines,
            'channels'  => $channels,
            'channel_id'  => $channel_id,
            'channel_name' => $channel_name
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Channel $channel)
    {
        $user = auth()->user();
        $channel_id = $request->input('channel_id');
        $channel_name = $channel->getChannelName($channel_id);

        return view('messages.create', [
            'user' => $user,
            'channel_name' => $channel_name,
            'channel_id' => $channel_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Message $message)
    {
        $user = auth()->user();
        $data = $request->all();
        $validator = Validator::make($data, [
            'text' => ['required', 'string', 'max:140']
        ]);

        $validator->validate();
        $message->messageStore($user->id, $data);

        return redirect('messages');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message, Comment $comment)
    {
        $user = auth()->user();
        $message = $message->getmessage($message->id);
        $comments = $comment -> getComments($message->id);

        return view('messages.show', [
            'user' => $user,
            'message' => $message,
            'comments' => $comments
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        $user = auth()->user();
        $messages = $message->getEditmessage($user->id, $message->id);

        if (!isset($messages)) {
            return redirect('messages');
        }

        return view('messages.edit', [
            'user'   => $user,
            'messages' => $messages
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'text' => ['required', 'string', 'max:140']
        ]);

        $validator->validate();
        $message->messageUpdate($message->id, $data);

        return redirect('messages');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        $user = auth()->user();
        $message->messageDestroy($user->id, $message->id);

        return back();
    }
}
