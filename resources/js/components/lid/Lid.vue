<template>
    <div class="Lid">
        <implement-map
            :open="implementMap.init"
            :date="implementMap.date"
            :region="implementMap.region"
            :lat="implementMap.lat"
            :lon="implementMap.lon"
            v-on:closeMap="implementMap.init = false"
            v-on:newOrder="setImplementDates"
        ></implement-map>
        <new-acts
            :open="modal.newLids"
            :helpers="helpers"
            :lid="lidData"
            @closeNewActs="modal.newLids=false"
        ></new-acts>
        <modal v-model="modal.statusHistory" title="История статусов" :footer="false" :dismiss-btn="false">
            <table class="table">
                <thead>
                <tr>
                    <th>Мэнеджер</th>
                    <th>Статус</th>
                    <th>Дата статуса</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="status in lid.statuses">
                    <td>{{helpers.user[status.manager].name }}</td>
                    <td>{{helpers.statuses[status.status].name}}</td>
                    <td>{{status.date}}</td>
                </tr>
                </tbody>
            </table>
        </modal>
        <modal v-model="modal.actionHistory" title="История действий" :footer="false" :dismiss-btn="false">
            <table class="table">
                <thead>
                <tr>
                    <th>Мэнеджер</th>
                    <th>Действие</th>
                    <th>Дата действия</th>
                    <th>Примечание</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="action in lid.actions">

                    <td>{{helpers.user[action.manager].name }}</td>
                    <td>{{helpers.actions[action.action].name}}</td>
                    <td>{{action.action_date}}</td>
                    <td>{{action.action_note}}</td>
                </tr>

                </tbody>
            </table>
        </modal>
        <modal v-model="modal.destination" :title="'Карта расстояний '+suggestions.destination+' км.'"  :footer="false" :dismiss-btn="false" size="lg">
            <div id="destMap" style="height: 500px;"></div>
        </modal>
        <div class="form-inline mse">

    <!--Договор-->
            <div class="form-group mse" :class="checkField(lid.contract)">
                <label for="site">Договор:</label>
                <input type="text" v-model="lid.contract" class="form-control" id="contract" disabled placeholder="Договор">
            </div>
    <!--Сайт-->
            <div class="form-group mse" :class="checkField(lid.site)">
                <label for="site">Сайт:</label>
                <select v-model="lid.site"  @change="save('lid','site',lid.site)" class="form-control" id="site">
                    <option v-for="company in helpers.companies" :value="company.id">{{company.name}}</option>
                </select>
            </div>
    <!--Мэнеджер стартер-->
            <div class="form-group mse" :class="checkField(lid.manager_starter)">
                <label for="manager_starter">Мэнеджер стартер:</label>
<!--                <span>{{}}</span>-->
                <input type="text" v-model="helpers.user[lid.manager_starter].name" class="form-control" id="manager_starter" disabled placeholder="Мэнеджер начавший лид">
            </div>
    <!--Дата обращения -->
            <div class="form-group mse" :class="checkField(lid.date_start)">
                 <label for="date_start">Дата обращения:</label>
                <input type="text" v-model="lid.date_start" class="form-control" id="date_start" disabled placeholder="Дата обращения">
            </div>
    <!--Тип обслуживания-->
            <div class="form-group mse" :class="checkField(lid.servicing)">
                <label for="servicing">Тип обслуживания:</label>
                <select v-model="lid.servicing"  @change="save('lid','servicing',lid.servicing)" class="form-control" id="servicing">
                    <option v-for="servicing in helpers.servicing" :value="servicing.id">{{servicing.name}}</option>
                </select>
            </div>
    <!--Будильник-->
            <fieldset class="attention" rel="Будильник">
            <div class="form-group mse" :class="checkField(lid.action)">
                 <label for="action">Действие: <a href="javascript:" @click="modal.actionHistory = true">История</a></label>
                <select v-model="log.action"  @focusout="save('action','action',log.action)" class="form-control" id="action">
                    <option v-for="action in helpers.actions" :value="action.id">{{action.name}}</option>
                </select>
            </div>
            <div class="form-group mse" :class="checkField(lid.action_date)">
                 <label>Дата и время действия:</label>
                <input type="datetime-local"  v-model="log.actionDate" @focusout="save('action','action_date',log.actionDate)" class="form-control">
            </div>
            <div class="form-group mse" :class="checkField(lid.action_note)">
                 <label for="action_note">Примечание: </label>
                <input type="text" v-model="log.actionNote" @focusout="save('action','action_note',log.actionNote)" class="form-control" id="action_note" style="width: 400px">
            </div>
            </fieldset>
        </div>
