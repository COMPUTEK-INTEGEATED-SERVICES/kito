<?php


namespace App\Actions;


use App\Events\NewChatMessage;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatAction
{
    public function store($data, $user): bool
    {
        DB::beginTransaction();
        try {
            $allowed_image = ['jpeg', 'png', 'gif', 'jpg'];

            if($data->file){
                //upload file
                $message =  $data->file('file')->store('public/attachments');

                if (in_array($data->file->extension(), $allowed_image))
                {
                    $type = 'image';
                }else{
                    $type = 'document';
                }
            }else{

                $message = $data->message;
            }

            //document the message
            $msg = $user->messages()->create([
                'message'=>$message,
                'type'=>$type??'text'
            ]);

            //save the message to the chat
            Chat::create([
                'user_1'=>$user->id,//sender
                'user_2'=>$data->to_user,//receiver
                'message_id'=>$msg->id,
            ]);

            DB::commit();
            broadcast(new NewChatMessage($msg, $user, $data->to_user));
            return true;
        }catch (\Exception $e){
            report($e);
            DB::rollBack();
            return false;
        }
    }

    public function getMessages($user1, $user2)
    {
        return Chat::where(function ($query) use ($user1, $user2) {
                    $query->where('user_1', $user1->id)->where('user_2', $user2->id);
                })->orWhere(function ($query) use ($user1, $user2) {
                    $query->where('user_1', $user2->id)->where('user_2', $user1->id);
                })->with('message')->get();
    }

    public function getAllMessages(Request $request, $user): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return Chat::with(['sender', 'receiver', 'message'])
            ->where('user_1', $user->id)
            ->orWhere('user_2', $user->id)
            ->select('chats.*')
            ->groupBy('user_1', 'user_2')
            ->latest()->paginate(20);
    }
}
