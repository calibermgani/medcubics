<table class="main">
    <tr>
        <td valign="top">
            <div class="border">
                Live Webcam<br>                           
            </div>
            <br/><input type="button" class="snap" value="SNAP IT" id="js-snap">
        </td>
        <div id="webcam"></div>
        <td width="50">&nbsp;</td>
        <td valign="top">
            <div id="upload_results" class="border img-border">
                Snapshot<br>           
            </div>
        </td>
    </tr>
</table> 
<?php $api_url = url('/api/getwebcamimage/').$type;?>
{!! Form::hidden('apiurl',$api_url,['id' => 'apiurl']) !!}
{!! Form::hidden('error-cam',null,['id' => 'error-cam'])!!}     