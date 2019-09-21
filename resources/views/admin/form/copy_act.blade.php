<div class=" {{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>


    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="input-group">
            <a href="javascript:" class="copy-act btn btn-success">{{$act}}</a>

            <div class="input-group" style="display: inline-flex;" >

                <span class="input-group-addon" style="width: 40px;"><i class="fa fa-calendar fa-fw"></i></span>

                <input style="width: 110px;" type="text" id="datetimepicker_from_{{$act}}"
                       class="form-control act floating_date_from" placeholder="Ввод Срок с">


            </div>

            <div class="input-group" style="display: inline-flex;">

                <span class="input-group-addon" style="width: 40px;"><i class="fa fa-calendar fa-fw"></i></span>

                <input style="width: 110px;" type="text" id="datetimepicker_to_{{$act}}"
                       class="form-control act floating_date_from" placeholder="Ввод Срок до">

            </div>

        </div>

        @include('admin::form.help-block')
    </div>
</div>


<script>
    $(document).ready(function () {

/*        function functionName () {
                $.ajax({
                    type: "get",
                    url: "/copy_act?act="+valID+"&from="+valID+"&to="+valID,
                }).success(function( data ) {
                    // location.reload();
                });
            }*/

    });
</script>
