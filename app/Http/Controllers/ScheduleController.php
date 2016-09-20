<?php

namespace App\Http\Controllers;

use App\Libraries\JSend;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Entities\Schedule;

/**
 * Schedule  resource representation.
 *
 * @Resource("Schedule", uri="/schedules")
 */
class ScheduleController extends Controller
{
	public function __construct(Request $request)
	{
		$this->request 				= $request;
	}

	/**
	 * Show all Schedules
	 *
	 * @Get("/")
	 * @Versions({"v1"})
	 * @Transaction({
	 *      @Request({"search":{"_id":"string","refid":"string","agenda":"string","mode":"string","date":"array|string","time":"array|string","day":"array|string"},"sort":{"newest":"asc|desc","date":"desc|asc","day":"desc|asc","time":"desc|asc","mode":"desc|asc"}, "take":"integer", "skip":"integer"}),
	 *      @Response(200, body={"status": "success", "data": {"data":{"_id":"string","ref_id":"string","agenda":"string","mode":"routine|eventual","contents":{"string"},"on":{"date":"string","day":"sunday|monday|tuesday|wednesday|thursday|friday|saturday","time":"string","time_start":"string","time_end":"string","timezone":"string"}},"count":"integer"} })
	 * })
	 */
	public function index()
	{
		$result						= new Schedule;

		if(Input::has('search'))
		{
			$search					= Input::get('search');

			foreach ($search as $key => $value) 
			{
				switch (strtolower($key)) 
				{
					case '_id':
						$result		= $result->id($value);
						break;
					case 'refid':
						$result		= $result->refid($value);
						break;
					case 'agenda':
						$result		= $result->agenda($value);
						break;
					case 'mode':
						$result		= $result->mode($value);
						break;
					case 'date':
						$result		= $result->ondate($value);
						break;
					case 'time':
						$result		= $result->ontime($value);
						break;
					case 'day':
						$result		= $result->onday($value);
						break;
					default:
						# code...
						break;
				}
			}
		}

		if(Input::has('sort'))
		{
			$sort					= Input::get('sort');

			foreach ($sort as $key => $value) 
			{
				if(!in_array($value, ['asc', 'desc']))
				{
					return response()->json( JSend::error([$key.' harus bernilai asc atau desc.'])->asArray());
				}
				switch (strtolower($key)) 
				{
					case 'newest':
						$result		= $result->orderby('ref_id', $value)->orderby('created_at', $value);
						break;
					case 'date':
						$result		= $result->orderby('ref_id', $value)->orderby('on.date', $value);
						break;
					case 'day':
						$result		= $result->orderby('ref_id', $value)->orderby('on.day', $value);
						break;
					case 'time':
						$result		= $result->orderby('ref_id', $value)->orderby('on.time', $value);
						break;
					case 'mode':
						$result		= $result->orderby('ref_id', $value)->orderby('mode', $value);
						break;
					default:
						# code...
						break;
				}
			}
		}
		else
		{
			$result		= $result->orderby('ref_id', 'desc')->orderby('on.date', 'desc');
		}

		$count						= count($result->get());

		if(Input::has('skip'))
		{
			$skip					= Input::get('skip');
			$result					= $result->skip($skip);
		}

		if(Input::has('take'))
		{
			$take					= Input::get('take');
			$result					= $result->take($take);
		}

		$result 					= $result->get();
		
		return response()->json( JSend::success(['data' => $result->toArray(), 'count' => $count])->asArray())
				->setCallback($this->request->input('callback'));
	}

	/**
	 * Store Schedule
	 *
	 * @Post("/")
	 * @Versions({"v1"})
	 * @Transaction({
	 *      @Request({"_id":"string","ref_id":"string","agenda":"string","mode":"routine|eventual","contents":{"string"},"on":{"date":"string","day":"sunday|monday|tuesday|wednesday|thursday|friday|saturday","time":"string","time_start":"string","time_end":"string","timezone":"string"}}),
	 *      @Response(200, body={"status": "success", "data": {"_id":"string","ref_id":"string","agenda":"string","mode":"routine|eventual","contents":{"string"},"on":{"date":"string","day":"sunday|monday|tuesday|wednesday|thursday|friday|saturday","time":"string","time_start":"string","time_end":"string","timezone":"string"}}}),
	 *      @Response(200, body={"status": {"error": {"code must be unique."}}})
	 * })
	 */
	public function post()
	{
		$id 			= Input::get('_id');

		if(!is_null($id) && !empty($id))
		{
			$result		= Schedule::id($id)->first();
		}
		else
		{
			$result 	= new Schedule;
		}
		

		$result->fill(Input::only('ref_id', 'agenda', 'mode', 'contents', 'on'));

		if($result->save())
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}
		
		return response()->json( JSend::error($result->getError())->asArray());
	}

	/**
	 * Delete Schedule
	 *
	 * @Delete("/")
	 * @Versions({"v1"})
	 * @Transaction({
	 *      @Request({"id":null}),
	 *      @Response(200, body={"status": "success", "data": {"_id":"string","ref_id":"string","agenda":"string","mode":"routine|eventual","contents":{"string"},"on":{"date":"string","day":"sunday|monday|tuesday|wednesday|thursday|friday|saturday","time":"string","time_start":"string","time_end":"string","timezone":"string"}}}),
	 *      @Response(200, body={"status": {"error": {"code must be unique."}}})
	 * })
	 */
	public function delete()
	{
		$schedule		= Schedule::id(Input::get('_id'))->first();
		
		$result 		= $schedule;

		if($schedule && $schedule->delete())
		{
			return response()->json( JSend::success($result->toArray())->asArray())
					->setCallback($this->request->input('callback'));
		}

		if(!$schedule)
		{
			return response()->json( JSend::error(['ID tidak valid'])->asArray());
		}

		return response()->json( JSend::error($schedule->getError())->asArray());
	}
}