<!-- !!!Основные данные-->
    <!--Статус-->
        <div class="form-inline legend mse" rel="Основные данные">
            <fieldset rel="Статус" style="height: 140px;">
                <div class="form-group mse">
                    <label>Мэнеджер: <a href="javascript:" @click="modal.statusHistory = true">История</a></label>
                    <input v-if="lid.statuses[lid.statuses.length - 1]" type="text"  disabled class="form-control" :value="helpers.user[lid.statuses[lid.statuses.length - 1].manager].name" >
                    <input type="text" v-model="user.name" class="form-control" disabled>
                    <input type="hidden" v-model="user.id" class="form-control" >
                </div>

                <div class="form-group mse">
                    <label for="site">Статус:</label>
                    <input v-if="lid.statuses[lid.statuses.length - 1]" type="text"  disabled class="form-control" :value="helpers.statuses[lid.statuses[lid.statuses.length - 1].status].name" >
                    <select @change="logSave('status',log.status,user.id)" v-model="log.status" class="form-control short" >
                        <option v-for="status in helpers.statuses" :value="status.id">{{status.name}}</option>
                    </select>
                </div>
                <div class="form-group mse">
                    <label for="site">Дата статуса:</label>
                    <input v-if="lid.statuses[lid.statuses.length - 1]" type="text"  disabled class="form-control" :value="lid.statuses[lid.statuses.length - 1].updated_at" >
                    <div class="empty_input"></div>
                </div>
            </fieldset>
            <fieldset rel="Клиент">
                <div class="form-group mse">
                     <label>Статус клиента:</label>
                    <dropdown ref="customerStatus" >
                        <btn>{{log.clientStatus[lidData.customer.status]}}</btn>
                        <btn class="dropdown-toggle"><span class="caret"></span></btn>
                        <template slot="dropdown">
                            <li><a @click="lid.customer.status = 0, save('customer','status',0)" role="button">Физический</a></li>
                            <li><a @click="lid.customer.status = 1, save('customer','status',1)" role="button">Юридический</a></li>
                        </template>
                    </dropdown>
                </div>
                <div class="form-group mse" v-if="lid.customer.organization">
                    <label>Организация: <a href="javascript:">Поиск</a></label>
                    <input type="text" v-model="lid.customer.organization" class="form-control" id="customer.organization" style="width: 350px;">
                </div>
                <div class="form-group mse">
                    <label>Имя:</label>
                    <input type="text" v-model="lid.customer.name" class="form-control" id="customer.name" style="width: 200px;">
                </div>
                <div class="form-group mse">
                    <label>Телефон:</label>
                    <masked-input mask="\+\7 (111) 111 11 11" class="form-control" @input="rawVal = arguments[1]" v-model="lid.customer.phone" />
                </div>
                <div class="form-group mse">
                    <label>Телефон доп.:</label>
                    <masked-input mask="\+\7 (111) 111 11 11" class="form-control" @input="rawVal = arguments[1]" v-model="lid.customer.phone_ext" />
                </div>
                <div class="form-group mse">
                    <label>Email:</label>
                    <input type="text" v-model="lid.customer.email" class="form-control" id="customer.email">
                </div>
    <!--ADDRESS-->
                <br/>

                <div class="form-group mse">
                    <label>Адрес:</label>
                    <input type="text" v-model="lid.customer.address" class="form-control" id="customer.address" style="width: 717px;">
                </div>
                <div class="form-group mse">
                    <label>Расстояние:</label>
                    <input type="text" v-model="lid.customer.destination" class="form-control" id="customer.destination">
                </div>

                <div class="form-group mse">
                    <label>Широта:</label>
                    <input type="text" v-model="lid.customer.geo_lat" class="form-control" id="customer.geo_lat">
                </div>
                <div class="form-group mse">
                    <label>Долгота:</label>
                    <input type="text" v-model="lid.customer.geo_lon" class="form-control" id="customer.geo_lon">
                </div>
            </fieldset>

        </div>

        <div class="form-inline legend mse" rel="Документы по договору">
            <div class="form-group mse">
                <label>Договор подписан:</label>
                <input id="lid.contract_signed" v-model="lid.contract_signed" type="checkbox"/>
                <label for="lid.contract_signed" class="check-label"></label>
            </div>
            <div class="form-group mse">
                <label>Договор передан:</label>
                <input id="lid.contract_transferred" v-model="lid.contract_transferred" type="checkbox"/>
                <label for="lid.contract_transferred" class="check-label"></label>
            </div>
            <div class="form-group mse">
                <label>Безнал?:</label>
                <input id="lid.customer_payment" v-model="lid.customer_payment" type="checkbox"/>
                <label for="lid.customer_payment" class="check-label"></label>
            </div>
            <div class="form-group mse">
                <label for="action">Порядок рачсетов:</label>
                <select v-model="lid.payment_rule"  class="form-control" id="lid.payment_rule">
                    <option v-for="payment_rule in helpers.payment_rules" :value="payment_rule.id">{{payment_rule.name}}</option>
                </select>
            </div>
            <div class="form-group mse">
                <label>Условия постоплаты:</label>
                <input type="text" v-model="lid.payment_condition" class="form-control" id="lid.payment_condition">
            </div>
            <div class="form-group mse">
                <label>Файл договора:</label>
                <div class="centered">
                    <label class="file" for="InputFile">ФАЙЛ</label>
                    <input type="file" id="InputFile" @change="uploadFile()">
                </div>
            </div>
        </div>

        <div class="form-inline legend mse" rel="Акты">
            <table class="acts">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Плавающий?</th>
                    <th>Срок с</th>
                    <th>Срок до</th>
                    <th>Работы завершены?</th>
                    <th>Адрес</th>
                    <th>Бух. акт передан?</th>
                    <th>Бух. акт подписан?</th>
                    <th>Бух. акт скан</th>
                    <th>Исп. акт подписан?</th>
                    <th>Исп. акт скан</th>
                    <th><a href="javascript:" class="btn btn-warning" @click="addNewLids()">Добавить</a></th>
                </tr>
                </thead>

                <tbody>

