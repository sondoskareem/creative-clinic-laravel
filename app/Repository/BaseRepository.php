<?php
namespace App\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Models\User;
 class BaseRepository {
    public $table;
    public function __construct(Model $model){
        $this->table = $model;
    }

    public function getAll($conditions, $columns, $sort, $skip, $take)
    {
        $result = $this->table->where($conditions)->where('is_deleted','=','false')->with('user');

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
        return $this->table->where('is_deleted','=','false')->with('user')->findorFail($id);
    }


    public function create($primary_data , $secondary_date , $funcName){

        $user = User::create($primary_data);
        $additional_info = $user->$funcName()->create($secondary_date);
        return Arr::flatten(Arr::prepend(array($user),array($additional_info)));
      
    }



    public function update($id, $values){
        $user_value =['name' , 'password' , 'phone'];
        $secondary_user = $this->table->where('is_deleted','=','false')
        ->where('id',$id)
        ->first();
        $secondary_user = tap($secondary_user)->update($values->except($user_value));
        $main_user = tap($secondary_user->user())->update($values->only($user_value));
        return Arr::flatten(Arr::prepend(array($secondary_user->user()->get()),array($secondary_user))); //with return user befor updating
        // return $secondary_user;
    }


    public function delete($model){
        $model = $this->table->where('is_deleted','=','false')
        ->where('id',$model)
        ->first()
        ;
        $model = $model->user()->update(['is_deleted' => 1]);
        if ($model) return ['response' => true];
        else return ['response' => false];
    }


    public function softDelete($model)
    {
        $model->update(['is_deleted' => 1]);
        return true;
    }

}
