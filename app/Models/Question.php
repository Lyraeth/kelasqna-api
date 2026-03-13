<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->with('author')->latest();
    }

    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'question_likes')
            ->withTimestamps();
    }

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'question_bookmarks')
            ->withTimestamps();
    }
}
