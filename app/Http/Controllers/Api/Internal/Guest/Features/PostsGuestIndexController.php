<?php

namespace App\Http\Controllers\Api\Internal\Guest\Features;

use App\Http\Controllers\Controller;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PostsGuestIndexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $postCount = null;

        $tagsOrContent = $request->input("tags_or_content");
        $searchQuery = $request->input("search_query");

        // categories filter logic # start
        $categoryIDs = [];

        if (isset($request->category) && is_array($request->category)) {
            foreach ($request->category as $category) {
                $categoryIDs[] = $category["id"] ?? null;
            }
        }

        // Remove null values from the array
        $categoryIDs = array_filter($categoryIDs, function ($value) {
            return $value !== null;
        });
        // categories filter logic # end

        // Check $searchQuery is an array
        // If it is, the implode function joins the elements of the array into a comma-separated string
        if (is_array($searchQuery)) {
            $searchQuery = implode(",", $searchQuery);
        }

        $categories = PostCategory::orderBy("name")->get();

        // Do not search with tags or content
        if (!$tagsOrContent) {
            $query = Post::latest()
                ->with("team")
                ->with("categories")
                ->with("coverImages")
                ->where("published", true)

                // categories filter logic # start
                ->when(!empty($categoryIDs), function ($query) use (
                    $categoryIDs
                ) {
                    $query->whereHas("categories", function ($query) use (
                        $categoryIDs
                    ) {
                        $query->whereIn("post_categories.id", $categoryIDs);
                    });
                })
                // categories filter logic # end

                // search for title, team name respecting states and categories
                ->when(!$tagsOrContent && $searchQuery, function ($query) use (
                    $searchQuery
                ) {
                    $query->where(function ($titleTeamQuery) use (
                        $searchQuery
                    ) {
                        $titleTeamQuery
                            ->where("title", "LIKE", "%" . $searchQuery . "%")
                            // search for team name
                            ->orWhereHas("team", function ($teamQuery) use (
                                $searchQuery
                            ) {
                                $teamQuery->where(
                                    "name",
                                    "LIKE",
                                    "%" . $searchQuery . "%"
                                );
                            });
                    });
                });

            $countQuery = clone $query;
            $postCount = $countQuery->count();
        }
        // Search with tags or content
        if ($tagsOrContent) {
            $query = Post::latest()
                ->with("team")
                ->with("categories")
                ->with("coverImages")
                ->where("published", true)

                // categories filter logic # start
                ->when(!empty($categoryIDs), function ($query) use (
                    $categoryIDs
                ) {
                    $query->whereHas("categories", function ($query) use (
                        $categoryIDs
                    ) {
                        $query->whereIn("post_categories.id", $categoryIDs);
                    });
                })
                // categories filter logic # end

                // search for title, team name respecting states and categories
                ->when($searchQuery, function ($query) use ($searchQuery) {
                    $query->where(function ($titleTeamQuery) use (
                        $searchQuery
                    ) {
                        $titleTeamQuery
                            ->where("title", "LIKE", "%" . $searchQuery . "%")

                            ->orWhere("tags", "LIKE", "%" . $searchQuery . "%")
                            ->orWhere(
                                "content",
                                "LIKE",
                                "%" . $searchQuery . "%"
                            )

                            // search for team name
                            ->orWhereHas("team", function ($teamQuery) use (
                                $searchQuery
                            ) {
                                $teamQuery->where(
                                    "name",
                                    "LIKE",
                                    "%" . $searchQuery . "%"
                                );
                            });
                    });
                });

            $countQuery = clone $query;
            $postCount = $countQuery->count();
        }

        $posts = $query->paginate(8);

        $posts->appends($request->all());

        return [
            "postCount" => $postCount,
            "categories" => $categories,
            "posts" => $posts,
            "oldInput" => [
                "search_query" => $request->input("search_query"),
            ],
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
