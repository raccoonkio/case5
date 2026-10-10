<?php

namespace App;

use Database\Factories\TripFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    /** @use HasFactory<TripFactory> */
    use HasFactory;

    protected $fillable = ['title', 'location', 'budget', 'body', 'places', 'photo_path'];

    protected function casts(): array
    {
        return ['budget' => 'decimal:2', 'places' => 'array', 'source_published_at' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceName(): string
    {
        $host = parse_url($this->source_url ?? '', PHP_URL_HOST);

        return match ($host) {
            't-j.ru' => 'Т—Ж',
            'klops.ru' => 'Клопс',
            default => $host ?: 'Источник',
        };
    }

    public function photoUrl(): string
    {
        return str_starts_with($this->photo_path, 'assets/') ? asset($this->photo_path) : asset('storage/'.$this->photo_path);
    }
}
