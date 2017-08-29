<?php

namespace App;

use App\Events\UserWasCreated;
use App\Events\UserWasUpdated;
use App\Events\UserWasDestroyed;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'admin_flag' => 'bool'
    ];

    protected $events = [
        'created' => UserWasCreated::class,
        'updated' => UserWasUpdated::class,
        'deleting' => UserWasDestroyed::class,
    ];

    /**
     * Promote the user to an admin
     * @method promoteToAdmin
     *
     * @return   void
     */
    public function promoteToAdmin()
    {
        $this->admin_flag = 1;
        $this->save();
    }

    /**
     * Is the user an admin
     * @method isAdmin
     *
     * @return   bool
     */
    public function isAdmin()
    {
        return !! $this->admin_flag;
    }

    /**
     * Get the users who are admins
     * @method scopeAdmins
     *
     * @return   Builder
     */
    public function scopeAdmins(Builder $query)
    {
        return $query->whereAdminFlag(1);
    }

    /**
     * A user can have many chat messages
     * @method chats
     *
     * @return   Collection<App\Chat>
     */
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
