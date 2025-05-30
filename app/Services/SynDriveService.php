<?php

namespace App\Services;

use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;

class SynDriveService
{
    protected function getClient($accessToken)
    {
        if (is_string($accessToken)) {
            $accessToken = json_decode($accessToken, true);
        }

        if (!is_array($accessToken) || !isset($accessToken['access_token'])) {
            throw new \InvalidArgumentException('Invalid access token format');
        }

        $client = new Google_Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect'));
        $client->setAccessToken($accessToken);
        $client->addScope(Google_Service_Drive::DRIVE);

        return $client;
    }

    public function uploadFile($file, $accessToken)
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Drive($client);

        $driveFile = new Google_Service_Drive_DriveFile();
        $driveFile->setName($file->getClientOriginalName());

        return $service->files->create($driveFile, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, name, webViewLink',
        ]);
    }

    public function listFiles($accessToken, $pageSize = 10)
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Drive($client);

        $files = $service->files->listFiles([
            'pageSize' => $pageSize,
            'fields' => 'files(id, name, webViewLink)',
        ]);

        return $files->getFiles();
    }

    public function deleteFile(string $fileId, $accessToken)
    {
        $client = $this->getClient($accessToken);
        $service = new Google_Service_Drive($client);

        return $service->files->delete($fileId);
    }
}
