@include('emails/header')
<div class="body-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px; text-align: left; color: #333333;">
    <br/><br/>{!! nl2br(@$newMessage) !!}<br/><br/>
</div>
@include('emails/footer')