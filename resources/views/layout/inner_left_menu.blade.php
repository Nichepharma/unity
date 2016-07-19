<div class="col-md-2 col-xs-12 leftpart" id="listmenu">
    <a href="@if(isset($headUrl)) {{url($headUrl)}} @else {{url($url)}}s @endif"
       @if(!isset($active)) class="active" @endif>ALL {{$title}}</a>
       @if(isset($items) and !empty($items))
          @foreach($items as $id => $name)
            <a href="{{url($url.'?area='.$name->id)}}" @if(isset($active) && $active==$name->id) class="active" @endif>{{$name->name}}</a>
           @endforeach
       @endif

</div>
