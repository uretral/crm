<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
{{--    @dump($attributes)--}}

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group">

            @if ($prepend)
            <span class="input-group-addon">{!! $prepend !!}</span>
            @endif

            <input {!! $attributes !!} />

            @if ($append)
                <span class="input-group-addon clearfix">{!! $append !!}</span>
            @endif

        </div>

        @include('admin::form.help-block')

    </div>
</div>
