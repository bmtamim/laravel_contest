<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contest extends Model
{
    use HasFactory;
    use Sluggable;

    protected $fillable = [
        'user_id',
        'contest_no',
        'title',
        'slug',
        'description',
        'short_description',
        'ticket_price',
        'competition_details',
        'ticket_quantity',
        'ticket_sold',
        'image',
        'competition_start',
        'competition_end',
        'status',
        'is_draw',
        'draw_date',
        'contest_id',
    ];

    protected $dates = [
        'competition_start',
        'competition_end',
        'draw_date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function contest_gallery(): HasMany
    {
        return $this->hasMany(ContestImageGallery::class, 'contest_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeParent($query)
    {
        return $query->where('contest_id', null);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_contest', 'contest_id', 'category_id')->withTimestamps();
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
