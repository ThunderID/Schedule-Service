<?php 

namespace App\Entities\Observers;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;

use App\Entities\Schedule as Model; 

/**
 * Used in Schedule model
 *
 * @author cmooy
 */
class ScheduleObserver 
{
	public function saving($model)
	{
		return true;
	}
}
