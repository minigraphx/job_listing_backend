<?php

namespace App\Http\Controllers\Api\Internal\LoggedIn;

use App\Http\Controllers\Controller;
use App\Models\Superadmin\PageBuilder\PageBuilderComponent;
use App\Models\Superadmin\PageBuilder\PageBuilderComponentCategory;
use App\Models\Team;
use Illuminate\Http\Request;

class PageBuilderComponentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Team $team, Request $request)
    {
        $this->authorize("can-read", $team);

        $componentsCategories = PageBuilderComponentCategory::all();

        $query = PageBuilderComponent::with("coverImages")
            ->with("categories")
            ->when($request->query("search_query"), function ($query, $term) {
                $query->where("title", "LIKE", "%" . $term . "%");
            });

        if (!auth()->user()->superadmin) {
            $query->where("published", true);
        }

        $components = $query->paginate(10);

        return [
            "component_categories" => $componentsCategories,
            "components" => $components,
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
