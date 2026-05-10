<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Scopes\DomainScope;

class Log extends Model
{
	protected $fillable = [
		'domain_id',
		'user_id',
		'subject',
		'raw_html',
	];

	protected static function booted(): void
	{
		static::addGlobalScope(new DomainScope());
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function domain()
	{
		return $this->belongsTo(Domain::class);
	}
}


