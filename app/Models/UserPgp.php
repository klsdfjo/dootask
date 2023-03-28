<?php

namespace App\Models;
/**
 * App\Models\UserPgp
 *
 * @property int $id
 * @property int|null $userid 所属人ID
 * @property string|null $public_key 公钥
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPgp whereUserid($value)
 * @mixin \Eloquent
 */
class UserPgp extends AbstractModel
{

}
