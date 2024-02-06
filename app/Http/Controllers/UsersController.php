<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class UsersController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}
	public static function send_mail(){ 
        $deta['name'] = "Sridhar";
        $deta['subject'] = "Test";
        $deta['msg'] = "Test Mail";
        $deta['email'] = "sridhar.clds@gmail.com";
        $template='general';
        // $deta = $request->all();
        // dd($deta);
        \Log::info("Send mail function called from common send mail");
        // Send common mail function itself.
        set_time_limit(0);        
        try {            
            $url_info = parse_url(url('/'));        
           // \Log::info('Mail Send Successfully to user '.$deta['email']." Subj:". $deta['subject']."msg".$deta['msg']);   
            // Send mail only for production server condition added.
            if (isset($url_info['host']) && strtolower($url_info['host']) == '35.193.23.45') {
                $tpl = (isset($template) && $template != '' ) ? 'emails.'.$template : 'emails.general';

                if(isset($deta['email']) && $deta['email'] != '') {
                    $to_email   = isset($deta['email']) ? $deta['email'] : "";
                    $to_name    = isset($deta['name']) ? $deta['name'] : $to_email;
                    $cc         = (isset($deta['cc_email'])) ? $deta['cc_email'] : "";                
                    $sub        = isset($deta['subject']) ? $deta['subject'] : "Medcubics";
                    $msg        = isset($deta['msg']) ? $deta['msg'] : "";
                    $attachment = (isset($deta['attachment']) && !empty($deta['attachment']) )? $deta['attachment'] : "";
                    
                    \Mail::send($tpl, ['msg' => $msg], function($message) use ($to_name, $to_email, $sub, $attachment, $cc) {
                        // $message->from('yourEmail@domain.com', 'From name');
                        // $message->cc('bar@example.com')->bcc('bar@example.com');
                        $message->to($to_email, $to_name)->subject($sub);  

                        // If mail sent to admin no needs to have a copy
                        if($to_email != 'admin@medcubics.com')
                            //$message->bcc('admin@medcubics.com');
                                 
                        // Include attachment if provided
                        if($attachment != '')        
                            $message->attach($attachment);        

                        // Handle CC email address if provided.
                        if($cc != ''){
                            // Handle comma separated emails in cc
                            $cc_mails = explode(",", $cc);
                            if(!empty($cc_mails)){
                                foreach ($cc_mails as $cc_id) {
                                    if($cc_id != '') $message->cc($cc_id);
                                }    
                            }
                        }
                    }); 
                    \Log::info('Mail Send Successfully to user '.$to_email." Subj:". $sub);    
                    return true;
                } else {
                    \Log::info("Invalid Send E-Mail call. receiver id not provided");
                } 
            }                   
        } catch (\Exception $e) {
            \Log::info("Error occured while send mail. Message".$e->getMessage() );
            return true;
        } 
        // Helpers::sendMail($data);
        return 'Success';       
        exit;

        // $mail = new PHPMailer(true);                              
        // // Passing `true` enables exceptions
        // try {                                                       
        //     //Server settings
        //     //$mail->SMTPDebug = 3;                                     
        //     // Enable verbose debug output
        //     $mail->isSMTP();                                        
        //     // Set mailer to use SMTP
        //     $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');                       
        //     // Specify main and backup SMTP servers
        //     $mail->SMTPAuth = true; 
            
        //     $mail->SMTPSecure = 'ssl';                                  
        //     // Enable SMTP authentication
        //     $mail->Username = env('MAIL_USERNAME', 'admin@medcubics.com');                 
        //     // SMTP username
        //     $mail->Password = env('MAIL_PASSWORD', 'Cloud%2016!');                           
        //     // SMTP password
        //     $mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');                            
        //     // Enable TLS encryption, `ssl` also accepted
        //     $mail->Port = env('MAIL_PORT', '587');                                    
        //     // TCP port to connect to
            
        //     //Recipients
        //     $mail->setFrom(env('MAIL_USERNAME', 'production@medcubics.com'), 'Medcubics Production');
        //     $mail->addAddress($data['email'], 'developers@clouddesigners.com');     // Add a recipient
        //     $mail->addReplyTo('no-reply@medcubics.com', 'No-reply');
        //     //$mail->addCC('cc@example.com');
        //     //$mail->addBCC('bcc@example.com');

        //     //Attachments
        //     if(!empty($data['attachment'])){
        //         $mail->addAttachment($data['attachment']);         // Add attachments
        //     }
        //     //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        //     // dd($data['msg']);
            
        //     //Content
        //     $mail->isHTML(true);                                  // Set email format to HTML
        //     $mail->Subject = $data['subject'];
        //     $msg = $data['msg'];
        //     $msg = view('name',compact('msg'));
        //     $mail->Body    = $msg;
        //     //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        //     $mail->send();
        //     return 'success';
        // } catch (Exception $e) {
        //     return 'Mailer Error: ' . $mail->ErrorInfo;
        // }
    }

}
