<?php

namespace Novius\Backpack\CRUD\PanelTraits;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

trait Permissions
{
    protected $availablePermissions = ['list', 'create', 'update', 'delete'];
    protected $permissionsPrefix;
    protected $defaultPermissionPrefix;

    /**
     * Initializes the permissions for the current CRUD controller.
     *
     * Available only if Backpack\PermissionManager is installed.
     *
     * @return bool
     */
    public function initPermissions()
    {
        // Checks if the PermissionManagerServiceProvider exists
        if (! class_exists('Backpack\PermissionManager\PermissionManagerServiceProvider')) {
            return false;
        }

        // Creates the permissions that doesn't already exists
        if (config('crud-extended.create_permissions_while_browsing', false)) {
            $this->createMissingPermissions();
        }

        // Gives the current's CRUD permissions to the currently connected user
        if (config('crud-extended.give_permissions_to_current_user_while_browsing', false)) {
            $user = Auth::user();
            if (! empty($user)) {
                $this->givePermissionsToUser($user);
            }
        }

        // Applies permissions on the CRUD (denies/allows access from user permissions)
        if (config('crud-extended.apply_permissions', false)) {
            $this->initCrudAccessFromUserPermissions();
        }

        return true;
    }

    /**
     * Gives all the permissions of the current CRUD to the specified user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     */
    public function givePermissionsToUser($user)
    {
        // Assigns all permissions to user
        $this->getPermissions()->each(function ($permission, $key) use ($user) {
            try {
                if (! $user->hasPermissionTo($permission)) {
                    $user->givePermissionTo($permission);
                }
            } catch (PermissionDoesNotExist $e) {
            }
        });

        // Reloads user permissions
        $user->load('permissions');
    }

    /**
     * Sets the permission prefix (instead of the default one).
     *
     * @param string|null $prefix
     */
    public function setPermissionsPrefix($prefix)
    {
        $this->permissionsPrefix = $prefix;
    }

    /**
     * Gets the permission prefix.
     *
     * @return string
     */
    public function getPermissionPrefix()
    {
        if (! is_null($this->permissionsPrefix)) {
            $prefix = $this->permissionsPrefix;
        } else {
            $prefix = $this->getDefaultPermissionPrefix();
        }

        return $prefix;
    }

    /**
     * Get the default permission prefix (derived from the controller's namespace).
     *
     * @param bool $cached
     * @return string
     */
    public function getDefaultPermissionPrefix($cached = true)
    {
        if (is_null($this->defaultPermissionPrefix) || ! $cached) {
            $this->defaultPermissionPrefix = '';

            if (! empty($this->controller)) {

                // Splits the controller's namespace and extracts the class name
                $namespaceParts = collect(explode('\\', trim(get_class($this->controller), '\\')));
                $className = $namespaceParts->pop();

                $namespaceParts = $namespaceParts->map(function ($value) {
                    return mb_strtolower($value);
                });

                // Removes the app/vendor prefix from namespace
                $namespaceParts = $namespaceParts->slice($namespaceParts->first() === 'app' ? 1 : 2);

                // Prepends "admin" to the prefix if present in the namespace or if it's a CRUD controller.
                // Redundant words like "crud" or "backpack" are also removed.
                if (is_subclass_of($this->controller, CrudController::class) || $namespaceParts->contains('admin')) {
                    $namespaceParts = $namespaceParts->diff(['backpack', 'admin', 'crud'])->prepend('admin');
                }

                // Removes excluded words from namespace and class name
                $excludedWords = config('crud-extended.excluded_words_from_default_permission_prefix', []);
                $namespaceParts = $namespaceParts->diff($excludedWords);
                $className = collect(explode('_', snake_case($className)))->diff($excludedWords)->implode('.');

                // Builds the prefix
                $prefix = implode('.', array_merge($namespaceParts->toArray(), [$className]));

                $this->defaultPermissionPrefix = (string) $prefix;
            }
        }

        return $this->defaultPermissionPrefix;
    }

    /**
     * Gets the permissions.
     *
     * @return Collection
     */
    public function getPermissions()
    {
        $prefix = $this->getPermissionPrefix();

        $permissions = collect($this->availablePermissions)->map(function ($item, $key) use ($prefix) {
            return $this->getPrefixedPermission($item);
        });

        return $permissions;
    }

    /**
     * Gets the prefixed permission item.
     *
     * @param $item
     * @return string
     */
    protected function getPrefixedPermission($item)
    {
        $permission = $item;

        // Adds the prefix
        $prefix = $this->getPermissionPrefix();
        if (! empty($prefix)) {
            $permission = $prefix.'::'.$permission;
        }

        return $permission;
    }

    /**
     * Gets the permissions that are missing in database.
     *
     * @return Collection
     */
    public function getMissingPermissions()
    {
        $prefix = $this->getPermissionPrefix();

        // Gets the existing permissions
        $databasePermissions = \Backpack\PermissionManager\app\Models\Permission::where('name', 'like', $prefix.'::%')
            ->get(['name'])
            ->pluck('name');

        // Gets the diff with available permissions
        $missingPermissions = $this->getPermissions()->diff($databasePermissions);

        return $missingPermissions;
    }

    /**
     * Creates the missing permissions in database.
     *
     * @return Collection
     */
    public function createMissingPermissions()
    {
        $permissions = $this->getMissingPermissions();
        if ($permissions->isNotEmpty()) {
            $this->createPermissions($permissions);
        }

        return $permissions;
    }

    /**
     * Creates the specified permissions in database.
     *
     * @param Collection $permissions
     * @return bool
     */
    protected function createPermissions($permissions)
    {
        // Add missing permissions to DB
        $datas = $permissions->map(function ($permissionName, $key) {
            return ['name' => $permissionName, 'created_at' => Carbon::now()];
        });

        $inserted = \Backpack\PermissionManager\app\Models\Permission::insert($datas->toArray());

        // Forget permissions cache
        app(\Backpack\PermissionManager\app\Models\Permission::class)->forgetCachedPermissions();

        return $inserted;
    }

    /**
     * Initializes the CRUD access from the current user's permissions.
     */
    protected function initCrudAccessFromUserPermissions()
    {
        // Gets the CRUD permissions
        $permissions = $this->getPermissions();

        // Gets the current user
        $user = Auth::user();
        if (empty($user)) {
            return;
        }

        // Denies access for each permission that the user has not
        $permissions->each(function ($permission, $key) use ($user) {
            try {
                if (! $user->hasPermissionTo($permission)) {
                    $this->denyAccess($this->extractPermissionKey($permission));
                }
            } catch (PermissionDoesNotExist $e) {
                // If permission does not exists : we deny access for security reasons
                $this->denyAccess($this->extractPermissionKey($permission));
            }
        });
    }

    /**
     * Extracts the permission key.
     *
     * @param $permission
     * @return string
     */
    protected function extractPermissionKey($permission)
    {
        return str_after($permission, '::');
    }
}
