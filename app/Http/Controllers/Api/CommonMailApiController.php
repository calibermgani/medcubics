<?php namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Response;
use Request;
use View;
use App\Http\Helpers\Helpers as Helpers;
use App;
use Auth;
use Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CommonMailApiController extends Controller {
	
	public static function common_send_mail($data){ 
		\Log::info("Send mail function called from common send mail");
		// Send common mail function itself.
		Helpers::sendMail($data);
		return 'Success';		
		exit;

		$mail = new PHPMailer(true);                              
		// Passing `true` enables exceptions
		try {														
			//Server settings
			//$mail->SMTPDebug = 3;                                 	
			// Enable verbose debug output
			$mail->isSMTP();                                      	
			// Set mailer to use SMTP
			$mail->Host = env('MAIL_HOST', 'smtp.gmail.com');  						
			// Specify main and backup SMTP servers
			$mail->SMTPAuth = true; 
			
			$mail->SMTPSecure = 'ssl';                               	
			// Enable SMTP authentication
			$mail->Username = env('MAIL_USERNAME', 'admin@medcubics.com');                 
			// SMTP username
			$mail->Password = env('MAIL_PASSWORD', 'Cloud%2016!');                           
			// SMTP password
			$mail->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');                            
			// Enable TLS encryption, `ssl` also accepted
			$mail->Port = env('MAIL_PORT', '587');                                    
			// TCP port to connect to
			
			//Recipients
			$mail->setFrom(env('MAIL_USERNAME', 'production@medcubics.com'), 'Medcubics Production');
			$mail->addAddress($data['email'], 'developers@clouddesigners.com');     // Add a recipient
			$mail->addReplyTo('no-reply@medcubics.com', 'No-reply');
			//$mail->addCC('cc@example.com');
			//$mail->addBCC('bcc@example.com');

			//Attachments
			if(!empty($data['attachment'])){
				$mail->addAttachment($data['attachment']);         // Add attachments
			}
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			
			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = $data['subject'];
			$msg = $data['msg'];
			$msg = view('emails/general',compact('msg'));
			$mail->Body    = $msg;
			//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			return 'Success';
		} catch (Exception $e) {
			return 'Mailer Error: ' . $mail->ErrorInfo;
		}
	}
	
}
