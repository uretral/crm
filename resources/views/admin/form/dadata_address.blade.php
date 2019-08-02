<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">
    <label for="{{$id}}" class="{{$viewClass['label']}} control-label">{{$label}}</label>
    @inject('cities', 'App\Models\Helper\Region')
    @php
        $citiesArray = $cities->cities();
    @endphp

    {{--@dump($points)--}}
    <div class="{{$viewClass['field']}}">
        @include('admin::form.error')

        <div class="input-group drop-address" style="position: relative;">
            <div id="mapContainer">
                <div class="under" id="destinationMap"></div>
            </div>

            <span class="input-group-addon"><a id="toggleShow" href="javascript:;"><i
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
</script>

<script>

    let citiesArray = @json($citiesArray);

    $(document).on('keyup', '[name="{{$name}}"]', function () {
        $('#destinationMap').remove();
        var str = $(this).val();
        var nest = $('.drop-address ul');
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
                    $('[name="customer[city]"]').val(dadataAddress.data.city);
                    $('[name="customer[region]"]').val(dadataAddress.data.region);
                    window.city = dadataAddress.data.city;
                    window.region = dadataAddress.data.region;
                    window.lat = citiesArray[window.city].center_lat;
                    window.lon = citiesArray[window.city].center_lon;
                    $('[name="center_lat"]').val(window.lat);
                    $('[name="center_lon"]').val(window.lon);


                    for (var p = 0, len = window.points.length; p < len; p++) {
                        if (dadataAddress.data.region === window.points[p].region || dadataAddress.data.region === window.points[p].region) {
                            centerLat = window.points[p].center_lat;
                            centerLon = window.points[p].center_lon;
                        }
                    }


                    if (dadataAddress.data.geo_lat && dadataAddress.data.geo_lon) {
                        $.ajax({
                            type: "get",
                            url: "/dadata/osm",
                            data: {
                                'lat': dadataAddress.data.geo_lat,
                                'lon': dadataAddress.data.geo_lon,
                                'flat': window.lat,
                                'flon': window.lon,
                                "_token": "{{ csrf_token() }}"
                            }
                        }).success(function (data) {
                            console.log(data);

                            var osm = JSON.parse(data);
                            map(osm.coordinates);

                            $('#destinationMap').toggleClass('under', 'over');
                            $('[name="customer[destination]"]').val(osm.properties.distance);

                        });
                    }

                    $('[name="customer[geo_lat]"]').val(dadataAddress.data.geo_lat);
                    $('[name="customer[geo_lon]"]').val(dadataAddress.data.geo_lon);
                    $(nest).html('');
                });

            });
        }
    });

    $(document).on('click', '#toggleShow', function () {
        $('#destinationMap').toggleClass('under', 'over');
    });


    function map(coord) {
        document.getElementById('mapContainer').innerHTML = '<div class="under" id="destinationMap"></div>';
        let map = L.map('destinationMap');
        map.setView([55.7422, 37.5719], 11);
        L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png').addTo(map);

        let u = [];
        for (let i in coord) {
            u[i] = [coord[i][1], coord[i][0]]
        }
        let polyline = L.polyline(u, {color: 'red'}).addTo(map);
        map.fitBounds(polyline.getBounds());
    }

</script>

{{--// unrestricted_value
// city
// region
// geo_lat
// geo_lon--}}
