<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;
use Arcanedev\LaravelAuth\Contracts\Role as RoleContract;
use Arcanedev\LaravelAuth\Traits\AuthRoleRelationships;
use Carbon\Carbon;

/**
 * Class     Role
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int     id
 * @property  string  slug
 * @property  string  description
 * @property  bool    is_active
 * @property  bool    is_locked
 * @property  Carbon  created_at
 * @property  Carbon  updated_at
 */
class Role extends Model implements RoleContract
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use AuthRoleRelationships;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description'];

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setTable(config('laravel-auth.roles.table', 'roles'));

        parent::__construct($attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Getters & Setters
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Set the name attribute.
     *
     * @param  string  $name
     */
    public function setNameAttributes($name)
    {
        $this->attributes['name'] = $name;
        $this->setSlugAttribute($name);
    }

    /**
     * Set the slug attribute.
     *
     * @param  string  $slug
     */
    public function setSlugAttribute($slug)
    {
        $this->attributes['slug'] = str_slug($slug);
    }

    /* ------------------------------------------------------------------------------------------------
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Attach a permission to a role.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|int  $permission
     *
     * @return int|bool
     */
    //public function attachPermission($permission)

    /**
     * Detach a permission from a role.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|int  $permission
     *
     * @return int
     */
    public function detachPermission($permission)
    {
        return $this->permissions()->detach($permission);
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        return $this->permissions()->detach();
    }
}
