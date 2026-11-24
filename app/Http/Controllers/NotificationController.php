<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class NotificationController extends Controller
{
    public function index(Request $request) { return view('notifications.index',['notifications'=>$request->user()->notifications()->latest()->paginate(30)]); }
    public function read(Request $request, string $id) {
        $notification=$request->user()->notifications()->whereKey($id)->firstOrFail();
        $notification->markAsRead();
        return $notification->data['url'] ? redirect($notification->data['url']) : back();
    }
}
