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
            :actsCount="lidData.acts.length"
            @closeNewActs="modal.newLids=false"
            @updateActs="updateActs"
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
                <template v-if="lid.statuses.length">
                    <tr v-for="status in lid.statuses">
                        <td>{{helpers.user[status.manager].name }}</td>
                        <td>{{helpers.statuses[status.status].name}}</td>
                        <td>{{status.date}}</td>
                    </tr>
                </template>

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
            <div class="form-group mse">
                <label for="site">Договор:</label>
                <input type="text" v-model="lid.contract" class="form-control" id="contract" disabled placeholder="Договор">
            </div>
    <!--Сайт-->
            <div class="form-group mse">
                <label for="site">Сайт:</label>
                <select v-model="lid.site"  @change="save('lid','site',lid.site)" class="form-control" id="site">
                    <option v-for="company in helpers.companies" :value="company.id">{{company.name}}</option>
                </select>
            </div>
    <!--Мэнеджер стартер-->
            <div class="form-group mse">
                <label for="manager_starter">Мэнеджер стартер:</label>
<!--                <span>{{}}</span>-->
                <input type="text" v-model="helpers.user[lid.manager_starter].name" class="form-control" id="manager_starter" disabled placeholder="Мэнеджер начавший лид">
            </div>
    <!--Дата обращения -->
            <div class="form-group mse">
                 <label for="date_start">Дата обращения:</label>
                <input type="text" v-model="lid.date_start" class="form-control" id="date_start" disabled placeholder="Дата обращения">
            </div>
    <!--Тип обслуживания-->
            <div class="form-group mse" >
                <label for="servicing">Тип обслуживания:</label>
                <select v-model="lid.servicing"  @change="save('lid','servicing',lid.servicing)" class="form-control" id="servicing">
                    <option v-for="servicing in helpers.servicing" :value="servicing.id">{{servicing.name}}</option>
                </select>
            </div>
    <!--Будильник-->
            <fieldset class="attention" rel="Будильник">
            <div class="form-group mse" >
                 <label for="action">Действие: <a href="javascript:" @click="modal.actionHistory = true">История</a></label>
                <select v-model="log.action"  @focusout="save('action','action',log.action)" class="form-control" id="action">
                    <option v-for="action in helpers.actions" :value="action.id">{{action.name}}</option>
                </select>
            </div>
            <div class="form-group mse" >
                 <label>Дата и время действия:</label>
                <input type="datetime-local"  v-model="log.actionDate" @focusout="save('action','action_date',log.actionDate)" class="form-control">
            </div>
            <div class="form-group mse" >
                 <label for="action_note">Примечание: </label>
                <input type="text" v-model="log.actionNote" @focusout="save('action','action_note',log.actionNote)" class="form-control" id="action_note" style="width: 390px">
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
                    <select v-model="customerData.status" @click="save('customer','status',customerData.status,customerData.id)" class="form-control" >
                        <option :value="0">Физический</option>
                        <option :value="1">Юридический</option>
                    </select>
                </div>
                <div class="form-group mse" v-if="customerData.status">
                    <label>Организация: <a href="javascript:" @click="customerSearch('organization')">Поиск</a></label>
                    <input type="text" v-model="customerData.organization" class="form-control" id="customer.organization" style="width: 350px;"  @focusout="save('customer','organization',customerData.organization,customerData.id)" >
                </div>
                <div class="form-group mse">
                    <label>Имя:</label>
                    <input type="text" v-model="customerData.name" class="form-control" id="customer.name" style="width: 200px;" @focusout="save('customer','name',customerData.name,customerData.id)">
                </div>
                <div class="form-group mse">
                    <label>Телефон: <a href="javascript:" @click="customerSearch('phone')">Поиск</a></label>
                    <input type="text" class="form-control tel" v-model="customerData.phone">
                </div>
                <div class="form-group mse">
                    <label>Телефон доп.:<a href="javascript:" @click="customerSearch('phone_ext')">Поиск</a></label>
                    <input type="text" class="form-control tel" v-model="customerData.phone_ext">
                </div>
                <div class="form-group mse">
                    <label>Email:<a href="javascript:" @click="customerSearch('email')">Поиск</a></label>
                    <input type="text" v-model="customerData.email" class="form-control" id="customer.email" @focusout="save('customer','email',customerData.email,customerData.id)">
                </div>
    <!--ADDRESS-->
                <br/>

                <dadata-address
                    :mse="true"
                    :main_btn="false"

                    :main="{
                                        address:customerData.address,
                                        lat:customerData.lat,
                                        lon:customerData.lon,
                                        destination:customerData.destination,
                                        region:customerData.region,
                                        }"
                    :regions="helpers.regions"
                    @setAddress="setMainAddress"
                    @saveAddressEntity="saveMainAddressEntity"
                    @mainAddress="mainMainAddress"
                />

