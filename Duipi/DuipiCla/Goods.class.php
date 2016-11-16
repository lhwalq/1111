<?php

/**
 * admin
 * addtime 2016/03/15
 */

namespace Duipi\DuipiCla;

use Think\Model\RelationModel;

class Goods extends RelationModel {

    /**
     * 获取 tp_spec_item表 指定规格id的 规格项
     * @param int $spec_id 规格id
     * @return array 返回数组
     */
    public function getSpecItem($spec_id) {
        $model = M('s_spec_item');
        $arr = $model->where("spec_id = $spec_id")->order('id')->select();
        $arr = get_id_val($arr, 'id', 'item');
        return $arr;
        
    }

    /**
     * 获取商品规格
     */
    public function get_spec($goods_id) {
        //商品规格 价钱 库存表 找出 所有 规格项id
        $keys = M('s_spec_goods_price')->where("goods_id = $goods_id")->getField("GROUP_CONCAT(`key` SEPARATOR '_') ");
        $filter_spec = array();
        if ($keys) {
            // $specImage = M('SpecImage')->where("goods_id = $goods_id$specImage and src != '' ")->getField("spec_image_id,src"); // 规格对应的 图片表， 例如颜色
            $keys = str_replace('_', ',', $keys);
            $sql = "SELECT a.name,a.order,b.* FROM yys_s_spec AS a INNER JOIN yys_s_spec_item AS b ON a.id = b.spec_id WHERE b.id IN($keys) ORDER BY b.id";
            $filter_spec2 = M()->query($sql);
            foreach ($filter_spec2 as $key => $val) {
                $filter_spec[$val['name']][] = array(
                    'item_id' => $val['id'],
                    'item' => trimall($val['item']),
                        // 'src' => $specImage[$val['id']],
                );
            }
        }
        return $filter_spec;
    }

    

}
