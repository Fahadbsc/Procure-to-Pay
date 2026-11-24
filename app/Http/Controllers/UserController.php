<?php
namespace App\Http\Controllers;
use App\Models\{Role,User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash};
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    private function formData(): array { $companyId=Auth::user()->company_id; return ['roles'=>Role::orderBy('name')->get(),'managers'=>User::where('company_id',$companyId)->orderBy('name')->get()]; }
    public function index(){ $users=User::with(['roles','manager'])->where('company_id',Auth::user()->company_id)->orderBy('name')->paginate(25); return view('users.index',compact('users')); }
    public function create(){ return view('users.form',$this->formData()+['user'=>new User(),'selectedRoles'=>[],'editing'=>false]); }
    public function store(Request $request){ $data=$this->validateData($request); $user=User::create(['company_id'=>Auth::user()->company_id,'name'=>$data['name'],'email'=>$data['email'],'password'=>Hash::make($data['password']),'manager_id'=>$data['manager_id']??null]); $user->roles()->sync($data['roles']); return redirect()->route('users.index')->with('success','User created.'); }
    public function edit(User $user){ $this->authorizeUser($user); return view('users.form',$this->formData()+['user'=>$user,'selectedRoles'=>$user->roles->pluck('id')->all(),'editing'=>true]); }
    public function update(Request $request,User $user){ $this->authorizeUser($user); $data=$this->validateData($request,$user); $changes=['name'=>$data['name'],'email'=>$data['email'],'manager_id'=>$data['manager_id']??null]; if(!empty($data['password']))$changes['password']=Hash::make($data['password']); $user->update($changes); $user->roles()->sync($data['roles']); return redirect()->route('users.index')->with('success','User updated.'); }
    private function validateData(Request $request,?User $user=null): array { return $request->validate(['name'=>'required|string|max:255','email'=>['required','email','max:255',Rule::unique('users','email')->ignore($user?->id)],'password'=>$user?'nullable|string|min:8':'required|string|min:8','manager_id'=>['nullable','integer',Rule::exists('users','id')->where('company_id',Auth::user()->company_id)],'roles'=>'required|array|min:1','roles.*'=>'required|integer|distinct|exists:roles,id']); }
    private function authorizeUser(User $user): void { abort_unless((int)$user->company_id===(int)Auth::user()->company_id,403); }
}
