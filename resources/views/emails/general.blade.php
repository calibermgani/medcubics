@include('emails/header')
<div class="body-text" style="font-family: Helvetica, Arial, sans-serif; line-height: 20px; text-align: left; color: #333333;">
    <br/>{!! nl2br(@$msg) !!}
</div>
@include('emails/footer')