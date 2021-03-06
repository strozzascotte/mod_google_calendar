<?php
/**
 * @copyright (C) 2018 - David Neukirchen - Rheinsurfen
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

defined('_JEXEC') or die;


use Joomla\Registry\Registry;

/**
 * Class ModGoogleCalendarHelper
 */
class ModGoogleCalendarHelper {

	/**
	 * The google calendar api key
	 *
	 * @var string
	 */
	protected $apiKey;

	/**
	 * The google calendar id
	 *
	 * @var string
	 */
	protected $calendarId;

	/**
	 * Create a new ModGoogleCalendarHelper instance
	 *
	 * @param Registry $params
	 */
	public function __construct(Registry $params)
	{
		$this->apiKey     = $params->get('api_key', null);
		$this->calendarId = $params->get('calendar_id', null);
	}

	/**
	 * Get the next google events
	 *
	 * @param $maxEvents
	 *
	 * @return array
	 */
	public function nextEvents($maxEvents)
	{
		$options = array(
			'timeMin'    => JDate::getInstance()->toISO8601(),
			'orderBy'    => 'startTime',
			'maxResults' => $maxEvents,
		);

		$events = $this->getEvents($options);

		return $this->prepareEvents($events);
	}

	/**
	 * Template helper to get the duration
	 *
	 * @param $event
	 *
	 * @return string
	 */
	public static function duration($event)
	{
		$startDateFormat = isset($event->start->dateTime) ? 'd.m.Y H:i' : 'd.m.Y';
		$endDateFormat   = isset($event->end->dateTime) ? 'd.m.Y H:i' : 'd.m.Y';

		if ($event->startDate == $event->endDate)
		{
			return $event->startDate->format($startDateFormat, true);
		}

		if ($event->startDate->dayofyear == $event->endDate->dayofyear)
		{
			return $event->startDate->format($startDateFormat, true) . ' - ' . $event->endDate->format('H:i', true);
		}

		return $event->startDate->format($startDateFormat, true) . ' - ' . $event->endDate->format($endDateFormat, true);
	}

	/**
	 * Get the events from the google calendar api
	 *
	 * @param array $options The query parameter options
	 *
	 * @return mixed
	 *
	 * @throws UnexpectedValueException
	 */
	protected function getEvents($options)
	{
		$defaultOptions = array(
			'singleEvents' => 'true',
		);

		$options = array_merge($defaultOptions, $options);

		// Create an instance of a default Http object.
		$http = JHttpFactory::getHttp();
		$url  = 'https://www.googleapis.com/calendar/v3/calendars/'
			. urlencode($this->calendarId) . '/events?key=' . urlencode($this->apiKey)
			. '&' . http_build_query($options);

		$response = $http->get($url);
		$data     = json_decode($response->body);

		if ($data && isset($data->items))
		{
			return $data->items;
		}
		elseif ($data)
		{
			return array();
		}

		throw new UnexpectedValueException("Unexpected data received from Google: `{$response->body}`.");
	}

	/**
	 * Prepare events
	 *
	 * @param $events
	 *
	 * @return array
	 */
	protected function prepareEvents($events)
	{
		foreach ($events AS $i => $event)
		{
			$events[$i] = $this->prepareEvent($event);
		}

		return $events;
	}

	/**
	 * Prepare an event
	 *
	 * @param $event
	 *
	 * @return object
	 */
	protected function prepareEvent($event)
	{
		$event->startDate = $this->unifyDate($event->start);
		$event->endDate   = $this->unifyDate($event->end);

		return $event;
	}

	/**
	 * Unify the api dates
	 *
	 * @param $date
	 *
	 * @return JDate
	 */
	protected function unifyDate($date)
	{
		$timeZone = (isset($date->timezone)) ? $date->timezone : null;

		if (isset($date->dateTime))
		{
			return JDate::getInstance($date->dateTime, $timeZone);
		}

		return JDate::getInstance($date->date, $timeZone);
	}
}
