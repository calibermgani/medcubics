<input type="hidden" class="js_set_confirm_msg" value='{{ trans("practice/practicemaster/confirmmessage.validation.patients.notes") }}' />

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 no-padding">

    <div class="box box-info no-shadow no-bottom">
        <?php //dd($notes_exist);?>
        @if(!empty($notes_exist))
            @foreach($notes_exist as $notes_exist)
                {!! Form::hidden(@$notes_exist->patient_notes_type,@$notes_exist->content,['id'=>$notes_exist->id, 'class' => "js-exist-notes"]) !!}
            @endforeach
        @endif
        <!-- form start -->
        {!! Form::hidden("exist_id",'', ['id' => 'js-exist-id']) !!}
        <div class="box-body form-horizontal">
            <div class="form-group hide">
                {!! Form::label('Title', 'Title', ['class'=>'col-lg-3 col-md-3 col-sm-3 control-label star']) !!} 
                <div class="col-lg-9 col-md-9 col-sm-12 @if($errors->first('title')) error @endif">
                    {!! Form::text('title',null,['class'=>'form-control','maxlength'=>'100']) !!}
                    {!! $errors->first('title', '<p> :message</p>')  !!}
                </div>
            </div>
            <?php 
				$currnet_page = Route::getFacadeRoot()->current()->uri();
                 $disabled = '';
                 if(strpos($currnet_page, 'edit') !== false) { 
                    $disabled = "disabled"; 
                    ?>
                    {!!Form::hidden('patient_notes_type', @$notes->patient_notes_type)!!}
                <?php  }
            ?> 
            <div class="form-group">
                {!! Form::label('patient_notes_type', 'Type', ['class'=>'col-lg-12 col-md-12 col-sm-12 control-label star']) !!} 
                <div class="col-lg-12 col-md-12 col-sm-12 @if($errors->first('patient_notes_type')) error @endif" style="padding-right: 12px;">
                    {!! Form::select('patient_notes_type',[''=>'-- Select --','alert_notes' => 'Alert Notes','patient_notes' => 'Patient Notes','claim_notes'=>'Claim Notes','statement_notes'=>'Statement Notes'],null,['class'=>'select2 form-control js_patient_notes_type', $disabled]) !!}
                    {!! $errors->first('patient_notes_type', '<p> :message</p>')  !!}
                </div>
            </div>            
            <div class="form-group @if(strpos($currnet_page, 'edit') !== false && $notes->patient_notes_type == 'claim_notes') show @else hide @endif js_claim_note">
                {!! Form::label('Claim Number', 'Claim No', ['class'=>'col-lg-12 col-md-12 col-sm-12 control-label star']) !!}
                <div class="col-lg-12 col-md-12 col-sm-12 @if($errors->first('codecategory_id')) error @endif" style="padding-right: 15px;">
                    <?php if(!empty($claims_id)){
                            $data = array('all' => "All");
                    } else{
                             $data = []; 
                    }
					if(isset($notes->claim_id) && !empty($notes->claim_id)){
						if(Session::get('practice_dbid') == 75){
							$claimsNo = ((strlen($notes->claim_id) == 4) ? "L0".$notes->claim_id : ((strlen($notes->claim_id) == 3) ? "L00".$notes->claim_id : ((strlen($notes->claim_id) == 2) ? "L000".$notes->claim_id : ((strlen($notes->claim_id) == 1) ? "L0000".$notes->claim_id : ((strlen($notes->claim_id) > 4) ? "L".$notes->claim_id : null)))));
						}else{
							$claimsNo = ((strlen($notes->claim_id) == 4) ? "0".$notes->claim_id : ((strlen($notes->claim_id) == 3) ? "00".$notes->claim_id : ((strlen($notes->claim_id) == 2) ? "000".$notes->claim_id : ((strlen($notes->claim_id) == 1) ? "0000".$notes->claim_id : null))));
						}
					}else{
						$claimsNo = null;
					}
					
					?>
                    {!! Form::select('claim_id[]',$data+(array)$claims_id, @$claimsNo,['class'=>'form-control select2', 'id' => 'jsclaimnumber', 'multiple' => 'multiple', $disabled]) !!}
                    {!! $errors->first('claim_id', '<p> :message</p>')  !!}
                </div>                
            </div>
            <div class="form-group app">
                {!! Form::label('Notes', 'Notes', ['class'=>'col-lg-12 col-md-12 col-sm-12 control-label star']) !!} 
                <div class="col-lg-12 col-md-12 col-sm-12 @if($errors->first('content')) error @endif" style="padding-right: 12px;">
                    {!! Form::textarea('content',null,['class'=>'form-control','style'=>'height:120px;', 'id' =>"content"]) !!}
                    {!! $errors->first('content', '<p> :message</p>')  !!}
                    <button id="start-record-btns" title="Start Recording" class="margin-t-5 record-button">Start <i class="fa fa-microphone text-success"></i></button>
                    <button id="pause-record-btns" title="Pause Recording" class="margin-t-5 record-button">Stop <i class="fa fa-microphone-slash text-danger"></i></button>
                    <!--<button id="save-note-btn" title="Save Note">Save Note</button>  --> 
                    <p id="recording-instructionss">Press the <strong>Start Recognition</strong> button and allow access.</p>
                    
                    <!--<h3>My Notes</h3>
                    <ul id="notes">
                        <li>
                            <p class="no-notes">You don't have any notes.</p>
                        </li>
                    </ul>-->
                </div>

            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-center">
                {!! Form::submit($submitBtn, ['class'=>'btn btn-medcubics','accesskey'=>'s']) !!}
                @if(strpos($currnet_page, 'patients') !== false)
                <a href="javascript:void(0)" data-url="{{ url('patients/'. $patients->id.'/notes') }}">
                    {!! Form::button('Cancel', ['class'=>'btn btn-medcubics','data-dismiss'=>'modal']) !!}
                </a>
                @else
                {!! Form::button('Cancel', ['class'=>'btn btn-medcubics', 'data-dismiss'=>'modal']) !!}
                @endif
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
</div><!--/.col (left) -->
<script>
$(document).ready(function() {
setTimeout(function(){   
     $(".js-submit-popupform-notes li.select2-search-choice div").attr("style",'text-overflow: clip');  
     $(".js-submit-popupform-notes li.select2-search-choice div").attr("style",'overflow:visible');
  }, 100);  
});

