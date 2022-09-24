<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuItem\StoreMenuItemRequest;
use App\Http\Requests\MenuItem\UpdateMenuItemRequest;
use App\Models\MenuItem;
use App\Repository\DB\MenuItemRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\Collection;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // TODO add pagination
        // TODO add transaction
        try {
            $menuItems = DB::transaction(function () {
                return MenuItem::all();
            }, 10) ?? new Collection();
        } catch (\Exception $e) {
            return response()->json(
                "error fetching from database",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return response()->json(
            $menuItems->toArray(),
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\MenuItem\StoreMenuItemRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreMenuItemRequest $request)
    {
        // check authorization
        if (!Gate::allows('create', new MenuItem())) {
            return response()->json([
                'status' => 'access denied'
            ], Response::HTTP_FORBIDDEN);
        };
        try {
            $menuItem = DB::transaction(function () use ($request) {
                return  MenuItemRepository::createMenuItem([
                    'name' => $request->input('name'),
                    'description' => $request->input('description') ?? null,
                ]);
            }, 10);
        } catch (\Exception $e) {
            dd($e);
            return response()->json(
                "error fetching from database",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            $menuItem->toArray(),
            Response::HTTP_OK
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MenuItem  $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(MenuItem $menuItem)
    {
        $menuItem = MenuItem::find($menuItem);
        abort_if(
            is_null($menuItem),
            Response::HTTP_NOT_FOUND,
            'requested menu item not found'
        );

        return response()->json($menuItem->toArray(), Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\MenuItem\UpdateMenuItemRequest  $request
     * @param  \App\Models\MenuItem  $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateMenuItemRequest $request, MenuItem $menuItem)
    {
        if (!Gate::allows('update')) {
            return response()->json([
                'status' => 'access denied'
            ], Response::HTTP_FORBIDDEN);
        };
        try {
            $menuItem = DB::transaction(function () use ($request, $menuItem) {
                return MenuItemRepository::updateMenuItem($menuItem, [
                    'name' => $request->input('name'),
                    'description' => $request->input('description'),
                ]);
            }, 10);
        } catch (\Exception $e) {
            return response()->json(
                "error fetching from database",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return response()->json(
            $menuItem->toArray(),
            Response::HTTP_OK
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MenuItem  $menuItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(MenuItem $menuItem)
    {
        if (!Gate::allows('delete')) {
            return response()->json([
                'status' => 'access denied'
            ], Response::HTTP_FORBIDDEN);
        };
        try {
            $menuItem = DB::transaction(function () use ($menuItem) {
                return MenuItemRepository::deleteMenuItem($menuItem);
            }, 10);
        } catch (\Exception $e) {
            return response()->json(
                "error deleting from database",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return response()->json(
            status: Response::HTTP_OK
        );
    }
}
