<?php

namespace Botble\RequestLog\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\Setting\Enums\DataRetentionPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Query\Builder;

class RequestLog extends BaseModel
{
    use MassPrunable;

    protected $table = 'request_logs';

    protected $fillable = [
        'url',
        'status_code',
    ];

    protected $casts = [
        'referrer' => 'json',
        'user_id' => 'json',
    ];

    public function prunable(): Builder|BaseQueryBuilder
    {
        $days = setting('request_log_data_retention_period', DataRetentionPeriod::ONE_MONTH);

        if ($days === DataRetentionPeriod::NEVER) {
            return $this->query()->where('id', '<', 0);
        }

        return $this->query()->where('created_at', '<', Carbon::now()->subDays($days));
    }
}