try {
  var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
  var recognition = new SpeechRecognition();
}
catch(e) {
  console.error(e);
  $('.no-browser-support').show();
  $('.app').hide();
}


var noteTextarea = $('#content');
var instructions = $('#recording-instructionss');
//var notesList = $('ul#notes');

var noteContent = '';

// Get all notes from previous sessions and display them.
var notes = getAllNotes();
renderNotes(notes);



/*-----------------------------
      Voice Recognition 
------------------------------*/

// If false, the recording will stop after a few seconds of silence.
// When true, the silence period is longer (about 15 seconds),
// allowing us to keep recording even when the user pauses. 
recognition.continuous = true;

// This block is called every time the Speech APi captures a line. 
recognition.onresult = function(event) {

  // event is a SpeechRecognitionEvent object.
  // It holds all the lines we have captured so far. 
  // We only need the current one.
  var current = event.resultIndex;

  // Get a transcript of what was said.
  var transcript = event.results[current][0].transcript;

  // Add the current transcript to the contents of our Note.
  // There is a weird bug on mobile, where everything is repeated twice.
  // There is no official solution so far so we have to handle an edge case.
  var mobileRepeatBug = (current == 1 && transcript == event.results[0][0].transcript);

  if(!mobileRepeatBug) {
    noteContent += transcript;
    noteTextarea.val( noteContent.charAt(0).toUpperCase() + noteContent.slice(1));
    $("#js-bootstrap-validator").bootstrapValidator('revalidateField', 'content');
  }
};

recognition.onstart = function() { 
  instructions.text('Voice recognition activated. Try speaking into the microphone.');
}

recognition.onspeechend = function() {
  instructions.text('You were quiet for a while so voice recognition turned itself off.');
}

recognition.onerror = function(event) {
  if(event.error == 'no-speech') {
    instructions.text('No speech was detected. Try again.');  
  };
}


$('#pause-record-btns').hide();
/*-----------------------------
      App buttons and input 
------------------------------*/

$('#start-record-btns').on('click', function(e) {
  if (noteContent.length) {
    noteContent += ' ';
  }
  recognition.start();
  instructions.text('Voice recognition activated. Try speaking into the microphone.');
  $('#pause-record-btns').show();
  $(this).hide();
});


$('#pause-record-btns').on('click', function(e) {
  recognition.stop();
  instructions.text('Voice recognition paused.');
  $('#start-record-btns').show();
  $(this).hide();
});

// Sync the text inside the text area with the noteContent variable.
noteTextarea.on('input', function() {
  noteContent = $(this).val();
})

/*$('#save-note-btn').on('click', function(e) {
  recognition.stop();

  if(!noteContent.length) {
    instructions.text('Could not save empty note. Please add a message to your note.');
  }
  else {
    // Save note to localStorage.
    // The key is the dateTime with seconds, the value is the content of the note.
    saveNote(new Date().toLocaleString(), noteContent);

    // Reset variables and update UI.
    noteContent = '';
    renderNotes(getAllNotes());
    noteTextarea.val('');
    instructions.text('Note saved successfully.');
  }
      
})


notesList.on('click', function(e) {
  e.preventDefault();
  var target = $(e.target);

  // Listen to the selected note.
  if(target.hasClass('listen-note')) {
    var content = target.closest('.note').find('.content').text();
    readOutLoud(content);
  }

  // Delete note.
  if(target.hasClass('delete-note')) {
    var dateTime = target.siblings('.date').text();  
    deleteNote(dateTime);
    target.closest('.note').remove();
  }
});

*/
/*-----------------------------
      Speech Synthesis 
------------------------------*/

function readOutLoud(message) {
    var speech = new SpeechSynthesisUtterance();
  // Set the text and voice attributes.
    speech.text = message;
    speech.volume = 1;
    speech.rate = 1;
    speech.pitch = 1;
  
    window.speechSynthesis.speak(speech);
}

/*-----------------------------
      Helper Functions 
------------------------------*/

function renderNotes(notes) {
  var html = '';
  if(notes.length) {
    notes.forEach(function(note) {
      html+= `<li class="note">
        <p class="header">
          <span class="date">${note.date}</span>
          <a href="#" class="listen-note" title="Listen to Note">Listen to Note</a>
          <a href="#" class="delete-note" title="Delete">Delete</a>
        </p>
        <p class="content">${note.content}</p>
      </li>`;    
    });
  }
  else {
    html = '<li><p class="content">You don\'t have any notes yet.</p></li>';
  }
  //notesList.html(html);
}

function saveNote(dateTime, content) {
  localStorage.setItem('note-' + dateTime, content);
}

function getAllNotes() {
  var notes = [];
  var key;
  for (var i = 0; i<localStorage.length; i++) {
    key = localStorage.key(i);
    if(key.substring(0,5) == 'note-') {
      notes.push({
        date: key.replace('note-',''),
        content: localStorage.getItem(localStorage.key(i))
      });
    } 
  }
  return notes;
}

function deleteNote(dateTime) {
  localStorage.removeItem('note-' + dateTime); 
}
</script>