<!--                <div class="form-group mse">
                    <label>Адрес:</label>
                    <input type="text" v-model="customerData.address" class="form-control" id="customer.address" style="width: 717px;">
                </div>
                <div class="form-group mse">
                    <label>Расстояние:</label>
                    <input type="text" v-model="customerData.destination" class="form-control" id="customer.destination">
                </div>

                <div class="form-group mse">
                    <label>Широта:</label>
                    <input type="text" v-model="customerData.lat" class="form-control" id="customer.lat">
                </div>
                <div class="form-group mse">
                    <label>Долгота:</label>
                    <input type="text" v-model="customerData.lon" class="form-control" id="customer.lon">
                </div>-->
            </fieldset>

        </div>

        <div class="form-inline legend mse" rel="Документы по договору">
            <div class="form-group mse">
                <label>Договор подписан:</label>
                <input id="lid.contract_signed" v-model="lidData.contract_signed" type="checkbox" @change="save('lid','contract_signed',lidData.contract_signed)"/>
                <label for="lid.contract_signed" class="check-label"></label>
            </div>
            <div class="form-group mse">
                <label>Договор передан:</label>
                <input id="lid.contract_transferred" v-model="lidData.contract_transferred" type="checkbox"  @change="save('lid','contract_transferred',lidData.contract_transferred)"/>
                <label for="lid.contract_transferred" class="check-label"></label>
            </div>
            <div class="form-group mse">
                <label>Безнал?:</label>
                <input id="lid.customer_payment" v-model="lidData.customer_payment" type="checkbox"  @change="save('lid','customer_payment',lidData.customer_payment)"/>
                <label for="lid.customer_payment" class="check-label"></label>
            </div>
            <div class="form-group mse">
                <label for="action">Порядок рачсетов:</label>
                <select v-model="lidData.payment_rule"  class="form-control" id="lid.payment_rule" @change="save('lid','payment_rule',lidData.payment_rule)">
                    <option v-for="payment_rule in helpers.payment_rules" :value="payment_rule.id">{{payment_rule.name}}</option>
                </select>
            </div>
            <div class="form-group mse">
                <label>Условия постоплаты:</label>
                <input type="text" v-model="lidData.payment_condition" class="form-control" id="lid.payment_condition" @focusout="save('lid','payment_condition',lidData.payment_condition)">
            </div>
            <div class="form-group mse">
                <label>Файл договора:
                    <a href="javascript:" @click="deleteFile('lidData','contract_file')" v-if="lidData.contract_file">X</a>
                </label>
                <div class="centered">
                    <template v-if="lidData.contract_file">
                        <btn target="_blank" :href="'/storage/docs/'+lidData.contract_file">Просмотреть</btn>
                    </template>
                    <template v-else>
                        <label class="file" for="InputFile">ФАЙЛ</label>
                        <input type="file" id="InputFile" v-on:change="uploadFile('lid','contract_file',lidData.contract_file)">
                    </template>
                </div>

            </div>
            <div class="form-group mse">
                 <label for="site">Конструктор актов:</label>
                <a href="javascript:" class="btn btn-warning" @click="addNewLids()">Добавить несколько актов</a>
            </div>
        </div>
        <div class="form-inline legend mse" rel="Комментарий">
            <textarea class="lid_comment" v-model="lidData.comment" @focusout="save('lid','comment',lidData.comment)"></textarea>
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
                    <th><btn type="primary" @click="addEmptyAct()">Добавить акт</btn></th>
                </tr>
                </thead>

                <tbody>

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
                            <input
                                v-model="lidData.acts[key].floating" type="checkbox" :id="'floating_'+key"
                                @change="save('act','floating',lidData.acts[key].floating,lidData.acts[key].id)"
                            />
                            <label class="true-false fa" :for="'floating_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.floating" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>
                    </td>
                    <td class="no-wrap">
                        <template v-if="act.active">
                            <cite>Срок с</cite>
                            <template v-if="lidData.acts[key].floating">
                                <input type="date"
                                       v-model="lidData.acts[key].floating_date_from"
                                       @focusout="save('act','floating_date_from',lidData.acts[key].floating_date_from,lidData.acts[key].id)"
                                />
                            </template>
                            <template v-else>
                                <input type="date" disabled v-model="lidData.acts[key].floating_date_from"/>
                            </template>

                        </template>
                        <template v-else>
                            {{act.floating_date_from}}
                        </template>
                    </td>
                    <td class="no-wrap">
                        <template v-if="act.active">
                            <cite>Срок до</cite>
                            <template v-if="lidData.acts[key].floating">
                                <input type="date"
                                       v-model="lidData.acts[key].floating_date_to"
                                       @focusout="save('act','floating_date_to',lidData.acts[key].floating_date_to,lidData.acts[key].id)"
                                />
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
                            <input v-model="lidData.acts[key].finished" type="checkbox"
                                   :id="'finished_'+key"
                                   @change="save('act','finished',lidData.acts[key].finished,lidData.acts[key].id)"
                            />
                            <label class="true-false fa" :for="'finished_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.finished" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>

                    </td>
                    <td class="address">{{act.address}}</td>
                    <td><!--Бух. акт передан?-->
                        <template v-if="act.active">
                            <cite>Бух. акт передан?</cite>
                            <input
                                v-model="lidData.acts[key].booking_act_transferred" type="checkbox"
                                :id="'booking_act_transferred_'+key"
                                @change="save('act','booking_act_transferred',lidData.acts[key].booking_act_transferred,lidData.acts[key].id)"/>
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
                            <input v-model="lidData.acts[key].booking_act_signed" type="checkbox"
                                   :id="'booking_act_signed_'+key"
                                   @change="save('act','booking_act_signed',lidData.acts[key].booking_act_signed,lidData.acts[key].id)"/>
                            <label class="true-false fa" :for="'booking_act_signed_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.booking_act_signed" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>

                    </td>
                    <td><!--Бух. акт скан-->
                        <template v-if="act.active">
                            <cite>Бух. акт скан: <a class="x white" @click="deleteFile('act','booking_act_file',key)">X</a> </cite>

                            <template v-if="lidData.acts[key].booking_act_file">
                                <a class="file sm btn-default" target="_blank" :href="'/storage/docs/'+lidData.acts[key].booking_act_file">Просмотреть</a>
                            </template>
                            <template v-else>
                                <label class="file sm" :for="'booking_act_file_'+key">ФАЙЛ</label>
                                <input type="file" :id="'booking_act_file_'+key" v-on:change="uploadFile('act','booking_act_file','',act.id,key)">
                            </template>

                        </template>
                        <template v-else>
                            <i v-if="act.booking_act_file" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>
                    </td>
                    <td><!--Исп. акт подписан?-->
                        <template v-if="act.active">
                            <cite>Исп. акт подписан?</cite>
                            <input v-model="lidData.acts[key].implement_act_signed" type="checkbox"
                                   :id="'implement_act_signed_'+key"
                                   @change="save('act','implement_act_signed',lidData.acts[key].implement_act_signed,lidData.acts[key].id)"
                            />
                            <label class="true-false fa" :for="'implement_act_signed_'+key"></label>
                        </template>
                        <template v-else>
                            <i v-if="act.implement_act_signed" class="yes fa"></i>
                            <i v-else class="not fa"></i>
                        </template>

                    </td>
                    <td><!--Исп. акт скан-->
                        <template v-if="act.active">
                            <cite>Исп. акт скан <a class="x white" @click="deleteFile('act','implement_act_file',key)">X</a></cite>

                            <template v-if="lidData.acts[key].implement_act_file">
                                <a class="file sm btn-default" target="_blank" :href="'/storage/docs/'+lidData.acts[key].implement_act_file">Просмотреть</a>
                            </template>
                            <template v-else>
                                <label class="file sm" :for="'implement_act_file_'+key">ФАЙЛ</label>
                                <input type="file" :id="'implement_act_file_'+key" v-on:change="uploadFile('act','implement_act_file','',act.id,key)">
                            </template>

