<?php

namespace App\Models\Post;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * Not all attributes are s assignable.
     *
     * @var string<int, string>
     */

    protected $fillable = [
        "user_id",
        "team_id",
        "title",
        "slug",
        "content",
        "published",
        "thumbnail",
        "tags",
        "slug_id",
        "show_author",
    ];

    // Define the relationship with the Team model
    public function team()
    {
        return $this->belongsTo(Team::class, "team_id");
    }
    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }
}
