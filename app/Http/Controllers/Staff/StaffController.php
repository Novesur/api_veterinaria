<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search=$request->get("search");
        $users=User::where("name","ilike","%".$search."%")->orderBy("id","desc")->get();

        return response()->json([
            "users"=> UserCollection::make($users),
            "roles"=>Role::where("name","not ilike","%Veterinarios%")->get()->map(function($role){
                return[
                    "id"=>$role->id,
                    "name"=>$role->name,
                ];
            })
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $is_user_exists=User::where("email",$request->email)->first();
        if($is_user_exists){
             return response()->json([
                "message"=>403,
                "message_text"=>"El usuario ya existe",
            ]);
        }

        if($request->hasFile("imagen")){
            $path=Storage::putFile("users",$request->file("imagen"));
            $request->request->add(["avatar"=>$path]);
        }

        if($request->password){
            $request->request->add(["password"=>bcrypt($request->password)]);
        }

        $user=User::create($request->all());
        $role=Role::findOrFail($request->role_id);
        $user->assignRole($role);


         return response()->json([
            "message"=>403,
            "user"=>UserResource::make($user)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $is_user_exists=User::where("email",$request->email)->where("id","<>",$id)->first();
        if($is_user_exists){
             return response()->json([
                "message"=>403,
                "message_text"=>"El usuario ya existe",
            ]);
        }

        $user=User::findOrfail($id);

        if($request->hasFile("imagen")){

            if($user->avatar){
                Storage::delete($user->avatar);
            }
            $path=Storage::putfile("users",$request->file("imagen"));
            $request->request->add(["avatar"=>$path]);
        }

        $user->update($request->all());

        if($request->role_id && $request->role_id != $user->role_id){
            $role_od = Role::findOrFail($user->role_id);
            //removeRole es una funcion de Spatie
            $user->removeRole($role_od);
            $role_new = Role::findOrFail($request->role_id);
            $user->assignRole($role_new);
        }

         return response()->json([
            "message"=>403,
            "user"=>UserResource::make($user)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user=User::findOrfail($id);
        if($user->avatar){
            Storage::delete($user->avatar);
        }
        $user->delete();
        return response()->json([
            "message"=>200,
        ]);
    }
}
