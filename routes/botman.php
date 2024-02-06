 <?php
   use BotMan\BotMan\BotMan;
   use BotMan\BotMan\BotManFactory;
   use BotMan\BotMan\Drivers\DriverManager;
   use App\Http\OnboardingConversations;
   use App\Http\Controllers\ChatbotController;
   use App\Http\Controllers\BotManController;
   use BotMan\BotMan\Messages\Incoming\Answer;

/*
Route::get('/chat', function () {
    return view('chat');
});
*/
Route::any('webhook', 'BotManController@botmanStartConversation');

Route::get('/chat', 'BotManController@callChatBot');

