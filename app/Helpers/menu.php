<?php

use App\Models\Menu;

function getDynamicMenu()
{

    // $menus = \App\Models\Menu::where('sub_menu_id', 0)->orderBy('id', 'asc')->get()->toArray();
    // foreach ($menus as &$menu) {
    //     $menu['submenus'] = \App\Models\Menu::where('sub_menu_id', $menu['id'])->orderBy('id', 'asc')->get()->toArray();
    // }
    // return $menus;
    $menus = Menu::join('permission', 'menu.id', '=', 'permission.menu_id')
        ->join('roles', 'permission.role_id', '=', 'roles.id')
        ->select('menu.*')
        ->where('permission.view', 1)
        ->where('sub_menu_id', 0)
        ->where('roles.id', Auth::user()->role_type)
        ->orderBy('menu.id', 'asc')
        ->get()
        ->toArray();

    foreach ($menus as &$menu) {
        $menu['submenus'] = Menu::join('permission', 'menu.id', '=', 'permission.menu_id')
            ->join('roles', 'permission.role_id', '=', 'roles.id')
            ->select('menu.*')
            ->where('permission.view', 1)
            ->where('sub_menu_id', $menu['id'])
            ->orderBy('menu.id', 'asc')
            ->where('roles.id', Auth::user()->role_type)
            ->get()
            ->toArray();
    }

    return $menus;
}
function get_all_menu_list()
{
    return $menus = \App\Models\Menu::get()->toArray();
}
function get_permission()
{
    return $permission = \App\Models\permission::orderBy('id', 'asc')->get()->toArray();
}
