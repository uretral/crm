<style>
    td .form-group {
        margin-bottom: 1px !important;
    }
</style>

<div class="row">
    <div class="{{$viewClass['label']}}"><h4 class="pull-right">{{ $label }}</h4></div>
    <div class="{{$viewClass['field']}}"></div>
</div>

{{--<hr style="margin-top: 0px;">--}}

<div class="has-many-{{$column}} " class="has-many-{{$column}}">
    <div class="has-many-{{$column}}-forms has-many-parent" >

        @foreach($forms as $pk => $form)
{{--            @dump($pk)--}}


            <div class="has-many-{{$column}}-form fields-group flat-group"  rel="{{$pk}}">
                @foreach($form->fields() as $kf => $field)

                    @if((new ReflectionClass($field))->getShortName() == 'TableFlat')
                        @php
                        $tbl            = $field->buildRelatedFlatForms($pk);

                        $headers        = $tbl['headers'];
                        $child_forms          = $tbl['forms'];
                        $template_child       = $tbl['template_child'];
                        $relationName   = $tbl['relationName'];
                        $options        = $tbl['options'];
                        $child_column   = $tbl['child_column'];
                        $child_label    = $tbl['child_label'];
                        $parent_name    = $tbl['parent_name'];

                        @endphp


                        <div class="row">
                            <div class="{{$viewClass['label']}}"><h4 class="pull-right">{{ $child_label }}</h4></div>
                            <div class="{{$viewClass['field']}}">
                                <div class="has-many-table-{{$child_column}}" style="margin-top: 15px;">
                                    <table class="table table-has-many has-many-table-{{$child_column}}">
                                        <thead>
                                        <tr>
                                            @foreach($headers as $header)
                                                <th>{{ $header }}</th>
                                            @endforeach

                                            <th class="hidden"></th>

                                            @if($options['allowDelete'])
                                                <th class="form-group">
                                                    @if($options['allowCreate'])

                                                        <div>
                                                            <div class="add btn {{$child_column}}-form-add-table btn-success btn-sm pull-right">
                                                                <i class="fa fa-save"></i>&nbsp;{{ trans('admin.new') }}</div>
                                                        </div>

                                                    @endif
                                                </th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody class="has-many-table-{{$child_column}}-forms">


                                        @foreach($child_forms as  $child_form)
                                            <tr class="has-many-table-{{$child_column}}-form fields-group">
                                                @php
                                                    $hidden = '';
                                                @endphp


                                                @foreach($child_form->fields() as $child_field)

                                                    @if (is_a($child_field, \Encore\Admin\Form\Field\Hidden::class))
                                                        @php
                                                            $hidden .= $child_field->setElementNameFlat($relationName,$parent_name)->render();
                                                        @endphp
                                                        @continue
                                                    @endif
                                                    <td>{!! $child_field->setLabelClass(['hidden'])->setWidth(12, 0)->setElementNameFlat($relationName,$parent_name)->render() !!}</td>
                                                @endforeach

                                                <td class="hidden dd">{!! $hidden !!}</td>

                                                @if($options['allowDelete'])
                                                    <td class="form-group">
                                                        <div>
                                                            <div class="remove btn btn-warning btn-sm pull-right"><i class="fa fa-trash">&nbsp;</i>{{ trans('admin.remove') }}</div>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach

                                        </tbody>
                                    </table>

{{--                                        <template class="{{$child_column}}-tpl">
                                            <tr class="has-many-table-{{$child_column}}-form fields-group">

                                                {!! $template_child !!}

                                                <td class="form-group">
                                                    <div>
                                                        <div class="remove btn {{$child_column}}-form-remove-table btn-warning btn-sm pull-right"><i class="fa fa-trash">&nbsp;</i>{{ trans('admin.remove') }}</div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>--}}

                                </div>
                            </div>
                        </div>

                    @else
                        {{----}}
                        {!! $field->render() !!}
                    @endif

                @endforeach


                @if($options['allowDelete'])
                    <div class="form-group">
                        <label class="{{$viewClass['label']}} control-label"></label>
                        <div class="{{$viewClass['field']}}">
                            <div class="remove btn btn-danger btn-sm pull-right"><i class="fa fa-trash">&nbsp;</i>{{ trans('admin.remove')}} {{$column}} </div>
                        </div>
                    </div>
                @endif
                <hr>
            </div>

        @endforeach

    </div>

    <template class="{{$column}}-tpl">
        <div class="has-many-{{$column}}-form fields-group" rel="new___PARENT_KEY__">
            {!! $template !!}

            <div class="form-group">
                <label class="{{$viewClass['label']}} control-label"></label>
                <div class="{{$viewClass['field']}}">
                    <div class="remove btn {{$column}}-parent-form-remove btn-danger btn-sm pull-right"><i class="fa fa-trash"></i>&nbsp;{{ trans('admin.remove') }}</div>
                </div>
            </div>
            <hr>
        </div>
    </template>

    @if($options['allowCreate'])
        <div class="form-group">
            <label class="{{$viewClass['label']}} control-label"></label>
            <div class="{{$viewClass['field']}}">
                <div class="add btn {{$column}}-parent-form-add btn-success btn-sm"><i class="fa fa-save"></i>&nbsp;{{ trans('admin.new') }} {{$column}}</div>
            </div>
        </div>
    @endif

</div>
