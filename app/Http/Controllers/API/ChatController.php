<?php


namespace App\Http\Controllers\API;


use App\Actions\ChatAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatSendRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    private ?\Illuminate\Contracts\Auth\Authenticatable $user;
    private ChatAction $chat;

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->user = auth()->guard('sanctum')->user();
        $this->chat = new ChatAction();
    }

    public function sendMessage(ChatSendRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $send = $this->chat->store($request, $this->user);
            if (!$send) {
                return $this->errorResponse();
            }
            return $this->successResponse();
        }catch (\Throwable $throwable){
            return $this->errorResponse();
        }
    }

    public function getMessages($with_user_id): \Illuminate\Http\JsonResponse
    {
        $messages = $this->chat->getMessages($this->user, User::find($with_user_id));
        return $this->successResponse($messages);
    }

    public function getAllMessages(Request $request): \Illuminate\Http\JsonResponse
    {
        $c = $this->chat->getAllMessages($request, $this->user);
        return $this->successResponse($c);
    }
}
