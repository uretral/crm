<?php


namespace App\Admin\Extensions\Nav;


use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Facades\Admin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;

class Heading
{
    public $buttonCreateHref = '';
    public $buttonCreateName = '';
    public $buttonListHref = '';
    public $buttonListName = '';
    public function __toString()
    {
        $parent = '';
        $child = '';
        $menu = Menu::where('uri',request()->path())->first();
        if($menu){
            if($menu->parent_id){
                $parent = Menu::find($menu->parent_id)->title;
            }
            $child = $menu->title;
        }
        $this->buttons();
//        $tpl = '';
//        if($this->buttonCreateHref) {
//            $tpl .=
//        '<a href="' .$this->buttonCreateHref. '" class="mseHeading " title="Добавить">
//        <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;' .$this->buttonCreateName. '</span>
//    </a>';
//        }
//        if($this->buttonListHref) {
//            $tpl .=
//                '<a href="' .$this->buttonListHref. '" class=" mseHeading" title="Добавить">
//        <i class="fa fa-bars"></i><span class="hidden-xs">&nbsp;&nbsp;' .$this->buttonListName. '</span>
//    </a>';
//        }

        return <<< HTML
<a class="mseHeading">
    <span> $parent </span>
</a>
HTML;

        // TODO: Implement __toString() method.
    }

    public function buttons()
    {
        $case = request()->segment(1);
        switch ($case){
            case 'crm':
                $this->buttonCreateHref = '/crm/lids/create';
                $this->buttonCreateName = 'Добавить';
                $this->buttonListHref = '/crm/lids';
                $this->buttonListName = 'В список';
                break;
            default:
                $this->buttonCreateHref = '';
                $this->buttonCreateName = '';
                $this->buttonListHref = '';
                $this->buttonListName = '';
                break;
        }

    }

}
