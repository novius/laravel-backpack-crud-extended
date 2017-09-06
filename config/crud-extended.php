<?php

return [
    /*
    |------------
    | PERMISSIONS
    |------------
    */

    // Automatically applies permissions to CRUD controllers. Requires the Backpack/PermissionManager package.
    //
    // Each CRUD controller should have a unique prefix for its permission keys. By default the prefix is automatically
    // derived from the controller's namespace but you can set your own (see the setPermissionsPrefix() method).
    //
    // This prefix will then be used to match the permissions handled by the permission manager.
    //
    'apply_permissions' => false,

    // Creates the CRUD's permissions in database while browsing in admin.
    'create_permissions_while_browsing' => false,

    // Gives the CRUD's permissions to the currently connected user while browsing in admin. Should be disabled in production.
    'give_permissions_to_current_user_while_browsing' => false,

    // Words that are excluded from the auto generated permission prefix (based on route's controller namespace).
    'excluded_words_from_default_permission_prefix' => ['app', 'http', 'controllers', 'controller', 'crud'],
];
