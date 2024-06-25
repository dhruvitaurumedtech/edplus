<?php

namespace App\Console\Commands;

use App\Models\Remainder_model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PDO;

class RemainderCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remainder-cron-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        date_default_timezone_set('Asia/Kolkata');
        $data=Remainder_model::get();
        foreach($data as $value){
            if($value->type_field==1){
            $givenDateString = $value->date;
            $timeString = $value->time;
            $dateTime = Carbon::createFromFormat('H:i:s', $timeString);
            $givenTimeString = $dateTime->format('H:i');
            $givenDateTime = Carbon::createFromFormat('Y-m-d H:i', "$givenDateString $givenTimeString");
            $currentDateTime = Carbon::now()->format('Y-m-d H:i'); 
            if ($givenDateTime->format('Y-m-d H:i') === $currentDateTime && $value->status == 'false') {
         
                Remainder_model::where('id',$value->id)->update(['status'=>'success']);
           
                $serverKey = env('SERVER_KEY');

                $url = "https://fcm.googleapis.com/fcm/send";
                if(!empty($value->student_id)){
                    $users = User::where('id', $value->student_id)->pluck('device_key');
                }
                if(!empty($value->role_type_id)){
                    $users = User::where('role_type', $value->role_type_id)->pluck('device_key')->toArray();
                }
                // \Log::info($users);
                $notificationTitle = $value->title;
                $notificationBody = $value->message;

                $data = [
                    'registration_ids' => $users,
                    'notification' => [
                        'title' => $notificationTitle,
                        'body' => $notificationBody,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ];

                if (!empty($users)) {
                    $json = json_encode($data);

                    $headers = [
                        'Content-Type: application/json',
                        'Authorization: key=' . $serverKey
                    ];

                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json,
                        CURLOPT_HTTPHEADER => $headers,
                    ]);

                    $result = curl_exec($ch);

                    if ($result === FALSE) {
                    }

                    curl_close($ch);
                }
            } 
         }
         if($value->type_field==2){
            $givenDateString = $value->date;
            $currentDateString = Carbon::now()->format('Y-m-d');

            if ($givenDateString === $currentDateString && $value->status == 'false') {
                
                Remainder_model::where('id',$value->id)->update(['status'=>'success']);
                $serverKey = env('SERVER_KEY');

                $url = "https://fcm.googleapis.com/fcm/send";
                if(!empty($value->student_id)){
                    $users = User::where('id', $value->student_id)->pluck('device_key');
                }
                if(!empty($value->role_type_id)){
                    $users = User::where('role_type', $value->role_type_id)->pluck('device_key')->toArray();
                }
                // \Log::info($users);
                $notificationTitle = $value->title;
                $notificationBody = $value->message;

                $data = [
                    'registration_ids' => $users,
                    'notification' => [
                        'title' => $notificationTitle,
                        'body' => $notificationBody,
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ];

                if (!empty($users)) {
                    $json = json_encode($data);

                    $headers = [
                        'Content-Type: application/json',
                        'Authorization: key=' . $serverKey
                    ];

                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json,
                        CURLOPT_HTTPHEADER => $headers,
                    ]);

                    $result = curl_exec($ch);

                    if ($result === FALSE) {
                    }

                    curl_close($ch);
                }
            } 
         }
         Log::info('Test message.');



       }
    }
}
