<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\BaseRepository;
use App\Helper\Utilities;
use App\Traits\UploadTrait;
use Str;
use App\Models\Doctor;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{
    use UploadTrait;
   
    private $BaseRepository;
    public function __construct()
    {
        $this->BaseRepository = new BaseRepository(new Doctor());
        $this->user = auth('api')->user();

    }
    public function index(Request $request)
    {
        // $request->validate([
        //     'skip' => 'Integer',
        //     'take' => 'required|Integer'
        // ]);

        //Param
        $conditions = json_decode($request->filter, true);
        $columns = json_decode($request->columns, true);
        $sort = json_decode($request->sort);
        $skip = $request->skip;
        $take = $request->take;

        //Processing
        $response = $this->BaseRepository->getAll($conditions, $columns, $sort, $skip, $take);

        // Response
        return Utilities::wrap($response);
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        $file = $request->file('image');
        $name = Str::slug($request->input('name')).'_'.time();
        $folder = '/uploads/images/';
        $filePath = $folder . $name. '.' . $file->getClientOriginalExtension();
        $this->uploadOne($file, $folder, 'public', $name);

        $response = $this->BaseRepository->create(
            [
            'name' =>$request['name'],
            'phone' =>$request['phone'] ,
            'password' =>Hash::make ( $request['password']),
            'type' =>'doctor',

            ],
            [
            'image' =>$filePath,
            'specialty' =>$request['specialty'],
             ]
            ,'doctor'
        );
        return Utilities::wrap($response);
    }

    public function show($Doctor)
    {
        $response = $this->BaseRepository->getById($Doctor);
        return Utilities::wrap($response);
    }

    public function edit( $Doctor , Request $request)
    {  
        if($request->hasFile('file')){
            $file = $request->file('file');
            $name = Str::slug('image').'_'.time();
            $folder = '/uploads/images/';
            $filePath = $folder . $name. '.' . $file->getClientOriginalExtension();
            $this->uploadOne($file, $folder, 'public', $name);
            $request['image']= $filePath;
        }
        $request['password'] = Hash::make($request['password']);

        $response = $this->BaseRepository->update($Doctor,$request);
        return Utilities::wrap($response);
    }

   
    public function destroy($Doctor)
    {
        $response = $this->BaseRepository->delete($Doctor);
        return Utilities::wrap($response);
    }
    private function validateRequest( $request, $options = ''  ){

        return $this->validate($request,[
            'name' => $options."required|string|min:4", 
            'phone' => $options."required|integer|min:6", 
            'password' => $options."required|string|min:6", 
            'image' => $options.'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'specialty' => $options."required|string", 

        ]);
    }
}
