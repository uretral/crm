<link type="text/css" rel="stylesheet" href="{{ asset('vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css') }}">
<link type="text/css" rel="stylesheet" href="{{ asset('css/helper.css') }}">

{{--@dump($data)--}}
<span class="grid-expand" data-toggle="modal" data-target="#grid-modal-22">
    <a href="javascript:void(0)"></a></span>
<div class="modal fade" id="grid-modal-22" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 99%; height: 99%;">
        <div class="modal-content" style="height:99%;">
            <div class="modal-header" style="min-height: 62px;">
{{--                <a class="close-master-new" href="javascript:;"><span aria-hidden="true">&times;</span></a>--}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="display: inline-block; margin: 0 20px;">{{$data->date}}</h4>
            </div>
            <div class="modal-body">
                <div class="logistic" >
                    <div class="logistic-lost">
                        <div class="logistic-lost-item">
                            <h4>Без адреса</h4>
                            <div class="logistic-lost-item-list">
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>

                            </div>
                        </div>
                        <div class="logistic-lost-item">
                            <h4>Без мастера</h4>
                            <div class="logistic-lost-item-list">
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>
                                <a class="badge warn" href="javascript:;">
                                    <b>34567</b>
                                </a>

                            </div>
                        </div>
                    </div>
                    <div class="logistic-map" >

                        <l-map :zoom="zoom"  :center="center">
                            <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
                            <template v-for="(arOrders,masterKey) in ORDERS" v-if="markerShow || currentMaster === masterKey">
                                <template v-for="(order,orderKey) in arOrders">
                                    <l-marker
                                            :options="{alt: orderKey,riseOnHover:true }"
                                            :lat-lng="setLatLng(order.ADDRESS.UF_GEO_LAT,order.ADDRESS.UF_GEO_LON)"
                                            @click="test()">

                                        <l-tooltip :options="{permanent: true, interactive: true, direction: 'auto'}">
                                            <div @click="innerClick" style="text-align: center;">
                                                :::orderKey::: (:::masterKey:::)
                                                <p v-show="showParagraph && paragraphID === orderKey" v-html="bindText(order)"></p>
                                            </div>
                                        </l-tooltip>


                                    </l-marker>
                                </template>
                            </template>
                        </l-map>
                    </div>
                    <div class="logistic-masters">
                        <input type="hidden" v-model="mapTrigger = ORDERS"/>
                        <!--<a @click="showLongText()" href="javascript:;">dasdasd</a>-->

                        @foreach($data->schedule as $masterID => $master)
                            <div class="logistic-masters-table">
                                <a href="javascript:;"
                                   onclick="showOrdersByMaster({{$masterID}})"
                                   ondblclick="showAllOrders()"
                                ><span>{{$master['name']}}</span></a>
                                <div class="logistic-masters-cells">
                                    @foreach($data->hours as $hour)
                                        <label
                                                class="cell drag-nest"
                                                id="{{$masterID}}_{{$hour}}"
                                                ondragenter="dragEnter()"
                                                ondrop="dragDrop({{$masterID}},{{$hour}})"
                                                ondragover="dragOver()"
                                                ondragleave="dragLeave()"
                                        >
                                            {{$hour}}

                                            @if($master['volumes'])

                                                @foreach($master['volumes'] as  $volume)
                                                    @if($volume['start'] == $hour)
                                                        <span
                                                                id="{{$volume['id']}}"
                                                                style="width:{{$volume['length'] * (18 -1)}}px;"
                                                                class="drag"
                                                                ondragover="dragCancel()"
                                                                ondragstart="dragStart({{$volume['id']}},{{$volume['length']}})"
                                                                draggable="true"
                                                                ondrop="false"
{{--                                                                onclick="stopClick({{$volume['id']}})"--}}
                                                        ></span>
                                                    @endif
                                                @endforeach
                                            @else
                                            @endif

                                            {{--                                            <template v-if="ORDERS">--}}
                                            {{--                                                <template v-if="ORDERS[mKey]" v-for="(o,oK) in ORDERS[mKey]">--}}

                                            {{--                                                </template>--}}

                                            {{--                                            </template>--}}
                                            {{--                                            <template v-if="newOrder.master === mKey  && newOrder.start === hour">--}}
                                            {{--                                                --}}
                                            {{--                                            </template>--}}
                                        </label>
                                    @endforeach

                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
{{--

<div class="l-box" style="display:block;">
    <!-- v-if="modal"-->
    <div class="l-box-inner">
        <a href="javascript:;" class="l-box-close" v-on:click="closeModal()">
            <i class="fa fa-close"></i>
        </a>
        <div class="l-box-content">
            <div class="l-box-header">
                <div class="l-box-header-left">
                    <p>Выбор мастера :::date:::</p>
                </div>
                <div class="l-box-header-right">
--}}
{{--                    <a v-if="!markerShow" class="badge submit" @click="showAllOrders()">Показать все заказы</a>--}}{{--

                </div>
            </div>
            <div class="l-box-body">

            </div>
            <div class="l-box-footer"></div>
        </div>
    </div>
</div>
--}}

{{--<script src="{{ asset('js/jq.js') }}"> </script>--}}

{{--<script src="{{ asset('vendor/laravel-admin/AdminLTE/bootstrap/js/bootstrap.min.js') }}"> </script>--}}

<script>

</script>


<script>
    $(document).on('click','#delete_order',function(){
        $(document).find('#new-order').remove();
        $('.modal-content').find('#save_order').remove();
        $('.modal-content').find('#delete_order').remove();

    });
    $(document).on('dblclick','#new-order',function(){
        $(document).find('#new-order').remove();
        $('.modal-content').find('#save_order').remove();
        $('.modal-content').find('#delete_order').remove();
    });
    function dragStart(orderID, orderLength) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData("Text", event.target.getAttribute('id'));
        event.dataTransfer.setData("Order", orderID);
        event.dataTransfer.setData("Length", orderLength);
        event.dataTransfer.setDragImage(event.target, 0, 0);
        return true;
    }
    function dragEnter() {
        event.preventDefault();
        let elem = event.target;
        if (!elem.classList.contains('drag'))
            elem.classList.add('drag-over');
        return true;
    }
    function dragOver() {
        event.preventDefault();
    }
    function dragLeave() {
        let elem = event.target;
        elem.classList.remove('drag-over');
    }
    function dragCancel() {
        return event.target.className === 'drag';
    }
    function dragDrop(master, hour) {
        if (!event.target.classList.contains('drag')) {

            if (confirm('Подтверждаете изменения?')) {
                let id = event.dataTransfer.getData("Text");
                let order = event.dataTransfer.getData("Order");
                let orderLength = event.dataTransfer.getData("Length");
                let data = {
                    action: 'master',
                    date: this.date,//this.date,
                    order: order,
                    start: hour,
                    duration: orderLength,
                    master: master
                };

                $.ajax({
                    type: "POST",
                    url: "/logistic/map/update",
                    data: { 'data': data}
                }).success(function( data ) {
                    console.log(data);
                });

                // this.mapTrigger = !this.mapTrigger;
                event.target.appendChild(document.getElementById(id));
                event.target.classList.remove('drag-over');



                event.stopPropagation();
                return false;
            } else {
                event.target.classList.remove('drag-over');
                return false;
            }
        } else {
            return false;
        }

    }
    //
    // var date = '2019-05-27';
    //
    // var newOrder = {
    //     start: '',
    //     startTime: '',
    //     end: '',
    //     endTime: '',
    //     master: '',
    //     step: false,
    //     date: date
    // };

    $(document).on('click','.cell',function(){
        var order = $(document).find('.new-order');
        var id = $(this).attr('id');
        var nextMark = id.split('_');

        if(!order.length){
            window.mark = id.split('_');
            $(this).append('<em id="new-order" class="new-order"></em>');
            $('.modal-header').prepend('<a  href="javascript:" id="save_order" class="btn btn-sm btn-success" title="Сохранить">\n' +
                '    <span class="hidden-xs"> Сохранить </span>\n' +
                '</a>\n' +
                '<a href="javascript:" id="delete_order" class="btn btn-sm btn-danger" title="Удалить">\n' +
                '    <span class="hidden-xs">Удалить</span>\n' +
                '</a>');

            window.markBtn = true;
        } else {
            if(nextMark[0] === window.mark[0]){
                var len = nextMark[1] - window.mark[1] + 1;
                document.querySelector('.new-order').style.width = len * 17 + 1 + 'px';

            }
        }
    });




    // $(document).on('dblclick','#new-order',function(){
    //     $(this).detach();
    // });





    function setLatLng(lat,lng){
        return L.latLng(lat,lng)
    };
    function bindText(order){
        let str = '';
        for(let o in order.VOLUMES){
            str +=  order.VOLUMES[o].THING +' '+ order.VOLUMES[o].AREA +' '+ order.VOLUMES[o].UF_VOLUME_AREA +' '+ order.VOLUMES[o].METHOD + '<br>';
        }
        return str;
    };
    function showOrdersByMaster(key){
        this.markerShow = false;
        this.currentMaster = key;
    };
    function showAllOrders(){
        this.markerShow = true;
        this.currentMaster = '';
    };
    function innerClick() {
        alert('Click!');
    };
    function stopClick(oK) {
        this.paragraphID = oK;
        this.showParagraph = !this.showParagraph;
        event.stopPropagation();
    };
    function showLongText() {
        this.showParagraph = !this.showParagraph;
    };
    function fetchMastersData () {
    };
    function closeModal () {
        this.$emit('closeModal')
    };
    function markBusy (arHours) {
        return 'width:' + ((arHours.length * 18) - 2) + 'px';
    };




/*
    function makeOrder (hour, master) {

        // '<em class="new-order" onclick="stopClick()" ondblclick="newOrderMarkRemove()"></em>'

        if (newOrder.step) {
            newOrder.end = hour;
            newOrder.endTime = date + ' ' + hour + ':00:00';
            let len = hour - newOrder.start + 1;
            document.querySelector('.new-order').style.width = len * 17 + len - 2 + 'px';
        } else {
            newOrder.start = hour;
            newOrder.startTime = this.date + ' ' + hour + ':00:00';
            newOrder.master = master;
            newOrder.masterName = 'ssss';
            newOrder.step = true;
        }
    };*/
</script>

{{--
<div class="modal fade" id="grid-modal-22" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                {$html}
            </div>
        </div>
    </div>
</div>
--}}