<!--                            <label class="file sm" :for="'implement_act_file_'+key">ФАЙЛ</label>-->
<!--                            <input type="file" :id="'implement_act_file_'+key" v-on:change="uploadFile('act','implement_act_file','',act.id,key)">-->
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

<!--                                <dadata-address
                                    :mse="false"
                                    :main="true"
                                    :id="key"

                                    :main="{
                                        address:customerData.address,
                                        lat:customerData.lat,
                                        lon:customerData.lon,
                                        destination:customerData.destination,
                                        region:customerData.region,
                                        }"
                                    :regions="helpers.regions"
                                    @setAddress="setActAddress"
                                    @saveAddressEntity="saveActAddressEntity"
                                    @mainAddress="mainActAddress"
                                />-->





                                <div class="form-group">
                                     <a href="javascript:" @click="setAddress(key)" class="btn btn-default">Основной адрес</a>
                                </div>
                                <div class="form-group drop-address">
                                    <label>Адрес:</label>
                                    <input  type="text" v-model="lidData.acts[key].address" class="form-control" style="width: 650px" @keyup="dadataAddress()"/>
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
                                        <th>Площадь:</th>
                                        <th>Единица площади:</th>
                                        <th>Методы:</th>
                                        <th>Цена гост</th>
                                        <th>Цена факт</th>
                                        <th>
                                            <btn type="success pull-right" @click="addVolume(act.id)" >+</btn>
                                        </th>
                                    </tr>
                                    </thead>
