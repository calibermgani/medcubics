<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
 use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;  
use App\Http\Controllers\Support\Api\TicketStatusApiController as TicketStatusApiController;
use App\Http\Controllers\Claims\Api\ClaimApiController as ClaimApiController;
use App\Http\Controllers\Medcubics\Api\AdminTicketApiController as AdminTicketApiController;
use App\Http\Controllers\Charges\Api\ChargeV1ApiController;
use App\Http\Helpers\Helpers as Helpers;
use Auth;
use App\Http\Controllers\Medcubics\Api\DBConnectionController as DBConnectionController;
use Session;
use DB;
$config = [
    // Your driver-specific configuration
    // "telegram" => [
    //    "token" => "TOKEN"
    // ]
];

   DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

   $botman = BotManFactory::create($config);

class BotManController extends Conversation
{
    public function run()
    {
        // $this->showIssues();

        $this->showStatus();
    }

    

    // public function showIssues()
    // {
    //     $butt_data=[];
    //     $ticket = new TicketStatusApiController();
    //     $status = $ticket->getTicketStatus(Auth::user()->id); 

    //     foreach($status as $stat)
    //     {
    //         $show_data=$stat['ticket_id']." - ".$stat['assigneddate'];

    //       array_push($butt_data,  Button::create($show_data)->value($stat['ticket_id']));
    //     }
     
    //     $question_reason = Question::create("These are the issues reported by you")
    //     ->fallback('Unable to ask question')
    //     ->callbackId('ask_reason')
    //     ->addButtons($butt_data);
    //     $user_id=Auth::user ()->id;

    //     return $this->ask($question_reason, function (Answer $answer_reason) use($ticket,$stat,$user_id) {
    //         $status = $ticket->getTicketDetails($answer_reason->getValue(),$stat['updated_at'],$user_id);
            
    //         $question_ffeedback = Question::create($status)
    //         ->fallback('Unable to ask question')
    //         ->callbackId('feedback_quest')
    //         ->addButtons([
    //         Button::create('Continue')->value('continue'),
    //         Button::create('Exit')->value('exit'),
    //         ]);

    //         $this->ask($question_ffeedback, function (Answer $answer){
    //             if ($answer->getValue() === 'continue') {

    //                 $this->run();
    //             }
    //             else if($answer->getValue() === 'exit')
    //             {
    //                 $user_name = Helpers::user_names(Auth::user ()->id);
    //                 $this->say("Thank you ".$user_name."!");
    //             }

    //         });
            
    //     });
    // }

