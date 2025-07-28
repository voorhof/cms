<?php

namespace App\Models;

use App\Policies\PostPolicy;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[UsePolicy(PostPolicy::class)]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'body',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)
            ->withDefault([
                'name' => 'Anonymous',
            ]);
    }

    /**
     * Get the user that published the post.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by', 'id')
            ->withDefault([
                'name' => 'Anonymous',
            ]);
    }
}
