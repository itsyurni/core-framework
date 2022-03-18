<?php
    // $mail = new mailer([
    //     "username" => "harizi.harizi@gmail.com",
    //     "password" => ""
    // ]);
  
    // $mail->SetFrom("harizi.harizi@gmail.com");
    // $mail->to([
    //     "elitedz95@gmail.com" => ""
    // ]);
    // $mail->Subject("Test Message !");
    // $mail->ReplyTo('replyto@gmail.com', 'Secure Developer');
    // $mail->Message("Hello world !");
    // if($mail->send()){
    //     echo "Sent !";
    // }else{
    //     echo "Error";
    // }
namespace yurni\framework\Http;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
class mailer {
    private PHPMailer $mail;
    public function __construct(array $auth){
        $this->mail = new PHPMailer(); 
        $this->mail->IsSMTP(); 
        $this->mail->SMTPDebug = false; 
        $this->mail->SMTPAuth = true; 
        $this->mail->SMTPSecure = 'ssl'; 
        $this->mail->Host = "smtp.gmail.com";
        $this->mail->Port = 465; 
        $this->mail->IsHTML(true);
        $this->mail->Username = $auth["username"] ?? null;
        $this->mail->Password = $auth["password"] ?? null;
    }
    public function setFrom(string $email,string $name = ""){
        $this->mail->setFrom($email, $name);
        return $this;
    }
    public function to(array $recipients){
         foreach($recipients as $email => $name)
         {
            $this->mail->AddCC($email, $name);
         }
    }

    public function Subject($subj){
        $this->mail->Subject = $subj;
        return $this;
    }

   
   public function message($html,$alt=""){
        $this->mail->msgHTML($html);
        $this->mail->AltBody = $alt;
        return $this;
   }
   
   public function send(){
        if (!$this->mail->send()) {
            return false;
        } else {
            return true;
        }
      

   }

}