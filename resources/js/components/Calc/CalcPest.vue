<template>
    <div class="Pest">
        <h2>{{pest.name}}</h2>
        <table class="pest-tbl">
            <thead>
            <tr>
                <th colspan="2">Метод :  </th>

                <th></th>
            </tr>
            </thead>

            <tbody>
            <tr v-for="(method,methodKey) in price">
                <!--
                ((ст.литра / 1000) * расход на 1л.) * расход на М² (мл)
                ((950/1000)*30)



                -->


                <td>
                    {{methods[methodKey].name}}({{methodKey}})
                </td>
                <td><btn @click="addRemedy(methodKey)">+</btn></td>
                <td colspan="3">
                    <table style="width:100%; margin:10px 0;">
                        <thead>
                        <tr>
                            <th colspan="2">Средство : </th>
                            <th>расход (л)</th>
                            <th> (м²)</th>
                            <th>Закуп  (1л/кг)</th>
                            <th>с/c (м²/сотка)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(remedy,remedyKey) in method">
                            <!--Средство-->
                            <td style="width: 36px;"><btn @click="removeRemedy(remedy.id)">-</btn></td>
                            <td >
                                <select @change="save('remedy',remedy.remedy,remedy.id)" v-model="remedy.remedy" class="form-control" style="width: 150px;">
                                    <option v-for="chemical in chemicals" :value="chemical.id">{{chemical.name}}</option>
                                </select>
                            </td>
                            <!--расход-->
                            <td>
                                <template v-if="chemicals[remedyKey] !== undefined">
                                    {{chemicals[remedyKey].expenses}}
                                </template>
                            </td>
                            <td>
                                <template v-if="chemicals[remedyKey] !== undefined">
                                    <input @focusout="calcPrice(remedy,remedy.expenses)" type="number" v-model="remedy.expenses">

                                </template>
                            </td>
                            <!--Закуп-->
                            <td>
                                <template v-if="chemicals[remedyKey] !== undefined">
                                    {{chemicals[remedyKey].price_net}}
                                </template>
                            </td>
                            <!--с/c (м²/сотка)-->
                            <td>
                                <template v-if="chemicals[remedyKey] !== undefined">
                                    {{remedy.cost}}
                                </template>
                            </td>
                            <!--расход на М² (мл)-->
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </td>

                <td></td>
                <td></td>
            </tr>
            </tbody>

        </table>

    </div>
</template>

<script>
    import Axios from 'axios';
    export default {
        name: 'CalcPest',
        props: ['table','pest','methods','chemicals','constant','tt'],
        data:function(){
            return {
                salary_hour: Math.round((this.constant[1].value/21)/8),
                price:this.table,
            }
        },
        methods:{
            async save(field,value,id){
                 try {
                     let response = await Axios.get('/ajax/price/save_field?pest='+this.pest.id+'&id='+id+'&field='+field+'&value='+value);
                     if(Number(response.data) !== 0){
                         this.price = response.data;
                     }

                 } catch (error) {
                     console.log(error);
                 }
            },
            async addRemedy(method){
                try {
                    let response = await Axios.get('/ajax/price/add_remedy?method='+method+'&pest='+this.pest.id);
                    if(Number(response.data) !== 0){
                        this.price = response.data;
                    }
                } catch (error) {
                    console.log(error);
                }
            },
            async removeRemedy(rowID){
                try {
                    let response = await Axios.get('/ajax/price/remove_remedy?id='+rowID);
                    console.log(response.data);
                    if(Number(response.data) !== 0){
                        this.price = response.data;
                    }
                } catch (error) {
                    console.log(error);
                }
            },
            calcPrice(remedy,exp){
                let r = this.chemicals[remedy.remedy];
                let cost = ((r.price_net/1000)*r.expenses)*exp;

                this.save('expenses',exp,remedy.id);
                this.save('cost',cost.toFixed(1),remedy.id);





                // console.log(cost.toFixed(1),remedy,exp);
            },
        },
        mounted() {

        }
    };
</script>

<style lang="scss">
    .pest-tbl {
        width: 100%;
        > tbody {
            > tr {
                border-bottom: 1px solid black;
            }
        }

        th,td {
/*            max-width: 120px;
            select {
                width: 120px;
            }*/
        }
    }


</style>