<!--
Обработка от плесени = мех очистка(9) / хим обработка
-->
                                    <tbody v-if="actRelations[act.id]">
                                    <tr class="volume-row" v-for="(volume,key) in actRelations[act.id].volumes">
                                        <td>
                                            <select v-model="volume.pest" class="form-control" @focusout="addActRelation('volume',volume.id,'pest',volume.pest)">
                                                <option v-for="pest in helpers.pests" :value="pest.id">{{pest.name}}</option>
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
                                        <td  v-if="volume.pest && volume.square && volume.entity">
<!--                                            <div >-->
                                                <template v-for="pest in helpers.pests[volume.pest].methods">

                                                    <input type="checkbox"
                                                           :id="volume.id+'_pest_'+pest"
                                                           :name="'methods_'+volume.id"
                                                           v-model="volume.method"
                                                           :checked="volume.method.indexOf(+pest)>-1"
                                                           :value="pest"
                                                           @change="addActRelation('volume',volume.id,'method',volume.method,act.id)"
                                                    >
                                                    <label class="methods_chb" :for="volume.id+'_pest_'+pest">{{helpers.methods[pest].name}}</label>
                                                </template>
<!--                                            </div>-->

<!--                                            <select v-model="volume.method" class="form-control" @focusout="addActRelation('volume',volume.id,'method',volume.method)">
                                                <option v-for="method in helpers.methods" :value="method.id">{{method.name}}</option>
                                            </select>-->
                                        </td>
                                        <td><input type="number" v-model="volume.price_standard" class="form-control" @focusout="addActRelation('volume',volume.id,'price_standard',volume.price_standard)"/></td>
                                        <td><input type="number" v-model="volume.price_fact" class="form-control" @focusout="addActRelation('volume',volume.id,'price_fact',volume.price_fact)"/></td>
                                        <td>
                                            <btn type="warning pull-right" @click="deleteVolume(act.id,volume.id,key)">-</btn>
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
                                            <btn type="success" @click="addImplement(act.id)">+</btn>
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
                                            <btn type="warning" @click="deleteImplement(act.id,implement.id,key)">-</btn>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </fieldset>
                            <fieldset rel="Удаление акта" class="full-center">
                                <btn type="danger" @click="deleteAct(key)">Удалить акт</btn>
                               <!-- <table style="width: 100%;">

                                    <thead>
                                    <tr>
                                        <th>Затраты по химии</th>
                                        <th>Затраты по транспорту</th>
                                        <th>Затраты по труду</th>
                                        <th>Итого</th>
                                        <th>Удалить акт</th>
                                    </tr>
                                    </thead>


                                    <tbody>
                                    <tr>
                                        <td>фывфыв</td>
                                        <td>{{act.cost_transport}}</td>
                                        <td>фывфВыв</td>
                                        <td>фывфВыв</td>
                                        <td></td>
                                    </tr>
                                    </tbody>

                                </table>-->
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
    import DadataAddress from "../Dadata/DadataAddress";
    import Inputmask from 'inputmask';

    export default {
        name:'Lid',
        props:['lid','acts','user','helpers','statuses','regions','customer'],
        data: function() {
            return {
                lidData: this.lid,
                customerData: this.customer,
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
                    newLids:false
                }
            }
        },
        watch: {
            'customerData.phone_ext'(n,o){
                if(n.match(/\+7 \(\d{3}\) \d{3} \d{2} \d{2}/) && o.match(/\+7 \(\d{3}\) \d{3} \d{2} \d_/)){
                    this.save('customer','phone_ext',n.replace(/[^0-9+]/g, ''),this.customerData.id)
                }},
            'customerData.phone'(n,o){
                console.log(n);
                if(n.match(/\+7 \(\d{3}\) \d{3} \d{2} \d{2}/) && o.match(/\+7 \(\d{3}\) \d{3} \d{2} \d_/)){
                    this.save('customer','phone',n.replace(/[^0-9+]/g, ''),this.customerData.id)

                }}, // +7 (798) 522 21 79
        },
        components: {
            MaskedInput,ImplementMap,L,NewActs,DadataAddress,Inputmask
        },
        methods: {
            co(gg){console.log(gg);},
           async save(model,field,value,id = false){
                if(value !== '') {
                    try {
                        let response = await Axios.get('/ajax/lid/update_field?id='+this.lid.id+'&model='+model+'&field='+field+'&value='+value+'&manager='+this.user.id+'&salt='+this.salt+'&child_id='+id);
                        response.data > 0 ? this.success(field,'save') : this.danger(field,'save');
                        console.log(response.data);
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
            async addActRelation(model,id,field,value,act = null){
                // console.log(model,id,field,value,act);
                this.calculateRemedy(act);
                try {
                    let response = await Axios.get('/ajax/lid/add_act_relation?model='+model+'&id='+id+'&field='+field+'&value='+value);
                    response.data > 0 ? this.success(field,'addActRelation') : this.danger(field,'addActRelation');
                } catch (error) {
                    console.log(error);
                    this.danger(field,'addActRelation');
                }
            },
            calculateRemedy(id){
                let actData,actKey;
                for (let a in this.lidData.acts)if(this.lidData.acts[a].id === id){
                    actData = this.lidData.acts[a];
                    actKey = a;
                }
                console.log('actKey',actKey);

                if(actData.destination) {
                    this.lidData.acts[actKey].cost_transport = (Number(actData.destination) * Number(this.helpers.constants[2].value)).toFixed(0);
                }

                let act = this.actRelations[id];
                for(let volume of act.volumes){
                    console.log(act.volumes);

                    for(let meth of volume.method) {
                        console.log(meth);
                    }

                }

                console.log(id);
            },
            calculateTransport(){},
            calculateLabor(){},
            /*---*/
            async addEmptyAct(){
                try {
                    let response = await Axios.get('/ajax/lid/add_empty_act?lid='+this.lidData.id+'&cnt='+this.lidData.acts.length);

                    console.log(response.data);
                    this.$set(this.lidData,'acts',response.data);

                } catch (error) {
                    console.log(error);
                }
            },
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
                    duration: 1000,
                    placement: 'bottom-right',
                    type: 'success',
                    title: 'Сохранено!',
                    content: field +' + '+finc
                })
            },

            danger (field, finc) {
                this.$notify({
                    duration: 1000,
                    placement: 'bottom-right',
                    type: 'danger',
                    title: 'Возникла ошибка',
                    content: field +' + '+finc
                })
            },
            rand(){
                return Math.floor(Math.random() * (900000 - 100000 + 1)) + 100000;
            },
            uploadFile(model,field,value,id=false,key=false) {
                let file = event.target.files[0] || event.dataTransfer.files[0];
                let formData = new FormData();
                formData.append('file', file);
                Axios.post('/attach', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                    .then(response => {
                        console.log(response.data);
                        if(response.data.uploaded){
                            if(model === 'act'){
                                this.$set(this.lidData.acts[key],field,response.data.fileName);
                            } else {
                                this.$set(this.lidData,'contract_file',response.data.fileName);
                            }

                            this.save(model,field,response.data.fileName,id);
                        }
                    }, error => {
                        console.log(error)
                    })
            },

            deleteFile(model,field,key = false){
                this.$confirm({title: 'Подтверждение', content: 'Удалить файл?'})
                    .then(() => {
                        let resp;
                        if(model === 'act'){
                             resp = this.save('act',field,null,this.lidData.acts[key].id);
                            if(resp){
                                this.$set(this.lidData.acts[key],field,null);
                            }
                        } else  {
                            resp = this.save('lid',field,null);
                            if(resp){
                                this.$set(this.$data[model],field,null);
                            }
                        }

                        this.$notify({
                            type: 'success',
                            content: 'Удалено'
                        });
                    })
                    .catch(() => {
                        this.$notify('Отменено')
                    })
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
                if(await this.save('act','address',this.customerData.address,id) > 0){
                     this.lidData.acts[actKey].address = this.customerData.address;
                }

                if(await this.save('act','lat',this.customerData.lat,id)){
                     this.lidData.acts[actKey].lat = this.customerData.lat;
                }

                if(await this.save('act','lon',this.customerData.lon,id)){
                     this.lidData.acts[actKey].lon = this.customerData.lon;
                }

                if(await this.save('act','destination',this.customerData.destination,id)){
                     this.lidData.acts[actKey].destination = this.customerData.destination;
                }

                if(await this.save('act','region',this.customerData.region,id)){
                     this.lidData.acts[actKey].region = this.customerData.region;
                }

            },
            /*---*/
           async dadataAddressClick(dadata,key){
               console.log(dadata);
                this.suggestions.text = '';
                this.lidData.acts[key].address = dadata.unrestricted_value;
                if(dadata.data.geo_lat && dadata.data.geo_lon){

                    let id =  this.lidData.acts[key].id;
                    if( this.save('act','address',dadata.unrestricted_value,id) > 0){
                        // this.$set(this.lidData.acts[key],'address',dadata.data.address);
                        this.lidData.acts[key].address = dadata.data.address;
                    }

                    if( this.save('act','lat',dadata.data.geo_lat,id) > 0){
                        // this.$set(this.lidData.acts[key],'lat',dadata.data.geo_lat);
                        this.lidData.acts[key].lat = dadata.data.geo_lat;
                    }

                    if( this.save('act','lon',dadata.data.geo_lon,id) > 0){
                        // this.$set(this.lidData.acts[key],'lon',dadata.data.geo_lon);
                        this.lidData.acts[key].lon = dadata.data.geo_lon;
                    }

                    if( this.save('act','region',dadata.data.region,id) > 0){
                        // this.$set(this.lidData.acts[key],'region',dadata.data.region);
                        this.lidData.acts[key].region = dadata.data.region;
                    }

                    let region = this.regions[dadata.data.region];
                    await this.OSMLayers(dadata.data.geo_lat,dadata.data.geo_lon,region.center_lat,region.center_lon);



                    if(this.save('act','destination',this.suggestions.destination,id)){
                        // this.$set(this.lidData.acts[key],'destination',dadata.data.destination);
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
            async updateActs(){
                try {
                    let response = await Axios.get('/ajax/lid/get_acts?lid='+this.lidData.id);
                    this.$set(this.lidData,'acts',response.data);
                } catch (error) {
                    console.log(error);
                }

                this.modal.newLids = false;

            },
            deleteAct(key){
                console.log(key);
                this.$confirm({title: 'Подтверждение', content: 'Удалить?'})
                    .then(() => {
                        let response = Axios.get('/ajax/lid/delete_act?id='+this.lidData.acts[key].id);

                        this.lidData.acts.splice(key,1);
                        this.$notify({
                            type: 'success',
                            content: 'Удалено'
                        });
                    })
                    .catch(() => {
                        this.$notify('Отменено')
                    })



            },
            async customerSearch(ent){

 /*               try {
                    let response = await Axios.get('/ajax/lid/customer_search?field='+ent+'&value='+)

                } catch (error) {
                    console.log(error);
                }*/

            },





            // setAddress(obj){},
            // saveAddressEntity(ent){},
            // mainAddress(obj){},

           async setMainAddress(obj){
               await this.$set(this.customerData, 'address', obj.address);
               await this.save('customer', 'address', this.customerData.address, this.customerData.id);

               await this.$set(this.customerData, 'destination', obj.destination);
               await this.save('customer', 'destination', this.customerData.destination, this.customerData.id);

               await this.$set(this.customerData, 'lat', obj.lat);
               await this.save('customer', 'lat', this.customerData.lat, this.customerData.id);

               await this.$set(this.customerData, 'lon', obj.lon);
               await this.save('customer', 'lon', this.customerData.lon, this.customerData.id);

               await this.$set(this.customerData, 'region', obj.region);
               await this.save('customer', 'region', this.customerData.region, this.customerData.id);
            },
            saveMainAddressEntity(ent){
                this.$set(this.customerData,ent.entity,ent.value);
                this.save('customer', ent.entity, this.customerData[ent.entity], this.customerData.id);
            },
            mainMainAddress(obj){},

            // async setActAddress(obj,actKey){
            //
            //     let actID = this.lidData.acts[actKey].id;
            //
            //     await this.save('act', 'address', obj.address, actID);
            //     await this.$set(this.lidData.acts[actKey], 'address', obj.address);
            //
            //     await this.save('act', 'destination', obj.destination, actID);
            //     await this.$set(this.customerData, 'destination', obj.destination);
            //
            //     await this.save('act', 'lat', obj.lat, actID);
            //     await this.$set(this.customerData, 'lat', obj.lat);
            //
            //     await this.save('act', 'lon', obj.lon, actID);
            //     await this.$set(this.customerData, 'lon', obj.lon);
            //
            //     await this.save('act', 'region', obj.region, actID);
            //     await this.$set(this.customerData, 'region', obj.region);
            //
            // },
            // saveActAddressEntity(ent,actKey){
            //     console.log(ent,actKey);
            // },
            // mainActAddress(obj,actKey){},







        },
        computed:{

        },
        mounted() {
            // this.init();
            this.salt = this.rand();
            var im = new Inputmask("+7 (999) 999 99 99");
            im.mask('.tel');
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

    .lid_comment {
        width: 100%;
        min-height: 100px;
        padding: 5px 15px;

    }
    input[type="checkbox"] + .methods_chb {
        cursor: pointer;
        display: inline-flex;
        margin-right: 15px;
        padding: 0 10px;
        height: 20px;
        justify-content: center;
        align-items: center;
        border-radius: 4px;
        border: 1px solid #ccc;
    }
    input[type="checkbox"]:checked + .methods_chb {
        border: 1px solid #3c8dbc;
        background: #3c8dbc;
        color: #fff;
        opacity: 1;
    }
    .volume-row {
        position: relative;
        /*border-bottom: 1px solid #3c8dbc;*/
    }
    .volume-row  > td {
        padding-top: 20px;
        padding-bottom: 20px;


    }
    .methods-nest {
        position: absolute!important;
        /*top: 0px;*/
        left: 0px;
        padding-top: 19px!important;


    }
</style>
<!-- рудницкая тетрадь для контрольных работ -->
