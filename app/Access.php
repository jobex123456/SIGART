<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    protected $table = 'access';
    protected $fillable = ['role_id', 'page_id', 'status'];

    public function menu(){
        return $this->join('pages', 'pages.id', '=', 'access.page_id')
                    ->join('modules', 'modules.id', '=', 'pages.module_id')
                    ->where('access.status', 1)
                    ->where('pages.status', 1)
                    ->where('modules.status', 1)
                    ->select('access.id', 'access.page_id', 'pages.name AS page_name', 'pages.module_id', 'modules.name AS module_name', 'pages.url')
                    ->orderBy('modules.name', 'asc')
                    ->orderBy('pages.name', 'asc')
                    ->get()
                    ->toArray();
    }

    public static function sideBar(){
        $access = new Access();
        $data = $access->menu();
        $menu = [];
        foreach($data as $row){
            $key = array_search($row['module_id'], array_column($menu, 'id'));
            if($key >= 0 and !is_bool($key)){
                $menu[$key]['pages'][] = [
                    'id' => $row['page_id'],
                    'name' => $row['page_name'],
                    'url' => $row['url']
                ];
            }else{
                $pages = [];
                $pages[0] = [
                    'id' => $row['page_id'],
                    'name' => $row['page_name'],
                    'url' => $row['url']
                ];
                $menu[] = [
                    'id' => $row['module_id'],
                    'name' => $row['module_name'],
                    'pages' => $pages
                ];
            }
        }
        
        return $menu;
    }
}
