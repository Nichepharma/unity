@extends('layout.main')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.5/css/AdminLTE.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">

@section('content')
<div class="container">
    <div class="row">
                  <ul class="timeline">
                    <!-- timeline time label -->
                    @foreach($messages as $message)
                    <li class="time-label">
                          <span class="bg-red">
                            {{$message->text}}
                          </span>
                    </li>
                    @endforeach
                    <li class="time-label">
                          <span class="bg-red">
                            July 30, 2016
                          </span>
                    </li>
                    <!-- /.timeline-label -->

                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-envelope bg-blue"></i>
                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:05</span>
                        <h3 class="timeline-header"><a href="#">Maher</a> started a new ticket</h3>
                      </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-comments bg-yellow"></i>
                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:10</span>
                        <h3 class="timeline-header"><a href="#">Maher</a> said:</h3>
                        <div class="timeline-body">
                          Please I can't use PGX application
                          I've tried to re-install it but I faild
                          thanks
                        </div>
                      </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-user bg-aqua"></i>

                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:30</span>
                        <h3 class="timeline-header no-border"><a href="#">Customer Survices</a> has read your meassage</h3>
                      </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-comments bg-yellow"></i>
                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:40</span>
                        <h3 class="timeline-header"><a href="#">Customer Survices</a> said:</h3>
                        <div class="timeline-body">
                          Good morning Dr. maher
                          Could you please provide us with a screenshot to the problem
                          Thanks
                        </div>
                      </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-bell-o bg-aqua"></i>
                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:40</span>
                        <h3 class="timeline-header"><a href="#">Customer Survices</a> has sent a reminder:</h3>

                      </div>
                    </li>
                    <!-- END timeline item -->

                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-camera bg-purple"></i>

                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 12:50</span>

                        <h3 class="timeline-header"><a href="#">Maher</a> uploaded new photos</h3>

                        <div class="timeline-body">
                          <img src="http://placehold.it/150x100" alt="..." class="margin">
                        </div>
                      </div>
                    </li>


                    <!-- timeline time label -->
                    <li class="time-label">
                          <span class="bg-green">
                            July 31, 2016
                          </span>
                    </li>
                    <!-- /.timeline-label -->

                    <!-- timeline item -->
                    <li>
                      <i class="fa fa-comments bg-yellow"></i>
                      <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> 09:10</span>
                        <h3 class="timeline-header"><a href="#">Customer Survices</a> said: </h3>
                        <div class="timeline-body">
                          We have solved the problem, you can now try again
                          Thank you for reporting the problem
                        </div>
                      </div>
                    </li>
                    <!-- END timeline item -->

                  </ul>
    </div>
</div>
@endsection