<!--                <tr class="act-content " style="display:unset;">

                    <td colspan="12">
                        <fieldset rel="Настройки">
                            <label>Договор подписан:</label>
                            <input id="act.contract_signed" type="checkbox"/>
                            <label for="act.contract_signed" class="check-label"></label>
                        </fieldset>
                        <fieldset rel="Настройки">
                            <label>Договор подписан:</label>
                            <input id="act.contract_signed" type="checkbox"/>
                            <label for="act.contract_signed" class="check-label"></label>
                        </fieldset>
                        <fieldset rel="Настройки">
                            <label>Договор подписан:</label>
                            <input id="act.contract_signed" type="checkbox"/>
                            <label for="act.contract_signed" class="check-label"></label>
                        </fieldset>

                    </td>
                </tr>-->

                <template v-for="(act,key) in this.lidData.acts">
                    <!--Opened-->
                <tr class="act-header" :id="'head_'+act.id" :class="odd(key)">
                    <td>
                        <template v-if="act.active">
                            <cite>#</cite>
                            <input type="text" v-model="lidData.acts[key].act_nr" class="form-control" style="width:80px; height: 26px;">
                        </template>
                        <template v-else>
                            {{act.act_nr}}
                        </template>
                    </td>

                    <td ><!--Плавающий?-->
                        <template v-if="act.active">
                            <cite>Плавающий?</cite>
                            <input v-model="lidData.acts[key].floating" type="checkbox" :id="'floating_'+key"/>
                            <label class="true-false fa" :for="'floating_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.floating" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>
                    </td>
                    <td class="no-wrap att">
                        <template v-if="act.active">
                            <cite>Срок с</cite>
                            <template v-if="lidData.acts[key].floating">
                                <input type="date" v-model="lidData.acts[key].floating_date_from"/>
                            </template>
                            <template v-else>
                                <input type="date" disabled v-model="lidData.acts[key].floating_date_from"/>
                            </template>

                        </template>
                        <template v-else>
                            {{act.floating_date_from}}
                        </template>
                    </td>
                    <td class="no-wrap att">
                        <template v-if="act.active">
                            <cite>Срок до</cite>
                            <template v-if="lidData.acts[key].floating">
                                <input type="date" v-model="lidData.acts[key].floating_date_to"/>
                            </template>
                            <template v-else>
                                <input type="date" disabled v-model="lidData.acts[key].floating_date_to"/>
                            </template>
                        </template>
                        <template v-else>
                            {{act.floating_date_to}}
                        </template>
                    </td>
                    <td><!--Работы завершены?-->
                        <template v-if="act.active">
                            <cite>Работы завершены?</cite>
                            <input v-model="lidData.acts[key].finished" type="checkbox" :id="'finished_'+key"/>
                            <label class="true-false fa" :for="'finished_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.finished" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>

                    </td>
                    <td class="address">
