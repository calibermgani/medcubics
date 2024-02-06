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
use App\Http\Controllers\Medcubics\Api\AdminTicketApiController as AdminTicketApiController;
use App\Http\Helpers\Helpers as Helpers;
use Auth;

$config = [
    // Your driver-specific configuration
    // "telegram" => [
    //    "token" => "TOKEN"
    // ]
];

DriverManager::loadDriver(\BotMan\Drivers\Web\WebDriver::class);

$botman = BotManFactory::create($config);
   
class ChatbotController extends Conversation
{

    private $count=0;

    public function run()
    {
        $this->askReason();
    }

    public function askReason()
    {
        if($this->count == 0) {
            $question_reason = Question::create("What are you looking for?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
            Button::create('Give a Feedback')->value('feedback'),
            Button::create('Raise an issue')->value('issue'),
            Button::create('Get to know Medcubics')->value('knows'),
            ]);
        } else {
            $question_reason = Question::create("Hi again, what are you looking for?")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
            Button::create('Give a Feedback')->value('feedback'),
            Button::create('Raise an issue')->value('issue'),
            Button::create('Get to know Medcubics')->value('knows'),
            ]);
        }

        return $this->ask($question_reason, function (Answer $answer_reason) {
            if ($answer_reason->isInteractiveMessageReply()) {
                if ($answer_reason->getValue() === 'feedback') {
                    $quest_1='We value your feedback. Let me know what you have to say in a single sentence and I will record it.';
                    $quest_2='Thank you for your valuable feedback.';
                    $title ="Feedback";
                } elseif($answer_reason->getValue() === 'issue'){
                    $quest_1='We apologize for the issue. Let me know the issue I will send it across for you.';
                    $quest_2='Thank you for bringing it to our notice. Our team will get intouch with you as soon as possible.';
                    $title ="Issue";
                } elseif($answer_reason->getValue() === 'knows') {
                    $this->say("For More details Visit www.Medcubics.com");
                }
                $reason=$answer_reason->getValue();
                
                if($reason != 'knows') {
                    $question_feedback = Question::create($quest_1)
                                            ->fallback('Unable to ask question')
                                            ->callbackId('feedback_quest');

                    $this->ask($question_feedback, function (Answer $answer) use($quest_2,$title) {

                        $new_ticket = new AdminTicketApiController();
                        $data['usertype'] ='registereduser';
                        $data['name'] ='null';
                        $data['email_id'] ='null';
                        $data['userlist_id'] ='1';
                        $data['title'] =$title;
                        $data['description'] =$answer->getValue();
                        $data['assigneduser_id'] =Auth::user ()->id;
                        $data['sample'] ='Submit';

                        $status = $new_ticket->postTicketApi($data); 
                            $question_ffeedback = Question::create($quest_2)
                                                    ->fallback('Unable to ask question')
                                                    ->callbackId('feedback_quest')
                                                    ->addButtons([
                                                        Button::create('Continue')->value('continue'),
                                                        Button::create('Exit')->value('exit'),
                                                    ]);

                        $this->ask($question_ffeedback, function (Answer $answer){
                            if ($answer->getValue() === 'continue') {
                                $this->count++;
                                $this->run();
                            } else if($answer->getValue() === 'exit') {
                                $user_name = Helpers::user_names(Auth::user ()->id);
                                $this->say("Thank you ".$user_name."!");
                            }
                        });
                    });
                }
            }
        });
    }
}