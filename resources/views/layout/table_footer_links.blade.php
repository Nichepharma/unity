{{Form::select('rows_per_page',Config::get('rows_per_page'),10,['class'=>'rows_per_page form-control'])}}
<a class="btn btn-default btn-sm fa fa-check-square-o check-all"></a>
<a class="btn btn-sm btn-danger fa fa-trash-o multiaction_btn" data-toggle="tooltip" title="{{translate('main.delete')}}" data-action="delSel"></a>

{{Form::hidden('m_action', '',['id'=>'multi_action_route'])}}
