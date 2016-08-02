@extends('layout.main')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.5/css/AdminLTE.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">

@section('content')
<div class="container">
  <div class="row">
    <h2 align="center">{{$request->type}} Ticket with refrence number of #{{$request->id}} ({{$request->status}})</h2>
  </div>
  <div class="row">
    <ul class="timeline">
      <!-- timeline time label -->
      <?php
      $lastdate = '';
      ?>
      @foreach($messages as $message)

      <!-- Date item -->
      @if(date("F j, Y", strtotime($message->created_at)) !== $lastdate)
      <li class="time-label">
        <span class="bg-green">
          {{date("F j, Y", strtotime($message->created_at))}}
        </span>
      </li>
      <?php
      $lastdate = date("F j, Y", strtotime($message->created_at));
      ?>
      @endif
      <!-- End of date item -->

      <!-- timeline item -->
      <?php
      switch (trim(strtolower($message->text))) {
        case '_new_':
        $title = ' started a new Ticket';
        $text = '';
        $icon = 'fa fa-envelope bg-blue';
        break;

        case 'read':
        $title = ' has read the previous message(s)';
        $text = '';
        $icon = 'fa fa-eye bg-aqua';
        break;

        case 'reminder':
        $title = ' has sent a reminder';
        $text = '';
        $icon = 'fa fa-bell-o bg-aqua';
        break;

        default:
        $title = ' said :';
        $text = $message->text;
        $icon = 'fa fa-comments bg-yellow';

        if ($message->pic){
          $title = ' uploaded a photo';
          $text = $message->text;
          $icon = 'fa fa-camera bg-purple';
        }
        break;
      }
      ?>
      <li>
        <i class="{{$icon}}"></i>
        <div class="timeline-item">
          <span class="time"><i class="fa fa-clock-o"></i> {{date("h:i A", strtotime($message->created_at))}}</span>
          <h3 class="timeline-header"><a href="#">{{$message->name}}</a> {{$title}}</h3>
          @if($text)
          <div class="timeline-body">
            {{$text}}
          </div>
          @endif
        </div>
      </li>
      <!-- END timeline item -->

      @endforeach

      <li>
        <i class="fa fa-circle bg-purple"></i>
      </li>
    </ul>
  </div>

  <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <!-- quick email widget -->
      <div class="box box-info">
        <div class="box-header">
          <i class="fa fa-envelope"></i>
          <h3 class="box-title">
            <font color="gray">Quick Reply</font>
          </h3>
        </div>
        <form method="post" enctype="multipart/form-data">
          <div class="box-body">
            <div class="form-group">
              <input type="text" class="form-control" name="user_name" value="{{Auth::user()->name}}" readonly>
            </div>
            <div class="form-group">
              <textarea class="textarea" placeholder="Message" name="text" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
            </div>
            <div class="form-group">
              <label for="img">Upload a photo </label>
              <input type="file" name="img" id="img">
            </div>
          </div>
          <div class="box-footer clearfix">
            <button type="submit" class="pull-right btn btn-default" id="sendEmail">Send
              <i class="fa fa-arrow-circle-right"></i>
            </button>
          </div>
          <input type="hidden" name="_token" value="{{csrf_token()}}">
        </form>

      </div>
    </div>
  </div>
</div>
@endsection
