<?php
namespace App\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

 class SecondaryRepository {
    public $table;
    public function __construct(Model $model){
        $this->table = $model;
    }


    public function getAll($conditions, $columns, $sort, $skip, $take )
    {
        $result = $this->table->where($conditions)->where('is_deleted','=','false');

        if(!is_null($columns))
            $result = $result->select($columns);

        if(!is_null($sort))
            $result = $result->orderBy($sort->column, $sort->dir);

       $response = [
           'items' => $result->skip($skip)->take($take)->get(),
           'totalCount' => $result->count()
       ];

       return $response;
    }


    public function getById($id){
        return $this->table->where('is_deleted','=','false')->findorFail($id);
    }


    public function create($data){

        $item = $this->table->create($data);
        return $item;
      
    }



    public function update($id, $values){
        $item = $this->table->where('is_deleted','=','false')
        ->where('id',$id)
        ->first();
        $item = tap($item)->update($values);
        return $item;

    }


    public function delete($model ,$values){
        $model = $this->table->where('is_deleted','=','false')
        ->where('id',$model)
        ->first()
        ;
        $model['is_deleted'] = $values['is_deleted'];
        $model->save();
        return $model;
    }


    public function softDelete($model)
    {
        $model->update(['is_deleted' => 1]);
        return $model;
    }

}
