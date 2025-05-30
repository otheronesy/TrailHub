<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SynDriveService;

class SynDriveController extends Controller
{
    protected $driveService;

    public function __construct(SynDriveService $driveService)
    {
        $this->driveService = $driveService;
    }

    public function uploadFile(Request $request)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        $file = $request->file('file');
        if (!$file) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        try {
            $response = $this->driveService->uploadFile($file, $tokenRecord->access_token);
            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('Google Drive upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to upload file', 'message' => $e->getMessage()], 500);
        }
    }

    public function listFiles(Request $request)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        $pageSize = $request->query('pageSize', 10);

        try {
            $files = $this->driveService->listFiles($tokenRecord->access_token, $pageSize);
            return response()->json($files);
        } catch (\Exception $e) {
            \Log::error('Google Drive list files error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to list files', 'message' => $e->getMessage()], 500);
        }
    }

    public function deleteFile(Request $request, $fileId)
    {
        $user = $request->user();

        $tokenRecord = $user->oauthTokenFor('google');
        if (!$tokenRecord || $tokenRecord->isExpired()) {
            return response()->json(['error' => 'Google OAuth token missing or expired'], 401);
        }

        try {
            $this->driveService->deleteFile($fileId, $tokenRecord->access_token);
            return response()->json(['message' => 'File deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Google Drive delete file error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete file', 'message' => $e->getMessage()], 500);
        }
    }
}
