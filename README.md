# Laravel integration for Facebook API

This library provides an objected-oriented wrapper of the PHP classes to send CRM Integration with Facebook API

## Installation

```
composer require silici0/facebook-api:dev-master
```

## Publish Conf File

```
php artisan vendor:publish --provider="silici0\FacebookApi\FacebookApiServiceProvider"
```

## Configuration

You need pixel_id and acesss_token to update your .env file with the follow options:

```
FACEBOOK_PIXEL_ID='ID'
FACEBOOK_ACCESS_TOKEN='TOKEN'
```

## Send Test Event 

Get your event test code and fill like the example below:

```
use silici0\FacebookApi;

$fb = new \FacebookApi();

$response = $fb::sendTest('TEST57893');

$msg = $response->getMessages();
$eventsReceived = $response->getEventsReceived();
$fbTraceId = $response->getFbTraceId();
```

## Send CRM Integration

To send CRM Integration you need send a Deduplication hash as event ID, so facebook can identify your browser pixel with your server Pixel:

```
$fb = new \FacebookApi();

$data[] = array(
	'user' => array(
		'id' => '1',
		'email' => 'rafael@email.com.br',
		'first_name' => 'rafael',
        'phone' => '1155994267171'
	),
	'custom' => array(
		'lead_event_source' => 'In-house CRM',
		'event_source' => 'crm'
	),
	'event' => array(
		'name' => 'Evento Lead',
		'unixtimestamp' => time(),
        'id' => Hash::make('rafael@email.com.br')
	)
);

$response = $fb::sendCRM($data);
foreach ($response as $key => $value) {
    print("<pre>".print_r($value,true)."</pre>");
}
```
