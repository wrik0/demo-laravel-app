<?php

declare(strict_types=1);

namespace App\Repository\DB;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Model;

class MenuItemRepository
{
    public static function createMenuItem(array $menuItemArray)
    {
        $menuItem = new MenuItem();
        $menuItem->name = $menuItemArray['name'];
        $menuItem->description = $menuItemArray['description'];
        throw_if(!$menuItem->save(), new DBException("error creating MenuItem entry on DB"));

        return $menuItem;
    }
    public static function updateMenuItem(Model $menuItem, array $menuItemArray)
    {
        $menuItem->name = $menuItemArray['name'];
        $menuItem->description = $menuItemArray['description'];
        $menuItem->update();

        return $menuItem;

    }
    public static function deleteMenuItem(Model $menuItem){
        $menuItem->delete();
    }
    // public static function getMenuItems(int $offset, int $count)
    // {

    // }
}
