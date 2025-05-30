<?php

namespace App\Services;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class CylendarService
{
    protected function getClient($accessToken)
    {
        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->addScope(Google_Service_Calendar::CALENDAR);

        // Decode token if JSON string
        if (is_string($accessToken)) {
            $accessToken = json_decode($accessToken, true);
        }

        if (!is_array($accessToken) || !isset($accessToken['access_token'])) {
            throw new \InvalidArgumentException('Invalid or missing access token');
        }

        $client->setAccessToken($accessToken);

        if ($client->isAccessTokenExpired() && isset($accessToken['refresh_token'])) {
            $newToken = $client->fetchAccessTokenWithRefreshToken($accessToken['refresh_token']);
            $client->setAccessToken($newToken);
            // TODO: Save $newToken back to your DB to keep tokens updated
        }

        return $client;
    }

    public function getEvents($accessToken, $calendarId = 'primary', $params = [])
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Calendar($client);

        $events = $service->events->listEvents($calendarId, $params);

        return $events->getItems();
    }

    public function getEvent(string $eventId, $accessToken, $calendarId = 'primary')
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Calendar($client);

        return $service->events->get($calendarId, $eventId);
    }

    public function createEvent(array $data, $accessToken, $calendarId = 'primary')
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Calendar($client);

        $event = new Google_Service_Calendar_Event($data);

        return $service->events->insert($calendarId, $event);
    }

    public function updateEvent(string $eventId, array $data, $accessToken, $calendarId = 'primary')
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Calendar($client);

        $event = $service->events->get($calendarId, $eventId);

        if (isset($data['summary'])) {
            $event->setSummary($data['summary']);
        }
        if (isset($data['start'])) {
            $event->setStart($data['start']);
        }
        if (isset($data['end'])) {
            $event->setEnd($data['end']);
        }

        return $service->events->update($calendarId, $eventId, $event);
    }

    public function deleteEvent(string $eventId, $accessToken, $calendarId = 'primary')
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Calendar($client);

        return $service->events->delete($calendarId, $eventId);
    }
}