<!--                    <template v-if="act.active">
                        <cite>Адрес</cite>
                        <input type="text" v-model="lidData.acts[key].address" style="width: 454px"/>
                    </template>
                    <template v-else>{{act.address}}</template>-->
                        {{act.address}}
                    </td>
                    <td><!--Бух. акт передан?-->
                        <template v-if="act.active">
                            <cite>Бух. акт передан?</cite>
                            <input v-model="lidData.acts[key].booking_act_transferred" type="checkbox" :id="'booking_act_transferred_'+key"/>
                            <label class="true-false fa" :for="'booking_act_transferred_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.booking_act_transferred" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>

                    </td>
                    <td><!--Бух. акт подписан?-->
                        <template v-if="act.active">
                            <cite>Бух. акт подписан?</cite>
                            <input v-model="lidData.acts[key].booking_act_signed" type="checkbox" :id="'booking_act_signed_'+key"/>
                            <label class="true-false fa" :for="'booking_act_signed_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.booking_act_signed" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>

                    </td>
                    <td><!--Бух. акт скан-->
                        <template v-if="act.active">
                            <cite>Бух. акт скан</cite>
                            <i v-if="act.booking_act_file" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>
                        <template v-else>
                            <i v-if="act.booking_act_file" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>
                    </td>
                    <td><!--Исп. акт подписан?-->
                        <template v-if="act.active">
                            <cite>Исп. акт подписан?</cite>
                            <input v-model="lidData.acts[key].implement_act_signed" type="checkbox" :id="'implement_act_signed_'+key"/>
                            <label class="true-false fa" :for="'implement_act_signed_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.implement_act_signed" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>

                    </td>
                    <td><!--Исп. акт скан-->
                        <template v-if="act.active">
                            <cite>Исп. акт скан</cite>
                            <i v-if="act.implement_act_file" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>
                        <template v-else>
                            <i v-if="act.implement_act_file" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>
                    </td>
                    <td>
                        <template v-if="act.active">
                            <a href="javascript:" @click="closeAct(key)"  class="btn btn-default">Закрыть</a>
                        </template>
                        <template v-else>
                            <a href="javascript:" @click="openAct(key)"  class="btn btn-default">Открыть</a>
                        </template>
                    </td>

                </tr>

                <tr class="act-content" :id="'body_'+act.id" :class="toggleAct(key)">
                    <td colspan="12">
                        <div class="form-inline legend mse" rel="">
                            <fieldset rel="Geo" class="full-center">
                                <div class="form-group">
                                     <a href="javascript:" @click="setAddress(key)" class="btn btn-default">Основной адрес</a>
                                </div>
                                <div class="form-group drop-address">
                                    <label>Адрес:</label>
                                    <input id="act.address" type="text" v-model="lidData.acts[key].address" class="form-control" style="width: 650px" @keyup="dadataAddress()"/>
                                    <ul style="width: 650px;right: 0;left:unset;">
                                        <li v-for="dadata in suggestions.text"><a @click="dadataAddressClick(dadata,key)" href="javascript:">{{dadata.unrestricted_value}}</a></li>
                                    </ul>
                                </div>
                                <div class="form-group">
                                    <label>Широта:</label>
                                    <input id="act.lat" type="text" v-model="lidData.acts[key].lat" class="form-control" style="width: 110px"/>
                                </div>
                                <div class="form-group">
                                    <label>Долгота:</label>
                                    <input id="act.lon" type="text" v-model="lidData.acts[key].lon" class="form-control" style="width: 110px"/>
                                </div>
                                <div class="form-group">
                                    <label>Расстояние:</label>
                                    <input id="act.destination" type="text" v-model="lidData.acts[key].destination" class="form-control" style="width: 110px"/>
                                </div>
                                <div class="form-group">
                                    <label>Регион:</label>
                                    <input id="act.region" type="text" v-model="lidData.acts[key].region" class="form-control"/>
                                </div>
                            </fieldset>
                            <fieldset rel="Обьёмы" class="full-center">
                                <table class="volumes">

                                    <thead>
                                    <tr>
                                        <th>Предмет работ:</th>
                                        <th>Метод:</th>
                                        <th>Площадь:</th>
                                        <th>Единица площади:</th>
                                        <th>Цена гост</th>
                                        <th>Цена факт</th>
                                        <th>
                                            <a class="add btn btn-success btn-sm pull-right" @click="addVolume(act.id)">
                                                <i class="fa fa-save"></i>&nbsp;Добавить
                                            </a>
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="actRelations[act.id]">
                                    <tr v-for="(volume,key) in actRelations[act.id].volumes">
                                        <td>
                                            <select v-model="volume.pest" class="form-control" @focusout="addActRelation('volume',volume.id,'pest',volume.pest)">
                                                <option v-for="pest in helpers.pests" :value="pest.id">{{pest.name}}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select v-model="volume.method" class="form-control" @focusout="addActRelation('volume',volume.id,'method',volume.method)">
                                                <option v-for="method in helpers.methods" :value="method.id">{{method.name}}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" v-model="volume.square" class="form-control" @focusout="addActRelation('volume',volume.id,'square',volume.square)"/>
                                        </td>
                                        <td>
                                            <select v-model="volume.entity" class="form-control" @focusout="addActRelation('volume',volume.id,'entity',volume.entity)">

                                                <option v-for="square in helpers.square" :value="square.id">{{square.name}}</option>
                                            </select>
                                        </td>
                                        <td><input type="number" v-model="volume.price_standard" class="form-control" @focusout="addActRelation('volume',volume.id,'price_standard',volume.price_standard)"/></td>
                                        <td><input type="number" v-model="volume.price_fact" class="form-control" @focusout="addActRelation('volume',volume.id,'price_fact',volume.price_fact)"/></td>
                                        <td>
                                            <a class="remove btn btn-warning btn-sm pull-right" @click="deleteVolume(act.id,volume.id,key)"><i class="fa fa-trash">&nbsp;</i>Удалить</a>
                                        </td>
                                    </tr>
                                    </tbody>

                                    <tfoot>
                                    <tr>
                                        <th colspan="7"></th>
                                    </tr>
                                    </tfoot>

                                </table>

                            </fieldset>
                            <fieldset rel="Выполнение" class="full-center">
                                <table class="volumes">

                                    <thead>
                                    <tr>
                                        <th>Выбрать мастера(Выберите дату)</th>
                                        <th>Мастер</th>
                                        <th>Начало работ</th>
                                        <th>Окончание Работ</th>
                                        <th>
                                            <a class="add btn btn-success btn-sm pull-right" @click="addImplement(act.id)">
                                                <i class="fa fa-save"></i>&nbsp;Добавить
                                            </a>
                                        </th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="actRelations[act.id]">

                                    <tr v-for="(implement,key) in actRelations[act.id].implements">
                                        <td>
                                            <input type="date" class="form-control" @change="triggerMasterMap(act.id,key)">
                                        </td>
                                        <td>
                                            <select v-model="implement.master" class="form-control" disabled>
                                                <option v-for="master in helpers.masters" :value="master.id">{{master.name}}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="datetime-local" v-model="implement.start_date" class="form-control" disabled>
                                        </td>
                                        <td>
                                            <input type="datetime-local" v-model="implement.end_date" class="form-control" disabled>
                                        </td>

                                        <td>
                                            <a class="remove btn btn-warning btn-sm pull-right" @click="deleteImplement(act.id,implement.id,key)"><i class="fa fa-trash">&nbsp;</i>Удалить</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </fieldset>
                        </div>

                    </td>
                </tr>
                </template>
                </tbody>
                <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>

