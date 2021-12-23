<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Storage;



class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $Users = User::where('status', 1)->where('role', 0);

        if($request->hobbies != ""){
            $Users->where('hobbies', 'like', '%'. $request->hobbies.'%');
        }
        return $this->sendResponse($Users->get(), 'Users retrieved successfully.');
    }

    /**
     * @param Request $request
     * 
     * @return array
    */
    private function rules(Request $request , $id = "" ): array
    {
        $rules = [
            'first_name' => 'sometimes|required',
            'last_name' => 'sometimes|required',
            'user_photo' => 'image:jpeg,png,jpg,gif,svg|max:2048',
            'mobile_number' => 'sometimes|required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'status' => 'in:Active,Inactive',
        ];

        if ($id != "" ){
            $rules['email'] = 'sometimes|required|email:rfc,dns|unique:users,email,' . $id;
        }else{
            $rules['email'] = 'required|email:rfc,dns|unique:users';
        }

        if ($id == ""){
            $rules['password'] = 'required|min:6';

        }else{
            $rules['password'] = 'sometimes|required|min:6';
        }

        return  $rules;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, $this->rules($request));

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        //image upload
        if( $request->file('user_photo') ){
            $image = $request->file('user_photo');
            $image_uploaded_path = $image->store('users', 'public');
            $input["user_photo"] = Storage::disk('public')->url($image_uploaded_path);
        }

        if (isset($request->status)) {
            $input["status"] = 0;
            if ($input['status'] == 'Active') {
                 $input["status"] = 1;
            }
        }
        $input['password'] = bcrypt($input['password']);
        $User = User::create($input);
        
        $success['first_name'] =  $User->first_name;
        $success['last_name'] =  $User->last_name;
        $success['email'] =  $User->email;
        return $this->sendResponse($success, 'User created successfully.');

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $input = $request->all();

        $validator = Validator::make($input, $this->rules($request, $user->id));

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        
        if (isset($request->first_name)) {
            $user->first_name = $input['first_name'];
        }

        if (isset($request->last_name)) {
            $user->last_name = $input['last_name'];
        }

        if (isset($request->email)) {
            $user->email = $input['email'];
        }

        if (isset($request->mobile_number)) {
            $user->mobile_no = $input['mobile_number'];
        }

        if (isset($request->hobbies) &&  count($request->hobbies) > 0) {
            $user->hobbies = $input['hobbies'];
        }

        if (isset($request->password)) {
            $user->password = bcrypt($input['password']);
        }

        if (isset($request->status)) {
            $user->status = 0;
            if ($input['status'] == 'Active') {
                $user->status = 1;
            }
        }

        //image upload
        if ($request->file('user_photo')) {
            $image = $request->file('user_photo');
            $image_uploaded_path = $image->store('users', 'public');
            $user->user_photo = Storage::disk('public')->url($image_uploaded_path);
        }
        $user->save();

        return $this->sendResponse($user, 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $User)
    {
        $User->delete();
        return $this->sendResponse([], 'User deleted successfully.');
    }
    
    public function profileUpdate(Request $request)
    {
        $User = Auth::user();
        $input = $request->all();


        $validator = Validator::make($input, $this->rules($request, $User->id));

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if (isset($request->first_name)) {
            $User->first_name = $input['first_name'];
        }

        if (isset($request->last_name)) {
            $User->last_name = $input['last_name'];
        }

        if (isset($request->email)) {
            $User->email = $input['email'];
        }

        if (isset($request->mobile_number)) {
            $User->mobile_number = $input['mobile_number'];
        }

        if (isset($request->hobbies) &&  count($request->hobbies) > 0) {
            $User->hobbies = $input['hobbies'];
        }

        if (isset($request->password)) {
            $User->password = bcrypt($input['password']);
        }

        if (isset($request->status)) {
            $User->status = 0;
            if ($input['status'] == 'Active') {
                $User->status = 1;
            }
        }

        //image upload
        if ($request->file('user_photo')) {
            $image = $request->file('user_photo');
            $image_uploaded_path = $image->store('users', 'public');
            $User->user_photo = Storage::disk('public')->url($image_uploaded_path);
        }
        $User->save();
        return $this->sendResponse($User, 'Profile updated successfully.');
    }
}
