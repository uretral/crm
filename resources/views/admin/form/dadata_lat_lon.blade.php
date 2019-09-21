<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    @inject('cities', 'App\Models\Helper\Region')
    @php
        $citiesArray = $cities->cities();
    @endphp

    {{--@dump($points)--}}
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')
        <a href="javascript:" rel="{{$name}}" class="btn current_address">Основной адрес</a>
        <br/>
        <br/>
        <div class="input-group drop-address">

            <span class="input-group-addon"><a href="javascript:;"><i
                        class="fa fa-map-marker fa-fw"></i></a></span>
            <input type="text" class="{{$class}} form-control" name="{{$name}}"
                   value="{{$value}}" {!! $attributes !!} />
            <ul></ul>

        </div>
        @include('admin::form.help-block')

    </div>
</div>
<script>
    $(document).ready(function () {
        window.points = JSON.parse('{!!$points!!}');
    });

    {{--var citiesArray = @json($citiesArray);--}}
    var name;
    var lonInput;
    var latInput;
    var regionInput;


    $(document).on('keyup', '[name="{{$name}}"]', function () {
        $('#map_{{$name}}').remove();
        var str = $(this).val();
        var nest = $(this).next('.drop-address ul');
        var list = '';
        if (str) {
            $.ajax({
                type: "get",
                url: "/dadata/curl",
                data: {
                    'a': str,
                    "_token": "{{ csrf_token() }}"
                }
            }).success(function (data) {
                window.addr = JSON.parse(data).suggestions;
                if (window.addr) {
                    for (var i = 0, len = window.addr.length; i < len; i++) {
                        list += `<li><a rel="` + i + `" href="javascript:;">` + window.addr[i].unrestricted_value + `</a></li>`;
                    }
                }
                $(nest).html(list);

                $('.drop-address li a').on('click', function () {
                    var dadataAddress = window.addr[$(this).attr('rel')];
                    $('[name="{{$name}}"]').val(dadataAddress.unrestricted_value);
                    if(dadataAddress.data.geo_lat){
                        name = '{{$name}}';
                       lonInput = name.replace(new RegExp("address",'g'),"lon");
                        latInput = name.replace(new RegExp("address",'g'),"lat");
                        regionInput = name.replace(new RegExp("address",'g'),"region");
                        $('[name="'+latInput+'"]').val(dadataAddress.data.geo_lat);
                        $('[name="'+lonInput+'"]').val(dadataAddress.data.geo_lon);
                        $('[name="'+regionInput+'"]').val(dadataAddress.data.region).trigger('change');
                        alert('Геоданные установлены')
                    }
                    $(nest).html('');
                });

            });
        }
    });


    function defineName(a,b){
       return  a.replace(new RegExp("address",'g'),b);
    }

    $(document).on('click','.current_address',function(){
        var currentName = $(this).attr('rel');
        if($('[name="customer[address]"]').val()) {
            $('[name="'+currentName+'"]').val($('[name="customer[address]"]').val());
            $('[name="'+defineName(currentName,'lat')+'"]').val($('[name="customer[geo_lat]"]').val());
            $('[name="'+defineName(currentName,'lon')+'"]').val($('[name="customer[geo_lon]"]').val());
            $('[name="'+defineName(currentName,'region')+'"]').val($('[name="customer[region]"]').val()).trigger('change');
            // alert('Геоданные установлены');
        } else {
            alert('Геоданные отсутствуют, заполните адрес на вкладке "Данные клиента"');
        }




    });





</script>

{{--// unrestricted_value
// city
// region
// geo_lat
// geo_lon--}}
