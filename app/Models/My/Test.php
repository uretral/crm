<?php


namespace App\Models\My;
use App\Admin\Controllers\Crm\ActController;
use App\Models\Crm\Act;
use Closure;
use Encore\Admin\Form;
use Encore\Admin\Form\Builder;
use Encore\Admin\Form\Field;
use Encore\Admin\Form\Field\HasMany;
use Encore\Admin\Form\NestedForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Test extends Model
{

    public $model;

    public function removeRelations($data){
        foreach ($data as $relName => $relArr) if(is_array($relArr)) {
            foreach ($relArr as $k => $v) if(is_numeric($k)) {
                $this->model->$relName()->getRelated()::find($k)->delete();
            }
        }
    }

    public function updateRelations($rel,$data,$parentID){

        foreach ($data as $relKey => $relArr){
            $remove = $relArr['_remove_'];
            unset($relArr['_remove_']);
            if($remove) { // remove
                if(is_numeric($relKey)){
                    $this->model->$rel()->getRelated()::find($relKey)->delete();
                } else {
                    continue;
                }
            } else {
                if(is_numeric($relKey)) { // update
                    $this->model->$rel()->where('parent',$relKey)->update($relArr);
                } else { // save

                    $this->model = new Act;
                    $relArr['parent'] = $parentID;
                    $relModel = $this->model->$rel()->getRelated();
                    foreach ($relArr as $k => $v){
                        $relModel->$k = $v;
                    }
                    $relModel->save();
                }
            }
        }
    }

    public function saveRelations(){

    }

    public function tableTest($arr,$lid){
        $this->model = new Act;

        $arrTest = [
            1 => [
                "act_nr" => "1",
                "volume" =>  [
                    1 => [
                        "pest" => "3",
                        "method" => "1",
                        "square" => "1",
                        "entity" => "1",
                        "price_standard" => "1111",
                        "price_fact" => "1111",
                        "_remove_" => "0",
                    ],
                    2 =>  [
                        "pest" => "2",
                        "method" => "2",
                        "square" => "2",
                        "entity" => "2",
                        "price_standard" => "2222",
                        "price_fact" => "2222",
                        "_remove_" => "0",
                    ],
                    "new_3" => [
                        "pest" => "3",
                        "method" => "1",
                        "square" => "2",
                        "entity" => "2",
                        "price_standard" => "1111",
                        "price_fact" => "11111",
                        "_remove_" => "0",
                    ],
                    "new_4" => [
                        "pest" => "2",
                        "method" => "1",
                        "square" => "5",
                        "entity" => "2",
                        "price_standard" => "1112",
                        "price_fact" => "1112",
                        "_remove_" => "0",
                    ],
                ],
                "implement" => [
                    "new_1" => [
                        "master" => "1111",
                        "start_date" => "2019-07-18 00:00:00",
                        "end_date" => "2019-07-18 00:00:00",
                        "_remove_" => "0"
                    ],
                    "new_2" => [
                        "master" => "1112",
                        "start_date" => "2019-07-18 00:00:00",
                        "end_date" => "2019-07-13 00:00:00",
                        "_remove_" => "0",
                    ],
                ],
                "id" => "1",
                "_remove_" => "0",
            ],
            2 =>  [
                "act_nr" => "2",
                "volume" =>  [
                    3 =>  [
                        "pest" => "3",
                        "method" => "3",
                        "square" => "3",
                        "entity" => "3",
                        "price_standard" => "3",
                        "price_fact" => "3",
                        "_remove_" => "0",
                    ],
                    4 =>  [
                        "pest" => "4",
                        "method" => "4",
                        "square" => "4",
                        "entity" => "4",
                        "price_standard" => "4",
                        "price_fact" => "4",
                        "_remove_" => "0",
                    ],
                ],
                "implement" => [
                    "new_1" => [
                        "master" => "2222",
                        "start_date" => "2019-07-11 00:00:00",
                        "end_date" => "2019-07-12 00:00:00",
                        "_remove_" => "0",
                    ],
                ],
                "id" => "2",
                "_remove_" => "0",
            ],
            14 => [
                "act_nr" => "3333",
                "volume" => [
                    "new_1" => [
                        "pest" => "3",
                        "method" => "2",
                        "square" => "5",
                        "entity" => "3",
                        "price_standard" => "3333",
                        "price_fact" => "3333",
                        "_remove_" => "0",
                    ],
                    "new_2" => [
                        "pest" => "4",
                        "method" => "3",
                        "square" => "3",
                        "entity" => "4",
                        "price_standard" => "33332",
                        "price_fact" => "33332",
                        "_remove_" => "0",
                    ],
                ],
                "implement" => [
                    "new_1" => [
                        "master" => "3333",
                        "start_date" => "2019-07-24 00:00:00",
                        "end_date" => "2019-07-19 00:00:00",
                        "_remove_" => "0",
                    ],
                    "new_2" => [
                        "master" => "33332",
                        "start_date" => "2019-07-05 00:00:00",
                        "end_date" => "2019-07-26 00:00:00",
                        "_remove_" => "0",
                    ],
                ],
                "id" => null,
                "_remove_" => "0",
            ],
            15 => [
                "act_nr" => "4444",
                "volume" => [
                    "new_1" => [
                        "pest" => "4",
                        "method" => "1",
                        "square" => "0",
                        "entity" => "2",
                        "price_standard" => "4444",
                        "price_fact" => "44444",
                        "_remove_" => "0",
                    ],
                ],
                "implement" => [
                    "new_1" => [
                        "master" => "4444",
                        "start_date" => "2019-07-27 00:00:00",
                        "end_date" => "2019-07-19 00:00:00",
                        "_remove_" => "0",
                    ],
                ],
                "id" => null,
                "_remove_" => "0",
            ],
        ];

        dump($arr);



        foreach ($arr as $parentID => $parentData){
            $remove = $parentData['_remove_'];
            $id = $parentData['id'];
            unset($parentData['_remove_']);
            unset($parentData['id']);

/*case delete*/
            if($remove){
                $this->removeRelations($parentData);
                $this->model::destroy($parentID);

// case update
            } elseif (is_numeric($parentID)) {

                $this->model::find($parentID);
                $values = [];
                foreach ($parentData as $actKey => $actValue){

                    if(is_array($actValue)){ // case relation
                        $this->updateRelations($actKey,$actValue,$parentID);
                    } else { // case act
                        $values[$actKey] = $actValue;
                    }
                }
                $this->model::find($parentID)->update($values);

// case save
            } else {
                $this->model = new Act;
                $relSet = [];
                foreach ($parentData as $actKey => $actValue){

                    if(is_array($actValue)){ // case relation
                        $relSet[] = [
                            'actKey' => $actKey,
                            'actValue' => $actValue
                        ];


                    } else { // case act
                        $this->model->parent = $lid;
                        $this->model->$actKey = $actValue;
                    }
                }

                $this->model->save();
                $id = $this->model->id;

                foreach ($relSet as $set) {
                    $this->updateRelations($set['actKey'],$set['actValue'],$id);
                }

            }


            return true;
        }

    }

    public function saveAct(){

    }


    public function vue(){
        return <<<HTML
<div id="implementMap"><implement-map  v-bind:date="date"></implement-map></div>
HTML;

    }




}
