<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bot;
use App\Traits\fetchApi;
use Illuminate\Http\Request;
use App\Mail\RequestBookingMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class FetchInformationsController extends Controller
{
    use fetchApi;
    /**
     * Return home page
     */
    public function index()
    {
        $apiResult = $this->fetchApi();
        // Get region that we need
        $region = "Île-de-France";

        // Iterate records 
        $records = collect();
        collect($apiResult)->each(function ($record) use ($region, $records) {
            $created_at = Carbon::parse($record->record_timestamp);
            $minPostDate = Carbon::now()->subDays(5);

            // Get right record
            if ($created_at->greaterThanOrEqualTo($minPostDate) && $record->fields->regions === $region) {
                // Add in wish records
                $records->push($record);
            }
        });

        return view('welcome', [
            'records' => $records
        ]);
    }

    /**
     * LaunchBot
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function launchBot(Request $request)
    {
        // Get user informations
        $senderName = $request->name;
        $mail = $request->email;
        $password = $request->password;

        // Data to store in session
        $data = [
            "name"     => $senderName,
            "mail"     => $mail,
            "password" => bcrypt($password)
        ];

        // Save data in session
        foreach ($data as $key => $value) {
            Session::put([$key => $value]);
        }

        $apiResult = $this->fetchApi();
        // Get region that we need
        $region = "Île-de-France";
        // Get how many receiver mail we don't get
        $emailNotFound = 0;
        // Get how many receiver mail we've send
        $totalMessageSends = 0;
        // Get total receiver mail
        $receiverMailsTotal = 0;
        // Get residences total
        $totalResidenceFound = 0;

        collect($apiResult)->each(function ($record) use ($region, $request, &$emailNotFound, &$totalMessageSends, &$receiverMailsTotal, &$totalResidenceFound) {
            $created_at = Carbon::parse($record->record_timestamp);
            $minPostDate = Carbon::now()->subDays(5);

            // Get right record
            if ($created_at->greaterThanOrEqualTo($minPostDate) && $record->fields->regions === $region) {
                // Get receiver email
                $receiver = $record->fields->mail ?? null;
                ++$totalResidenceFound;

                // Check if receiver exists
                if (!is_null($receiver)) {
                    // Increment total mail we're get
                    ++$receiverMailsTotal;

                    // Get receiver e-mail to check if he already exists on system
                    $receiverAlreadyProcessed = Bot::firstWhere('receiverProcessed', $receiver);

                    // Make sure that receiver doesn't have received a mail by the bot
                    if(is_null($receiverAlreadyProcessed))
                    {
                        // Send mail
                        $sendMail = Mail::to("lmtahirou@gmail.com")->send(new RequestBookingMail($request->name, $request->email, $request->password, $record->fields));
                        if($sendMail)
                        {
                            // Increment total message sends
                            ++$totalMessageSends;
                        }

                        // Store receiver e-mail to avoid spam
                        Bot::create(['receiverProcessed' => $receiver]);
                    }
                } else {
                    // Increment total email not found
                    ++$emailNotFound;
                }
            }
        });

        return response()->json(
        [
            'message'             => 'success', 
            'totalResidenceFound' => $totalResidenceFound,
            'totalReceiverMails'  => $receiverMailsTotal,
            'totalMailNotFound'   => $emailNotFound,
            'totalMessageSends'   => $totalMessageSends
        ], 200);
    }
}
