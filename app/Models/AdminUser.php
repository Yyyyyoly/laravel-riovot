<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator;

class AdminUser extends Administrator
{
    /**
     * AdminUser constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('admin.database.users_table'));
    }

}
