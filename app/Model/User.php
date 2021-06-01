<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Model;

use Hyperf\DbConnection\Model\Model;
use Qbhy\HyperfAuth\AuthAbility;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class User extends Model implements Authenticatable
{
    use AuthAbility;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * guarded.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * hidden.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * following.
     *
     * @return \Hyperf\Database\Model\Relations\BelongsToMany;
     */
    public function following()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')
            ->withPivot([
                'created_at',
            ]);
    }

    /**
     * followers.
     *
     * @return \Hyperf\Database\Model\Relations\BelongsToMany;
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')
            ->withPivot([
                'created_at',
            ]);
    }

    /**
     * posts.
     *
     * @return \Hyperf\Database\Model\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * like posts.
     *
     * @return \Hyperf\Database\Model\Relations\BelongsToMany
     */
    public function likePosts()
    {
        return $this->belongsToMany(Post::class, 'post_like')
            ->where('liked', Post::LIKED_LIKE)
            ->withTimestamps();
    }
}
