<?php

// These are the tables needed for each model.
// The tables can change but the model will remain the same througout the whole app
define("UM_USER_TABLE",         "users");
define("UM_ROLE_TABLE",         "roles");
define("UM_USER_ROLE_TABLE",    "roles_users");
define("UM_ACTION_ROLE_TABLE",  "actions_roles");
define("UM_USER_LOC_TABLE",     "acl_locations");