<?php

namespace silici0\FacebookApi;

use Illuminate\Contracts\Config\Repository as Config;

use FacebookAds\Api;
use FacebookAds\Object\ServerSide\BatchProcessor;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\EventRequestAsync;
use FacebookAds\Object\ServerSide\UserData;
use FacebookAds\Object\ServerSide\ActionSource;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise;

class FacebookApi
{
	protected $config_handler;
	protected $events = array();

	public function __construct(Config $config)
	{
		$this->config_handler = $config;
	}

	public function sendTest($data)
	{
		$config = $this->config_handler->get('facebook-api.facebook_config');

		$api = Api::init(null, null, $config['access_token'], false);

		$user_data = (new UserData())
			->setEmail('joe'.$data.'@eg.com')
			->setClientIpAddress($_SERVER['REMOTE_ADDR'])
			->setClientUserAgent($_SERVER['HTTP_USER_AGENT']);

		$custom_data = (new CustomData())
			->setCurrency('brl')
			->setValue(0);

		$event = (new Event())
			->setEventName('TestEvent')
			->setEventId($data)
			->setEventTime(time())
			->setCustomData($custom_data)
			->setUserData($user_data);


		$request = (new EventRequest($config['pixel_id']))
			->setTestEventCode($data)
			->setEvents(array($event));

		$response = $request->execute();
		
		$msg = $response->getMessages();
		$eventsReceived = $response->getEventsReceived();
		$fbTraceId = $response->getFbTraceId();
		if ($eventsReceived >= 1)
			return $response;
		else
			return false;
	}

	public function sendCRM(array $data)
	{
		$config = $this->config_handler->get('facebook-api.facebook_config');
		$api = Api::init(null, null, $config['access_token'], false);

		foreach ($data as $key => $value) {
			$this->events[] = $this->createEvent($value);
		}

		foreach ($this->events as $key => $value) {
			$request = (new EventRequest($config['pixel_id']))
				->setUploadTag('Envio In-House CRM - Requests')
				->setEvents(array($value));
			$this->events[$key]['response'] = $request->execute();
		}

		// print("<pre>".print_r($this->events,true)."</pre>");
		return $this->events;
	}

	public function createEvent(array $data)
	{

		$user_data = (new UserData())
			->setLeadId($data['user']['id'])
			->setEmail($data['user']['email'])
			->setFirstName($data['user']['first_name'])
			->setPhone($data['user']['phone']);
			// ->setClientIpAddress($data['user']['ip'])
			// ->setClientUserAgent($data['user']['agent']);

		if (isset($data['custom']['value']) and !empty($data['custom']['value'])) {
			$custom_data = (new CustomData())
				->setCurrency('brl')
				->setValue($data['custom']['value'])
				->setCustomProperties(array(
					'lead_event_source' => $data['custom']['lead_event_source'],
					'event_source' => $data['custom']['event_source']
				));
		} else {
			$custom_data = (new CustomData())
				->setCurrency('brl')
				->setValue(0)
				->setCustomProperties(array(
					'lead_event_source' => $data['custom']['lead_event_source'],
					'event_source' => $data['custom']['event_source']
				));
		}

		$event = (new Event())
			->setEventId($data['event']['id'])
			->setEventName($data['event']['name'])
			->setEventTime($data['event']['unixtimestamp'])
			->setUserData($user_data)
			->setCustomData($custom_data)
			->setActionSource(ActionSource::SYSTEM_GENERATED);

		return $event;
	}
}