    public function showStatus()
    {
        $db = new DBConnectionController();
        $db->connectPracticeDB(Session::get('practice_dbid'));

        $butt_data=[];
        $ticket = new TicketStatusApiController();
        $status = $ticket->getTicketState(Auth::user()->id);

        $claimstatus = $ticket->getClaimStatus(Auth::user()->id);

        if(!empty($status) && empty($claimstatus)){

            if(count($status) == '1'){
                if($status[0]['ticket_detail']['description'] == "" && $status[0]['status'] == "Open"){

                    foreach($status as $stat)
                    {
                      $show_data = "Ticket No: ".$stat['ticket_id']." <br> "."Status: ".$stat['status']." <br> ". "Comment: No Comments <br> <br> Is there something more I can help you with? / Do you want to know more?";
                      
                    }
                    $question_reason = Question::create($show_data)
                    ->fallback('Unable to ask question')
                    ->callbackId('ask_reason');
                    $user_id=Auth::user ()->id;

                    return $this->ask($question_reason, function (Answer $answer) use($ticket,$stat,$user_id) {

                        if($answer->getValue() == "yes"){
                            $this->say("Ok. What is it? Tell me");
                            if($answer->getValue() == "status"){
                                $this->run();
                            }
                        }
                        else if($answer->getValue() == "no")
                        {
                            $user_name = Helpers::user_names(Auth::user ()->id);
                            $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                        }

                    });
                }

                if($status[0]['status'] == "Open"){

                    foreach($status as $stat)
                    {
                      $show_data = "Ticket No: ".$stat['ticket_id']." <br> "."Status: ".$stat['status']." <br> ". "Comment: ".$status[0]['ticket_detail']['description']. "<br> <br> Is there something more I can help you with? / Do you want to know more?";
                    }

                    $question_reason = Question::create($show_data)
                    ->fallback('Unable to ask question')
                    ->callbackId('ask_reason');
                    $user_id=Auth::user ()->id;

                    return $this->ask($question_reason, function (Answer $answer) use($ticket,$stat,$user_id) {

                        if($answer->getValue() == "yes"){
                            $this->say("Ok. What is it? Tell me");
                           if($answer->getValue() == "status"){
                                $this->run();
                            }
                        }
                        else if($answer->getValue() == "no")
                        {
                            $user_name = Helpers::user_names(Auth::user ()->id);
                            $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                        }
                    });
                }

                if($status[0]['status'] == "Closed"){

                    foreach($status as $stat)
                    {
                      $show_data = "Ticket No: ".$stat['ticket_id']." <br> "."Status: ".$stat['status']." <br> ". "Comment: ".$status[0]['ticket_detail']['description']. "<br> <br> Is there something more I can help you with? / Do you want to know more?";
                    }

                    $question_reason = Question::create($show_data)
                    ->fallback('Unable to ask question')
                    ->callbackId('ask_reason');
                    $user_id=Auth::user ()->id;

                    return $this->ask($question_reason, function (Answer $answer) use($ticket,$stat,$user_id) {
                        if($answer->getValue() == "yes"){
                            $this->say("Ok. What is it? Tell me");
                            
                            if($answer->getValue() == "status"){
                                $this->run();
                            }

                        }
                        else if($answer->getValue() == "no")
                        {
                            $user_name = Helpers::user_names(Auth::user ()->id);
                            $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                        }
                    });
                }
            }

            if(count($status) != 1){

                $butt_data=[];
                $ticket = new TicketStatusApiController();
                $status = $ticket->getTicketState(Auth::user()->id);

                foreach($status as $stat)
                {
                    $show_data=$stat['ticket_id']." - ".$stat['status'];

                  array_push($butt_data,  Button::create($show_data)->value($stat['ticket_id']));
                }
             
                $question_reason = Question::create("These are the status reported by you")
                ->fallback('Unable to ask question')
                ->callbackId('ask_reason')
                ->addButtons($butt_data);
                $user_id=Auth::user ()->id;

                return $this->ask($question_reason, function (Answer $answer_reason) use($ticket,$stat,$user_id) {
                    $status = $ticket->getTicketDetails($answer_reason->getValue(),$stat['updated_at'],$user_id);
                    
                    $question_ffeedback = Question::create($status)
                    ->fallback('Unable to ask question')
                    ->callbackId('feedback_quest');


                    $this->ask($question_ffeedback, function (Answer $answer){

                        if ($answer->getValue() == 'yes') {

                            $this->say("Ok. What is it? Tell me");
                        }
                        else if($answer->getValue() == "no")
                        {
                            $user_name = Helpers::user_names(Auth::user ()->id);
                            $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                        }

                    });
                    
                });

            }
        }

        if(empty($status) && !empty($claimstatus)){

            $question_reason = Question::create("What is the claim number?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons($butt_data);
            $user_id=Auth::user ()->id;    

            return $this->ask($question_reason, function (Answer $answer_reason) use($ticket,$user_id) {
                $status = $ticket->getClaimNumber($answer_reason->getValue(),$user_id);

                $question_ffeedback = Question::create($status)
                    ->fallback('Unable to ask question')
                    ->callbackId('feedback_quest');
                
                if(!empty($status)){
                    $this->ask($question_ffeedback, function (Answer $answer){

                        if ($answer->getValue() == 'yes') {
                            $this->say("Ok. What is it? Tell me");
                            if($answer->getValue() == "status"){
                                $this->run();
                            }
                        }
                        else if($answer->getValue() == "no")
                        {
                            $user_name = Helpers::user_names(Auth::user ()->id);
                            $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                        }
                    });
                }
                  
            });

              
        }

        if(!empty($status) && !empty($claimstatus)){

             $question_reason = Question::create("Do you mean Claim status or ticket status?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason');
            $user_id=Auth::user ()->id;    

            return $this->ask($question_reason, function (Answer $answer_type) use($ticket,$user_id) {

                if($answer_type->getValue() == "ticket"){

                    $butt_data=[];
                    $ticket = new TicketStatusApiController();
                    $status = $ticket->getTicketState(Auth::user()->id);

                    if(count($status) == '1'){

                        if($status[0]['status'] == "Open"){
                            foreach($status as $stat)
                            {
                              $show_data = "Ticket No: ".$stat['ticket_id']." <br> "."Status: ".$stat['status']." <br> ". "Comment: ".$status[0]['ticket_detail']['description']. "<br> <br> Is there something more I can help you with? / Do you want to know more?";
                            }
                            $question_reason = Question::create($show_data)
                            ->fallback('Unable to ask question')
                            ->callbackId('ask_reason');
                            $user_id=Auth::user ()->id;
                            return $this->ask($question_reason, function (Answer $answer) use($ticket,$stat,$user_id) {
                                if($answer->getValue() == "yes"){
                                    $this->say("Ok. What is it? Tell me");
                                   if($answer->getValue() == "status"){
                                        $this->run();
                                    }
                                }
                                else if($answer->getValue() == "no")
                                {
                                    $user_name = Helpers::user_names(Auth::user ()->id);
                                    $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                                }
                            });
                        }

                        if($status[0]['status'] == "Closed"){
                            foreach($status as $stat)
                            {
                              $show_data = "Ticket No: ".$stat['ticket_id']." <br> "."Status: ".$stat['status']." <br> ". "Comment: ".$status[0]['ticket_detail']['description']. "<br> <br> Is there something more I can help you with? / Do you want to know more?";
                            }
                            $question_reason = Question::create($show_data)
                            ->fallback('Unable to ask question')
                            ->callbackId('ask_reason');
                            $user_id=Auth::user ()->id;
                            return $this->ask($question_reason, function (Answer $answer) use($ticket,$stat,$user_id) {
                                if($answer->getValue() == "yes"){
                                    $this->say("Ok. What is it? Tell me");
                                    
                                    if($answer->getValue() == "status"){
                                        $this->run();
                                    }

                                }
                                else if($answer->getValue() == "no")
                                {
                                    $user_name = Helpers::user_names(Auth::user ()->id);
                                    $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                                }
                            });
                        }

                        if($status[0]['ticket_detail']['description'] == "" && $status[0]['status'] == "Open"){

                            foreach($status as $stat)
                            {
                              $show_data = "Ticket No: ".$stat['ticket_id']." <br> "."Status: ".$stat['status']." <br> ". "Comment: No Comments <br> <br> Is there something more I can help you with? / Do you want to know more?";
                              
                            }
                            $question_reason = Question::create($show_data)
                            ->fallback('Unable to ask question')
                            ->callbackId('ask_reason');
                            $user_id=Auth::user ()->id;

                            return $this->ask($question_reason, function (Answer $answer) use($ticket,$stat,$user_id) {

                                if($answer->getValue() == "yes"){
                                    $this->say("Ok. What is it? Tell me");
                                    if($answer->getValue() == "status"){
                                        $this->run();
                                    }
                                }
                                else if($answer->getValue() == "no")
                                {
                                    $user_name = Helpers::user_names(Auth::user ()->id);
                                    $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                                }

                            });
                        }
                    }
                    if(count($status) != 1){

                        $butt_data=[];
                        $ticket = new TicketStatusApiController();
                        $status = $ticket->getTicketState(Auth::user()->id);
                        foreach($status as $stat)
                        {
                            $show_data=$stat['ticket_id']." - ".$stat['status'];
                          array_push($butt_data,  Button::create($show_data)->value($stat['ticket_id']));
                        }
                     
                        $question_reason = Question::create("These are the status reported by you")
                        ->fallback('Unable to ask question')
                        ->callbackId('ask_reason')
                        ->addButtons($butt_data);
                        $user_id=Auth::user ()->id;

                        return $this->ask($question_reason, function (Answer $answer_reason) use($ticket,$stat,$user_id) {
                            $status = $ticket->getTicketDetails($answer_reason->getValue(),$stat['updated_at'],$user_id);
                            $question_ffeedback = Question::create($status)
                            ->fallback('Unable to ask question')
                            ->callbackId('feedback_quest');
                            $this->ask($question_ffeedback, function (Answer $answer){

                                if ($answer->getValue() == 'yes') {

                                    $this->say("Ok. What is it? Tell me");
                                }
                                else if($answer->getValue() == "no")
                                {
                                    $user_name = Helpers::user_names(Auth::user ()->id);
                                    $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                                }
                            });
                        });
                    }
                }

                if($answer_type->getValue() == "claim"){
                   
                    $question_reason = Question::create("What is the claim number?")
                    ->fallback('Unable to ask question')
                    ->callbackId('ask_reason');
                    $user_id=Auth::user ()->id;    

                    return $this->ask($question_reason, function (Answer $answer_reason) use($ticket,$user_id) {
                        $status = $ticket->getClaimNumber($answer_reason->getValue(),$user_id);

                        $question_ffeedback = Question::create($status)
                            ->fallback('Unable to ask question')
                            ->callbackId('feedback_quest');
                        
                        if(!empty($status)){
                            $this->ask($question_ffeedback, function (Answer $answer){

                                if ($answer->getValue() == 'yes') {
                                    $this->say("Ok. What is it? Tell me");
                                    
                                    if($answer->getValue() == "status"){
                                        $this->run();
                                    }
                                }
                                else if($answer->getValue() == "no")
                                {
                                    $user_name = Helpers::user_names(Auth::user ()->id);
                                    $this->say("Thank you, Have a nice day I am glad I can help, Thank you! Ok. Let me know if you need something Glad I could help");
                                }
                            });
                        }
                          
                    });
                }

            });
        }
    }
    
    public function botmanStartConversation() {
        $config = [
        // Your driver-specific configuration
        // "telegram" => [
        //    "token" => "TOKEN"
        // ]
        ];

        DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);
     
        $botman = BotManFactory::create($config);
        $botman = resolve('botman');
        $botman->hears('.*(Hi|Hello).*', function ($bot) {
            $bot->startConversation(new ChatbotController);
        });

        // $botman->hears('.*(Status|Issue).*', function ($bot) {
        //     $bot->startConversation(new BotManController);
        // });

        $botman->hears('.*(Status).*', function ($bot) {
            $bot->startConversation(new BotManController);
        });


        // $botman->hears('.*(I want ([0-9]+)).*', function ($bot, $number) {
        //     $number = "100";
        //     $bot->reply('You will get: '.$number);
        // });
         
        $botman->fallback(function($bot) {
            $bot->reply("Sorry, I can't Understand the Language (◐‿◑)﻿ ");
        });

        $botman->listen();
    }

    public function callChatBot() {
        return view('chat');
    }
}
