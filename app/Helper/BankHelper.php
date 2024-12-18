<?php

namespace App\Helper;


use App\Models\Role;
use App\Models\Status;


class BankHelper {

    public static function getRoles() {
        return Role::all();
    }

    public static function getStatus() {
        return Status::all();
    }
}
