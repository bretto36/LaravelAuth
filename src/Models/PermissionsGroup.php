<?php namespace Arcanedev\LaravelAuth\Models;

use Arcanedev\LaravelAuth\Bases\Model;

/**
 * Class     PermissionsGroup
 *
 * @package  Arcanedev\LaravelAuth\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @property  int                                       id
 * @property  string                                    name
 * @property  string                                    slug
 * @property  string                                    description
 * @property  \Carbon\Carbon                            created_at
 * @property  \Carbon\Carbon                            updated_at
 * @property  \Illuminate\Database\Eloquent\Collection  permissions
 */
class PermissionsGroup extends Model
{
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
        $this->setTable(config('laravel-auth.permissions-group.table', 'permissions_group'));

        parent::__construct($attributes);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Relationships
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Permissions Groups has many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(Permission::class, 'group_id');
    }

    /* ------------------------------------------------------------------------------------------------
     |  CRUD Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Create and attach a permission.
     *
     * @param  array  $attributes
     * @param  bool   $reload
     */
    public function createPermission(array $attributes, $reload = true)
    {
        $this->permissions()->create($attributes);

        if ($reload) {
            $this->load('permissions');
        }
    }

    /**
     * Attach the permission to a group.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission  $permission
     * @param  bool                                      $reload
     */
    public function attachPermission(&$permission, $reload = true)
    {
        if ($this->hasPermission($permission)) {
            return;
        }

        $permission = $this->permissions()->save($permission);

        if ($reload) {
            $this->load('permissions');
        }
    }

    /**
     * Attach the permission by id to a group.
     *
     * @param  int   $id
     * @param  bool  $reload
     *
     * @return \Arcanedev\LaravelAuth\Models\Permission
     */
    public function attachPermissionById($id, $reload = true)
    {
        $permission = $this->getPermissionById($id);

        $this->attachPermission($permission, $reload);

        return $permission;
    }

    /**
     * Attach the permission from a group.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission  $permission
     * @param  bool                                      $reload
     */
    public function detachPermission(&$permission, $reload = true)
    {
        if ( ! $this->hasPermission($permission)) {
            return;
        }

        $permission = $this->getPermissionFromGroup($permission);

        $permission->update([
            'group_id' => 0,
        ]);

        if ($reload) {
            $this->load('permissions');
        }
    }

    /**
     * Attach the permission by id to a group.
     *
     * @param  int   $id
     * @param  bool  $reload
     *
     * @return \Arcanedev\LaravelAuth\Models\Permission
     */
    public function detachPermissionById($id, $reload = true)
    {
        $permission = $this->getPermissionById($id);

        $this->detachPermission($permission, $reload);

        return $permission;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Check Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Check if role has the given permission (Permission Model or Id).
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|int  $id
     *
     * @return bool
     */
    public function hasPermission($id)
    {
        if ($id instanceof Permission) {
            $id = $id->getKey();
        }

        return ! is_null($this->getPermissionFromGroup($id));
    }

    /**
     * Get a permission from the group.
     *
     * @param  \Arcanedev\LaravelAuth\Models\Permission|int  $id
     *
     * @return \Arcanedev\LaravelAuth\Models\Permission|null
     */
    private function getPermissionFromGroup($id)
    {
        if ($id instanceof Permission) {
            $id = $id->getKey();
        }

        $this->load('permissions');

        return $this->permissions->filter(function (Permission $permission) use ($id) {
            return $permission->id == $id;
        })->first();
    }

    /**
     * Get a permission by id.
     *
     * @param  int  $id
     *
     * @return \Arcanedev\LaravelAuth\Models\Permission|null
     */
    private function getPermissionById($id)
    {
        return $this->permissions()
            ->getRelated()
            ->where('id', $id)
            ->first();
    }
}
