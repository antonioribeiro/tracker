<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Referer extends Base {

	protected $table = 'tracker_referers';

	protected $fillable = array(
		'url',
		'host',
		'domain_id',
	);

	public function domain()
	{
		return $this->belongsTo($this->getConfig()->get('domain_model'));
	}
}