<script>
    import Axios from 'axios';
    import L from 'leaflet';
    import MaskedInput from 'vue-masked-input'
    import ImplementMap from "../ImplementMap";
    import NewActs from "./NewActs";

    export default {
        name:'Lid',
        props:['lid','acts','user','helpers','statuses','regions'],
        data: function() {
            return {
                lidData: this.lid,
                actRelations: {},
                log: {
                    'status': '',
                    'statusDate': '',
                    'action': '',
                    'actionDate': '',
                    'actionNote': '',
                    'clientStatus': ['Физизический','Юридический']
                },
                salt: 0 ,
                implementMap: {
                    key: '',
                    act: '',
                    date: '',
                    region: '',
                    lat: '',
                    lon: '',
                    init: false,
                },
                settings: {},
                suggestions: {
                    text: '',
                    value: '',
                    destination: '',
                },
                modal: {
                    statusHistory:false,
                    actionHistory:false,
                    destination:false,
                    newLids:true
                }
            }
        },
        watch: {},
        components: {
            MaskedInput,ImplementMap,L,NewActs
        },
        methods: {
           async save(model,field,value,id = false){
                if(value !== '') {
                    try {
                        let response = await Axios.get('/ajax/lid/update_field?id='+this.lid.id+'&model='+model+'&field='+field+'&value='+value+'&manager='+this.user.id+'&salt='+this.salt+'&child_id='+id);
                        response.data > 0 ? this.success(field,'save') : this.danger(field,'save');
                        return response.data;
                    } catch(error) {
                        console.log(error);
                        this.danger(field,'save');
                    }
                }
            },
            logSave(model,value,manager,date='',comment='') {
                Axios.get('/ajax/lid/log?id='+this.lid.id+'&model='+model+'&value='+value+'&manager='+manager+'&salt='+this.salt+'&date='+date+'&comment='+comment)
                    .then(response => {
                        response.data ? this.success(model,'logSave') : this.danger(model,'logSave');
                    })
                    .catch(error => {
                        console.log(error);
                        this.danger(model,'logSave');
                    })
            },
            async addActRelation(model,id,field,value){
                console.log(model,id,field,value);
                try {
                    let response = await Axios.get('/ajax/lid/add_act_relation?model='+model+'&id='+id+'&field='+field+'&value='+value);
                    response.data > 0 ? this.success(field,'addActRelation') : this.danger(field,'addActRelation');
                } catch (error) {
                    console.log(error);
                    this.danger(field,'addActRelation');
                }
            },
            /*---*/
            async dadataAddress(){
              try {
                  let response = await Axios.get('/dadata/curl?a='+event.target.value);
                  this.suggestions.text = response.data.suggestions;
              } catch (error) {
                  console.log(error);
              }
            },
            checkField(val){
                if(val == null || val === 'undefined'){
                    return 'fa flash'
                } else {
                    return 'fa check'
                }
            },
            success (field, finc) {
                this.$notify({
                    duration: 10000,
                    placement: 'bottom-right',
                    type: 'success',
                    title: 'Сохранено!',
                    content: field +' + '+finc
                })
            },
            danger (field, finc) {
                this.$notify({
                    duration: 10000,
                    placement: 'bottom-right',
                    type: 'danger',
                    title: 'Возникла ошибка',
                    content: field +' + '+finc
                })
            },
            rand(){
                return Math.floor(Math.random() * (900000 - 100000 + 1)) + 100000;
            },
            uploadFile(){
                let file = event.target.files[0] || event.dataTransfer.files[0];
                let formData = new FormData();
                console.log(file);
/*                formData.append('image', file);
                Axios.post('upload/', formData)
                    .then(response => {
                        console.log(response)
                    }, error => {
                        console.log(error)
                    })*/
            },
            // Manipulations
/*            openAct(id){
                $('#body_'+id).slideToggle();
                $('#head_'+id).toggleClass('active');
                // let el = document.getElementById(id);
                // el.style.display = (el.style.display == 'none') ? 'block' : 'none';
            },*/
            openAct(key){
                Axios.get('/ajax/lid/get_act_relations?act='+this.lidData.acts[key].id)
                    .then(response => {
                        this.$set(this.actRelations,this.lidData.acts[key].id,response.data);
                    })
                    .catch(error => {
                        console.log(error);
                    });
                this.$set(this.lidData.acts[key],'active',true);
            },
            closeAct(key){
                this.$set(this.lidData.acts[key],'active',false);
            },
            toggleAct(key){
                if(this.lidData.acts[key].active){
                    return 'active';
                }
            },
            odd(key){
                let className = '';
                if(key%2){
                    className += 'odd ';
                }
                if(this.lidData.acts[key].active){
                    className += ' active';
                }
                return className;

            },
            triggerMasterMap(act,implementKey){
                let actData = this.acts[act];
                !actData.region? actData.region = 'Москва': actData.region;
                !actData.lat? actData.lat = '55.77321337302965': actData.lat;
                !actData.lon? actData.lon = '37.50043094158173': actData.lon;
                this.implementMap = {
                    key: implementKey,
                    act: act,
                    date: event.target.value,
                    region: actData.region,
                    lat: actData.lat,
                    lon: actData.lon,
                    init: true,
                }
            },
            setImplementDates(obj) {
                let id = this.actRelations[this.implementMap.act].implements[this.implementMap.key].id;

                this.actRelations[this.implementMap.act].implements[this.implementMap.key].start_date = obj.startTime;
                this.actRelations[this.implementMap.act].implements[this.implementMap.key].end_date = obj.endTime;
                this.actRelations[this.implementMap.act].implements[this.implementMap.key].master = obj.master;
                this.addActRelation('implement',id,'start_date',obj.startTime);
                this.addActRelation('implement',id,'end_date',obj.endTime);
                this.addActRelation('implement',id,'master',obj.master);
                this.implementMap = {
                    key: '',
                    act: '',
                    date: '',
                    region: '',
                    lat: '',
                    lon: '',
                    init: false,
                };

            },
            addVolume(actID){
                Axios.get('/ajax/lid/add_volume?lid='+this.lid.id+'&act='+actID)
                    .then(response => {
                        console.log(response.data);
                        this.actRelations[actID].volumes.push(response.data);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            addImplement(actID){
                Axios.get('/ajax/lid/add_implement?lid='+this.lid.id+'&act='+actID)
                    .then(response => {
                        console.log(response.data);
                        this.actRelations[actID].implements.push(response.data);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            deleteVolume(act,volume,key){
                Axios.get('/ajax/lid/delete_volume?lid='+this.lid.id+'&id='+volume)
                    .then(response => {
                        console.log(response.data);
                        if (response.data){
                            this.actRelations[act].volumes.splice(key,1);
                        } else {
                            this.danger();
                        }

                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            deleteImplement(act,implement,key){
                Axios.get('/ajax/lid/delete_implement?lid='+this.lid.id+'&id='+implement)
                    .then(response => {
                        console.log(response.data);
                        if (response.data){
                            this.actRelations[act].implements.splice(key,1);
                        } else {
                            this.danger();
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            async setAddress(actKey){
                let id =  this.lidData.acts[actKey].id;
                if(await this.save('act','address',this.lidData.customer.address,id) > 0){
                     this.lidData.acts[actKey].address = this.lidData.customer.address;
                }

                if(await this.save('act','lat',this.lidData.customer.geo_lat,id)){
                     this.lidData.acts[actKey].lat = this.lidData.customer.geo_lat;
                }

                if(await this.save('act','lon',this.lidData.customer.geo_lon,id)){
                     this.lidData.acts[actKey].lon = this.lidData.customer.geo_lon;
                }

                if(await this.save('act','destination',this.lidData.customer.destination,id)){
                     this.lidData.acts[actKey].destination = this.lidData.customer.destination;
                }

                if(await this.save('act','region',this.lidData.customer.region,id)){
                     this.lidData.acts[actKey].region = this.lidData.customer.region;
                }

            },
            /*---*/
           async dadataAddressClick(dadata,key){
                this.suggestions.text = '';
                this.lidData.acts[key].address = dadata.unrestricted_value;
                if(dadata.data.geo_lat && dadata.data.geo_lon){

                    let id =  this.lidData.acts[key].id;
                    if( this.save('act','address',dadata.unrestricted_value,id) > 0){
                        this.lidData.acts[key].address = dadata.data.address;
                    }

                    if( this.save('act','lat',dadata.data.geo_lat,id) > 0){
                        this.lidData.acts[key].lat = dadata.data.geo_lat;
                    }

                    if( this.save('act','lon',dadata.data.geo_lon,id) > 0){
                        this.lidData.acts[key].lon = dadata.data.geo_lon;
                    }

                    if( this.save('act','region',dadata.data.region,id) > 0){
                        this.lidData.acts[key].region = dadata.data.region;
                    }

                    let region = this.regions[dadata.data.region];
                    await this.OSMLayers(dadata.data.geo_lat,dadata.data.geo_lon,region.center_lat,region.center_lon);


                    if(this.save('act','destination',this.suggestions.destination,id)){
                        this.lidData.acts[key].destination = this.suggestions.destination;
                    }
                }
            },
            /*---*/
            async OSMLayers(lat,lon,flat,flon){
                console.log(lat,lon,flat,flon);
                try {
                    let response = await Axios.get('/dadata/osm?lat='+lat+'&lon='+lon+'&flat='+flat+'&flon='+flon);
                    if(response.data){
                        this.modal.destination = true;
                        let map = await L.map('destMap');
                        map.setView([flat, flon], 11);
                        L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager/{z}/{x}/{y}.png').addTo(map);

                        let u = [];
                        for (let i in response.data.coordinates) {
                            u[i] = [response.data.coordinates[i][1], response.data.coordinates[i][0]]
                        }
                        let polyline = L.polyline(u, {color: 'red'}).addTo(map);
                        map.fitBounds(polyline.getBounds());
                        this.suggestions.destination = response.data.properties.distance;
                    }
                } catch (error) {
                    console.log(error);
                }
            },
            addNewLids(){
                this.modal.newLids = true;
            },



        },
        computed:{

        },
        mounted() {
            // this.init();
            this.salt = this.rand();
            // this.lid.customer.status = this.$refs.dropdown.$el;
        }
    };
</script>


<style>
    input[type="text"],input[type="number"] {
        text-indent: 10px;
    }
    .full-center {
        width: 100%;
        text-align: center;
        border-color:transparent ;
    }
    .full-center:before {
        content: attr(rel);
        display: inline-block;
        position: absolute;
        top: -11px;
        left: 18px;
        padding: 0 5px;
        background: #ecf0f5;
        color: #aaa;
        z-index: 200;
    }
    .full-center:after {
        content: '';
        display: none;
    }
    .acts {
        width: 100%;
    }
    .acts .att {
        /*color: white;*/
        /*font-weight: bolder;*/
        text-shadow: 2px 2px 4px rgb(255, 255, 255);
    }
    .acts th, .acts td {
        padding: 3px 5px;
        position: relative;
    }
    .acts thead th {
        text-align: center;
        height: 20px;
    }

    .acts td cite {
        position: absolute;
        top: -13px;
        left: 2%;
        width: 96%;
        /*background: #e4e4e4;*/
        display: block;
        border-radius: 3px;
        font-size: 12px;
        color: #fff;
    }

    .acts thead {
        /*position: fixed;*/
        /*width: 1812px;*/
    }
    .acts .legend {
        border-color: transparent;
    }
    .no-wrap {
        white-space: nowrap;
    }
    .address {
        width: 350px;
    }
    .act-header {
        /*background: #3c8dbc;*/
        background: #607d8b78;
        height: 40px;
        text-align: center;
        color: #000;
        border-top: 3px solid #ecf0f5;

        border-bottom: 3px solid #ecf0f5;

    }
    .act-header.odd {
        background: #607d8b63;
    }
    .act-header.odd.active {

    }
    .act-header.odd + tr {
        /*background: #ffffffc4;*/
    }
    .act-header.active {
        background: #3c8dbc;
    }
    .act-header.active td {
        border-top: 40px solid transparent;
    }
    .act-content {
        display: none;
        /*background: white;*/
    }
    .act-content.active {
        display: table-row;
    }
    .Lid  {
        font-family: 'Source Sans Pro','Helvetica Neue',sans-serif;
    }
    label,input,select {
        font-family: 'Source Sans Pro','Helvetica Neue',sans-serif!important;
    }
    label {
        font-weight: 200;
        opacity: .6;
    }
    input {
        font-weight: normal;
    }
    select.short {
        width: 160px!important;
    }
    .input {
        width: 160px!important;
        margin-right: 0!important;
    }
    .form-group {
        margin-right: 10px;
        position: relative;
    }
    .form-group label {
        margin-right: 3px;
    }
    i.yes {}
    i.no {}
    i.not {}

    i.not:before {
        content: "";
        color: green;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 20px;
        height: 20px;
        background: #c9d6dc;
        border-radius: 50%;

    }
    i.yes:before {
        content: "\f00c";
        color: green;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 20px;
        height: 20px;
        background: #c9d6dc;
        border-radius: 50%;

    }
    i.no:before {
        content: "\f0e7";
        color: red;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 20px;
        height: 20px;
        background: #c9d6dc;
        border-radius: 50%;
    }
    .form-group.check:before {
        content: "\f00c";
        color: green;
        position: absolute;
        bottom: 9px;
        right: 5px;
        z-index: 10;
    }
    .form-group.flash:before {
        content: "\f0e7";
        color: red;
        position: absolute;
        bottom: 9px;
        right: 5px;
        z-index: 10;
    }
    fieldset {
        display: inline-block;
        border: 1px dotted #d2d6de;
        padding: 10px;
        position: relative;
        vertical-align: top;

    }
    fieldset.attention {
        box-shadow:         inset 3px 3px 26px 9px rgba(50, 50, 50, 0.05);
    }
    fieldset.attention:after {
        color: #ff0000;
    }
    fieldset:after {
        content: attr(rel);
        display: inline-block;
        position: absolute;
        top: -11px;
        right: 18px;
        padding: 0 5px;
        background: #ecf0f5;
        color: #aaa;
        z-index: 200;
    }
    .empty_input {
        width: 163px;
        height: 36px;
    }
    .flatpickr-current-month {
        font-size: 100%!important;
    }
    .flatpickr-monthDropdown-months {
        height: 28px!important;
        width: 70%!important;
    }
    .numInputWrapper {
        width: 30%!important;
    }



    input[type="checkbox"] {
        display: none!important;
    }
    .true-false {
        width: 20px;
        height: 20px;
        display: block;
        font: normal normal normal 14px/1 FontAwesome!important;
        margin: 0 auto;
        padding: 0;
        cursor: pointer;
        position: relative;
    }
    input[type="checkbox"] + .true-false:after {
        content: "";
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 20px;
        height: 20px;
        background: #fff!important;
        border-radius: 50%;
        font-size: inherit;
        position: absolute;
        z-index: 2;
        left: 0;

    }
    input[type="checkbox"]:checked + .true-false:after {
        content: "\F00C";
    }

    input[type="checkbox"] + .check-label {
        display: block;
        width: 54px;
        height: 32px;
        border-radius: 4px;
        border: 1px solid #ccc;
        position: relative;
        opacity: 1;
        padding: 0;
        margin: 0 auto;
        background: #fff;
    }
    input[type="checkbox"] + .check-label:after {
        content: 'Нет';
        display: flex;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: -1px;
        right: -2px;
        width: 32px;
        height: 32px;
        background: #eee;
        font-size: 12px;
        border-radius: 4px;
        color: #3c8dbc;
        border: 1px solid #ccc;

    }
    input[type="checkbox"]:checked + .check-label:after {
        content: 'Да';
        left: -2px;
        background: #3c8dbc;
        color: #fff;
    }
    .centered {
        display: flex;
        align-items: center;
        height: 34px;
    }

    .volumes {
        width: 100%;
        text-align: left;
    }
    .volumes thead {
        border-bottom: 2px solid #000;

    }
    .volumes thead th {
        text-align: left;
    }
    .volumes thead th:last-of-type {
        text-align: right;
    }
    .volumes thead {}

    .volumes tbody td:last-of-type {
        text-align: right;
    }
    .volumes tbody td {
        text-align: left;
    }
    .volumes tbody {}
</style>
<!-- рудницкая тетрадь для контрольных работ -->
