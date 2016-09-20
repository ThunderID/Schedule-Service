<?php

namespace App\Entities;

use App\Entities\Observers\ScheduleObserver;

use MongoRegex;

use DatePeriod;
use DateInterval;
use Carbon\Carbon;

/**
 * Used for Schedule Models
 * 
 * @author cmooy
 */
class Schedule extends BaseModel
{
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $collection			= 'mt_schedule';

	/**
	 * Date will be returned as carbon
	 *
	 * @var array
	 */
	protected $dates				=	['created_at', 'updated_at', 'deleted_at'];

	/**
	 * The appends attributes from mutator and accessor
	 *
	 * @var array
	 */
	protected $appends				=	[];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden 				= [];

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable				=	[
											'ref_id'						,
											'agenda'						,
											'mode'							,
											'contents'						,
											'on'							,
										];
										
	/**
	 * Basic rule of database
	 *
	 * @var array
	 */
	protected $rules				=	[
											'agenda'						=> 'required',
											'mode'							=> 'required|in:routine,eventual',
											'contents.summary'				=> 'required|max:255',
											
											'on.date'						=> 'required_without:on.day|date_format:"Y-m-d"',
											'on.day'						=> 'required_without:on.date|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
											'on.time'						=> 'required_without:on.time_start|date_format:"H:i:s"',
											'on.time_start'					=> 'date_format:"H:i:s"',
											'on.time_end'					=> 'date_format:"H:i:s"',
											'on.timezone'					=> 'required|timezone',
										];


	/* ---------------------------------------------------------------------------- RELATIONSHIP ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- QUERY BUILDER ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- MUTATOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- ACCESSOR ----------------------------------------------------------------------------*/
	
	/* ---------------------------------------------------------------------------- FUNCTIONS ----------------------------------------------------------------------------*/
		
	/**
	 * boot
	 * observing model
	 *
	 */
	public static function boot() 
	{
        parent::boot();

		Schedule::observe(new ScheduleObserver);
    }

	/* ---------------------------------------------------------------------------- SCOPES ----------------------------------------------------------------------------*/

	/**
	 * scope to get condition where ref_id
	 *
	 * @param string or array of ref_id
	 **/
	public function scopeRefID($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('ref_id', $variable);
		}

		return $query->where('ref_id', 'regexp', '/^'. preg_quote($variable) .'$/i');
	}

	/**
	 * scope to get condition where agenda
	 *
	 * @param string or array of agenda
	 **/
	public function scopeAgenda($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('agenda', $variable);
		}

		return $query->where('agenda', 'regexp', '/^'. preg_quote($variable) .'$/i');
	}

	/**
	 * scope to get condition where mode
	 *
	 * @param string or array of mode
	 **/
	public function scopeMode($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('mode', $variable);
		}

		return $query->where('mode', 'regexp', '/^'. preg_quote($variable) .'$/i');
	}

	/**
	 * scope to get condition where date
	 *
	 * @param string or array of date
	 **/
	public function scopeOnDate($query, $variable)
	{
		if(is_array($variable))
		{
			$min = Carbon::parse($variable[0]);
			$max = Carbon::parse($variable[1]);

			if ($min->format('Y-m-d') > $max->format('Y-m-d'))
			{
				$tmp = $min;
				$min = $max;
				$max = $tmp;
			}

			$filter_days	= [];

			$interval 		= new DateInterval('P1D');
			$daterange 		= new DatePeriod($min, $interval ,$max);

			foreach($daterange as $date)
			{
				$filter_days[] 	= new MongoRegex('/^'.strtolower($date->format('l')).'/i');
			}

			return $query->where(function($query) use ($min, $max) {
						return $query->where('on.date', '>=', $min->format('Y-m-d'))->where('on.date', '<=', $max->format('Y-m-d'));	
					})->orwhere(function($query) use ($filter_days) {
						return $query->whereIn('on.day', $filter_days)->where('mode', 'routine');	
					});
		}

		return $query->where(function($query) use ($variable) {
			return $query->where('on.date', Carbon::parse($variable)->format('Y-m-d'));	
			})
			->orwhere(function($query) use ($variable) {
				return $query->where('mode', 'routine')->where('on.day', 'regexp', '/^'. preg_quote(Carbon::parse($variable)->format('l')) .'$/i');	
			});
	}

	/**
	 * scope to get condition where time
	 *
	 * @param string or array of time
	 **/
	public function scopeOnTime($query, $variable)
	{
		if(is_array($variable))
		{
			$min = Carbon::parse($variable[0])->format('H:i:s');
			$max = Carbon::parse($variable[1])->format('H:i:s');

			if ($min > $max)
			{
				$tmp = $min;
				$min = $max;
				$max = $tmp;
			}

			return $query->where(function($query) use ($min, $max) {
						return $query->where('on.time_start', '>=', $min)->where('on.time_end', '<=', $max);	
					})->orwhere(function($query) use ($min, $max) {
						return $query->where('on.time', '>=', $min)->where('on.time', '<=', $max);	
					});
		}

		return $query->where(function($query) use ($variable) {
				return $query->where('on.time_start', '>=', $variable)->where('on.time_end', '<=', $variable);	
			})
			->orwhere(function($query) use ($variable) {
				return $query->where('on.time', $variable);	
			});
	}

	/**
	 * scope to get condition where day
	 *
	 * @param string or array of day
	 **/
	public function scopeOnDay($query, $variable)
	{
		if(is_array($variable))
		{
			return 	$query->whereIn('on.day', $variable);
		}

		return $query->where('on.day', 'regexp', '/^'. preg_quote($variable) .'$/i');
	}


}
