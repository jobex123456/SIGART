<?php 
namespace App\QueryDB;

use App\User;
use DB;
class UserQuery extends BaseQuery
{

    public function getModel()
    {
        return new User();
    }

    public function getPaginated($num_per_page, $buscar="")
    {
        return $this->getModel()::join('roles', 'users.role_id', '=', 'roles.id')
                ->where('users.status', '<>', 2)
                ->where(function($query) use($buscar){
                    if($buscar!=""){
                        $query->where('users.name', 'like', '%'.$buscar.'%')
                        ->orWhere('users.last_name', 'like', '%'.$buscar.'%')
                        ->orWhere('users.email', 'like', '%'.$buscar.'%')
                        ->orWhere('users.document', 'like', '%'.$buscar.'%')
                        ->orWhere('users.name', 'like', '%'.$buscar.'%');
                    }
                })
                ->select(
                    'users.id', 
                    'users.role_id', 
                    'users.name', 
                    'users.last_name', 
                    'users.email', 
                    'users.document', 
                    'users.birthday', 
                    'users.date_entry', 
                    'users.status', 
                    'users.address',
                    'users.phone',
                    'roles.name as role_name',
                    DB::raw("date_format(users.date_entry, '%Y') year_entry"),
                    DB::raw("(date_format(users.date_entry, '%c') -1) as month_entry"),
                    DB::raw("date_format(users.date_entry, '%e') day_entry"))
                ->orderBy('users.last_name', 'asc')
                ->paginate($num_per_page);
    }


}

