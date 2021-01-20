<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use App\Repository\BaseRepository;
use App\Helper\Utilities;
use App\Traits\UploadTrait;
use Str;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    use UploadTrait;
   
    private $BaseRepository;
    public function __construct()
    {
        $this->BaseRepository = new BaseRepository(new Patient());
    }
    public function index(Request $request)
    {
        $request->validate([
            'skip' => 'Integer',
            'take' => 'required|Integer'
        ]);

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
        $file = $request->file('file');
        $name = Str::slug($request->input('name')).'_'.time();
        $folder = '/uploads/images/';
        $filePath = $folder . $name. '.' . $file->getClientOriginalExtension();
        $this->uploadOne($file, $folder, 'public', $name);

         $this->validateRequest($request);
        $response = $this->BaseRepository->create(
            [
            'name' =>$request['name'],
            'phone' =>$request['phone'] ,
            'password' =>  Hash::make($request['password']),
            'type' =>'patient',

            ],
            [
            'file' =>$filePath,
            'age' =>$request['age'],
            'condition' =>$request['condition'],
             ]
            ,'patient'
        );
        return Utilities::wrap($response);
    }

    public function show($Patient)
    {
        $response = $this->BaseRepository->getById($Patient);
        return Utilities::wrap($response);
    }

    public function edit($Patient , Request $request)
    {
        $this->validateRequest($request,'sometimes|');

        if($request->hasFile('files')){
            $file = $request->file('files');
            $name = Str::slug('image').'_'.time();
            $folder = '/uploads/images/';
            $filePath = $folder . $name. '.' . $file->getClientOriginalExtension();
            $this->uploadOne($file, $folder, 'public', $name);
            $request['file']= $filePath;
        }
        $request['password'] = Hash::make($request['password']);

        $response = $this->BaseRepository->update($Patient,$request);
        return Utilities::wrap($response);
    }

   
    public function destroy($Patient)
    {
        $response = $this->BaseRepository->delete($Patient);
        return Utilities::wrap($response);
    }
    private function validateRequest( $request, $options = ''  ){

        return $this->validate($request,[
            'name' => $options."required|string|min:4", 
            'phone' => $options."required|integer|min:6", 
            'password' => $options."required|string|min:6", 
            'file' => $options.'required|mimes:csv,txt,xlx,xls,pdf,jpeg,png,jpg,gif,svg',
            'age' => $options."required|integer", 
            'condition' => $options."required|string", 
          

        ]);
    }
}
