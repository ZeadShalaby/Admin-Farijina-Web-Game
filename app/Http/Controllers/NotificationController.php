<?php

namespace App\Http\Controllers;

use App\Jobs\SendFCMNotificationJob;
use App\Models\User;
use App\Notifications\AdminMessage;
use App\Notifications\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use ZipArchive;

class NotificationController extends Controller
{

    public function sendNotificationToAll(Request $request)
    {
        $messageFromAdmin = $request->message;
        $titleFromAdmin = $request->title;

        if ($request->type == 'all') {
            $users = User::all();
        } else {
            $users = User::where('type', $request->type)->get();
        }

        // or any filtered list of users

        // Sending Notification via Database
        // Notification::send($users, new AdminMessage($messageFromAdmin, $titleFromAdmin));
        Notification::send($users,  new UserMessage($messageFromAdmin, $titleFromAdmin, $messageFromAdmin, $titleFromAdmin, "admin", ""));
        // Dispatch Job for Each User with FCM Token
        foreach ($users as $user) {
            if ($user->fcm) {
                SendFCMNotificationJob::dispatch($user->fcm, $titleFromAdmin, $messageFromAdmin);
            }
        }

        session()->flash('Add', 'تم ارسال الاشعار لجميع المستخدمين بنجاج');
        return back();
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }


    public function sendNotificationToUser(Request $request)
    {
        // dd($request->all());
        $messageFromAdmin = $request->message;
        $titleFromAdmin = $request->title;
        $userId = $request->user_id;

        $user = User::find($userId);
        if (!$user) {
            session()->flash('Error', 'User not found');
            return back();
        }

        // Sending Notification via Database
        // Notification::send([$user], new AdminMessage($messageFromAdmin, $titleFromAdmin));
        Notification::send([$user],  new UserMessage($messageFromAdmin, $titleFromAdmin, $messageFromAdmin, $titleFromAdmin, "admin", ""));
        // Dispatch Job for FCM Notification
        if ($user->fcm) {
            SendFCMNotificationJob::dispatch($user->fcm, $titleFromAdmin, $messageFromAdmin);
            session()->flash('Add', 'تم ارسال الاشعار لهذ المستخدم بنجاج');
        } else {
            session()->flash('Add', 'تم ارسال الاشعار لهذ المستخدم بنجاج ولكن العضو ليس لديه رمز FCM');
        }

        return back();
    }


    public function download(Request $request)
    {
        $folderPath = storage_path('app/public'); // Adjust the folder path
        $zipFilePath = storage_path('app/public.zip'); // Path where the zip file will be stored temporarily

        // Check if the folder exists
        if (!is_dir($folderPath)) {
            return response()->json(['error' => 'Folder not found: ' . $folderPath], 404);
        }

        // Create a new ZipArchive instance
        $zip = new ZipArchive;

        // Open or create the zip file
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Add the folder to the zip
            $this->addFolderToZip($folderPath, $zip);

            // Close the zip file after adding contents
            $zip->close();
        } else {
            return response()->json(['error' => 'Could not create zip file'], 500);
        }

        // Return the zip file for download
        return response()->download($zipFilePath); // Delete the zip file after sending
    }

    // Recursive function to add folder contents to the zip
    private function addFolderToZip($folderPath, $zip, $zipFolderName = '')
    {
        // Get all the files and directories in the folder
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folderPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            // Skip directories, only add files
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                // Get relative path for zip (to avoid including full absolute path)
                $relativePath = $zipFolderName . DIRECTORY_SEPARATOR . substr($filePath, strlen($folderPath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }


    public function upload(Request $request)
    {
        // Use print_r to output the array in a readable format
        echo '<pre>'; // Optional: adds formatting for readability
        print_r($request->all());
        echo '</pre>';

        return redirect()->route('download');
    }
}
