<?php
/**
 * Created by Md.Abdullah Al Mamun.
 * Email: mamun1214@gmail.com
 * Date: 10/29/2022
 * Time: 9:57 PM
 * Year: 2022
 */

namespace App\Scopes;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Scope;

class BotFilter implements Scope
{

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */

    public function apply(Builder $builder, Model $model)
    {
        $ids = config('settings.bots.agent') . ',' . config('settings.bots.meron') . ',' . config('settings.bots.wala');
        $ids = explode(",", $ids);
        $builder->whereNotIn('id', $ids);
    }
}
