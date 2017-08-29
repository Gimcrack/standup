<?php

namespace App;

use App\Client;
use Carbon\Carbon;
use App\Events\LogEntryWasCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LogEntry extends Model
{
    protected $guarded = [];

    protected $events = [
        'created' => LogEntryWasCreated::class
    ];

    protected $casts = [
        'client_id' => 'int',
    ];

    /**
     * A LogEntry belongs to one Client
     * @method client
     *
     * @return   Client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the recent log entries
     * @method scopeRecent
     *
     * @return   Builder
     */
    public function scopeRecent(Builder $query, $days = 7)
    {
        return $query->where('created_at','>',Carbon::now()->subDays($days));
    }
}
