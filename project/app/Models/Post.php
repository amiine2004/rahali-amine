<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'image',
        'privacy',
        'is_active',
        'likes_count',
        'comments_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function incrementLikesCount(): void
    {
        $this->increment('likes_count');
    }

    public function decrementLikesCount(): void
    {
        $this->decrement('likes_count');
    }

    public function incrementCommentsCount(): void
    {
        $this->increment('comments_count');
    }

    public function decrementCommentsCount(): void
    {
        $this->decrement('comments_count');
    }
}