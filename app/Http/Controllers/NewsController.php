<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\SecondaryRepository;
use App\Helper\Utilities;
use App\Traits\UploadTrait;
use Str;
use App\Models\News;

class NewsController extends Controller
{
    use UploadTrait;
       
    private $SecondaryRepository;
    public function __construct()
    {
        $this->SecondaryRepository = new SecondaryRepository(new News());
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
        $response = $this->SecondaryRepository->getAll($conditions, $columns, $sort, $skip, $take);

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
        $request['image']= $filePath;

         $this->validateRequest($request);
        $response = $this->SecondaryRepository->create( $request->all());
        return Utilities::wrap($response);
    }

    public function show($News)
    {
        $response = $this->SecondaryRepository->getById($News);
        return Utilities::wrap($response);
    }

    public function edit($News , Request $request)
    {
        $this->validateRequest($request,'sometimes|');
        $response = $this->SecondaryRepository->update($News,($request->all()));
        return Utilities::wrap($response);
    }

   
    public function destroy($News)
    {
        $response = $this->SecondaryRepository->delete($News ,array('is_deleted' => true));
        return Utilities::wrap($response);
    }

    private function validateRequest( $request, $options = ''  ){

        return $this->validate($request,[
            'title' => $options."required|string|min:4", 
            'file' => $options.'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => $options."required|string", 

        ]);
    }
}
