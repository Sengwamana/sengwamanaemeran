<?php

namespace Botble\Base\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AdminNotificationQueryBuilder extends BaseQueryBuilder
{
    public function hasPermission(): self
    {
        $user = Auth::guard()->user();

        if ($user->isSuperUser()) {
            return $this;
        }

        $this->when($user->permissions, function ($query, $permissions): void {
            $query->where(function ($query) use ($permissions): void {
                /**
                 * @var Builder $query
                 */
                $query
                    ->whereNull('permission')
                    ->orWhereIn('permission', $permissions);
            });
        });

        return $this;
    }
}
