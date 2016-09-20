FORMAT: 1A

# SCHEDULE-SERVICE

# Schedule [/schedules]
Schedule  resource representation.

## Show all Schedules [GET /schedules]


+ Request (application/json)
    + Body

            {
                "search": {
                    "_id": "string",
                    "refid": "string",
                    "agenda": "string",
                    "mode": "string",
                    "date": "array|string",
                    "time": "array|string",
                    "day": "array|string"
                },
                "sort": {
                    "newest": "asc|desc",
                    "date": "desc|asc",
                    "day": "desc|asc",
                    "time": "desc|asc",
                    "mode": "desc|asc"
                },
                "take": "integer",
                "skip": "integer"
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "data": {
                        "_id": "string",
                        "ref_id": "string",
                        "agenda": "string",
                        "mode": "routine|eventual",
                        "contents": [
                            "string"
                        ],
                        "on": {
                            "date": "string",
                            "day": "sunday|monday|tuesday|wednesday|thursday|friday|saturday",
                            "time": "string",
                            "time_start": "string",
                            "time_end": "string",
                            "timezone": "string"
                        }
                    },
                    "count": "integer"
                }
            }

## Store Schedule [POST /schedules]


+ Request (application/json)
    + Body

            {
                "_id": "string",
                "ref_id": "string",
                "agenda": "string",
                "mode": "routine|eventual",
                "contents": [
                    "string"
                ],
                "on": {
                    "date": "string",
                    "day": "sunday|monday|tuesday|wednesday|thursday|friday|saturday",
                    "time": "string",
                    "time_start": "string",
                    "time_end": "string",
                    "timezone": "string"
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": "string",
                    "ref_id": "string",
                    "agenda": "string",
                    "mode": "routine|eventual",
                    "contents": [
                        "string"
                    ],
                    "on": {
                        "date": "string",
                        "day": "sunday|monday|tuesday|wednesday|thursday|friday|saturday",
                        "time": "string",
                        "time_start": "string",
                        "time_end": "string",
                        "timezone": "string"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }

## Delete Schedule [DELETE /schedules]


+ Request (application/json)
    + Body

            {
                "id": null
            }

+ Response 200 (application/json)
    + Body

            {
                "status": "success",
                "data": {
                    "_id": "string",
                    "ref_id": "string",
                    "agenda": "string",
                    "mode": "routine|eventual",
                    "contents": [
                        "string"
                    ],
                    "on": {
                        "date": "string",
                        "day": "sunday|monday|tuesday|wednesday|thursday|friday|saturday",
                        "time": "string",
                        "time_start": "string",
                        "time_end": "string",
                        "timezone": "string"
                    }
                }
            }

+ Response 200 (application/json)
    + Body

            {
                "status": {
                    "error": [
                        "code must be unique."
                    ]
                }
            }