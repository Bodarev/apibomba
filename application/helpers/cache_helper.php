<?php
function init_category_cache($lang, $category_id)
{
    $ci=&get_instance();

    $data=$ci->db->where('id',$category_id)->get('category')->row_array();
    if (empty($data)) return false;

    $mnflt = $ci->db->select('attribute_id,sorder,opened')->where('category_id',$category_id)->get('category_attribute')->result_array();

    if (empty($mnflt)) {
        $ci->db->where('id',$category_id)->update('category',array('need_update'=>0));
        return false;
    }

    $attribute_id_list = array();
    $clist=array();
    $clist2=array();
    $clist3=array();
    foreach($mnflt as $row) {
        $clist[$row['attribute_id']]=$row['sorder'];
        $clist3[$row['attribute_id']]=$row['opened'];
        $attribute_id_list[] = $row['attribute_id'];
    }

    $prod_id_query = $ci->db->select('DISTINCT(category_product.product_id) as id')
        ->from('category_product')
        ->join('product_price','product_price.product_id=category_product.product_id AND product_price.price>0','inner')
        //->join('product_stock','product_stock.product_id=category_product.product_id AND product_stock.quantity>0','inner')
        ->where('category_id',$category_id)->get()->result_array();

    if (empty($prod_id_query)) {
        $ci->db->where('id',$category_id)->update('category',array('need_update'=>0));
        return false;
    }

    $id_list = array();
    foreach($prod_id_query as $product) {
        $id_list[] = $product['id'];
    }

    $filters = get_filters_for_set($lang, $data['set_id'],$id_list,$attribute_id_list);
    
    $inserter = array();
    foreach($filters as $group) {
        if (empty($group['attributes'])) continue;
        foreach($group['attributes'] as $attribute) {
            if (!empty($attribute['values'])) {
                if ($attribute['attribute_type']=='string') {
                    $vals_ru_a=array();
                    $vals_ro_a=array();
                    foreach($attribute['values'] as $row) {
                        $vals_ru_a[]=array(
                            'value'=>$row['value'],
                            'count'=>$row['count'],
                            'position'=>$row['position']
                        );
                        $vals_ro_a[]=array(
                            'value'=>$row['value_ro'],
                            'count'=>$row['count'],
                            'position'=>$row['position']
                        );
                        $vals_ro=json_encode($vals_ro_a);
                        $vals_ru=json_encode($vals_ru_a);
                    }
                } else {
                    $vals_ru=$vals_ro=json_encode($attribute['values']);
                }
            } else {
                $vals_ru=$vals_ro='';
            }
            $ins=array(
                'category_id'=>$category_id,
                'opened'=>intval(@$clist3[$attribute['id']]),
                'attribute_id'=>$attribute['id'],
                'type'=>$attribute['attribute_type'] == 'integer' ? 'string' : $attribute['attribute_type'],
                'name_ru'=>@$attribute['name'],
                'name_ro'=>@$attribute['name_ro'],
                'description_ru'=>@$attribute['description_ru'],
                'description_ro'=>@$attribute['description_ro'],
                'sorder'=>intval(@$clist[$attribute['id']]),
                'checked'=>1,
                'values_ru'=>$vals_ru,
                'values_ro'=>$vals_ro
            );

            $inserter[]=$ins;
            $attrList[]=$attribute['id'];
        }
    }

    $ci->db->where('category_id',$category_id)->delete('category_attribute');
    if (!empty($inserter)) $ci->db->insert_batch('category_attribute', $inserter);

    if (!empty($attrList) && !empty($id_list)) {
        $attr_val_cache = array();
        $list = $ci->db->where_in('product_id',$id_list)->where_in('attribute_id', $attrList)->get('product_attribute_value')->result_array();

        foreach($list as $val) {
            $ins=array(
                'category_id'=>$category_id,
                'product_id'=>$val['product_id'],
                'articol'=>$val['articol'],
                'attribute_id'=>$val['attribute_id'],
                'value_ro'=>$val['value_ro'],
                'value_ru'=>$val['value_ru']
            );
            $attr_val_cache[$val['product_id'].'-'.$val['attribute_id']]=$ins;
        }

        $ci->db->where('category_id',$category_id)->delete('product_attribute_value_cached');
        if (!empty($attr_val_cache)) $ci->db->insert_batch('product_attribute_value_cached', $attr_val_cache);
    }

    $ci->db->where('id',$category_id)->update('category',array('need_update'=>0));

    return true;
}

function get_attributes_for_product($lang, $category_set_id = 0, $prod_id = 0): array
{
    $ci=&get_instance();

    $groups_query = $ci->db->select("
        attribute_group.name_$lang as name,
        attribute_group.id as group_id
    ")
        ->from('attribute_group')
        ->join('attribute_set_group','attribute_set_group.group_id=attribute_group.id')
        ->where_in('attribute_set_group.set_id', explode(",", $category_set_id))
        ->order_by('attribute_set_group.sorder ASC')
        ->get()
        ->result_array();

    $group_list = array(0);
    $groups = array(0);
    foreach($groups_query as $key=>$row) {
        $groups[$row['group_id']] = $row;
        if (!in_array($row['group_id'],$group_list)) $group_list[]=$row['group_id'];
    }

    $attr_query = $ci->db->select("
			attribute.id,
			attribute.in_filter,
			attribute.name_$lang as name,
			attribute_group_attribute.group_id,
			attribute_type.value as attribute_type
    ")
        ->from('attribute')
        ->join('attribute_type','attribute_type.id = attribute.type_id')
        ->join('attribute_group_attribute','attribute_group_attribute.attribute_id = attribute.id')
        ->where_in('attribute_group_attribute.group_id',$group_list)
        ->order_by('attribute_group_attribute.group_id asc,attribute_group_attribute.sorder ASC')
        ->get()
        ->result_array();


    foreach($attr_query as $row) {
        $group_id = $row['group_id'];
        unset($row['group_id']);
        $groups[$group_id]['attributes'][$row['id']] = $row;
    }

    $attribute_id_list = array(0);
    $required_types = array('string','integer','decimal','boolean');

    foreach($groups as $id=>$group) {
        if (empty($group['attributes'])) {
            unset($groups[$id]);
        } else {
            foreach($group['attributes'] as $attr) {
                if (in_array($attr['attribute_type'],$required_types)) $attribute_id_list[] = $attr['id'];

            }
        }
    }

    $attribute_value_query = get_attribute_values($lang, $attribute_id_list, [$prod_id]);

    foreach($attribute_value_query as $attribute) {
        $attribute_value[$attribute['attribute_id']][] = array('value'=>$attribute['value'],'count'=>$attribute['num_products']);
    }

    foreach($groups as $group_key=>$group) {
        foreach($group['attributes'] as $attr_key=>$attr) {
            if (isset($attribute_value[$attr['id']])) {
                foreach($attribute_value[$attr['id']] as $val) {
                    $groups[$group_key]['attributes'][$attr_key]['values'] = $val;
                }
            }
        }
    }

    return $groups;
}

function get_attribute_values($lang, $attribute_id_list = array(),$prod_ids = array()): array
{
    $ci=&get_instance();

    ini_set('memory_limit', '2048M');
    ini_set('max_execution_time', '0');

    $ci->db->select("attribute_id,value_$lang as value,value_ro as value_ro, articol, position");
    $ci->db->from('product_attribute_value');
    if(!empty($attribute_id_list)) $ci->db->where_in('attribute_id', $attribute_id_list);
    $prod_ids_array = array_chunk($prod_ids, 1000);
    foreach ($prod_ids_array as $key=>$prod_ids_row) {
        if($key == 0) {
            $ci->db->where_in('product_id', $prod_ids_row);
        } else {
            $ci->db->or_where_in('product_id', $prod_ids_row);
        }
    }
    $atribute_value_query = $ci->db->get()->result_array();

    $positions = array();
    foreach ($atribute_value_query as $row) {
        $key = $row['attribute_id']."-".$row['value_ro'];
        if((isset($positions[$key]) && $positions[$key] < $row['position']) || !isset($positions[$key])) $positions[$key] = $row['position'];
    }

    $new = [];
    $articol = [];
    foreach ($atribute_value_query as $row) {
        $key = $row['attribute_id']."-".$row['value_ro'];
        $articol[$key][$row['articol']] = $row['articol'];
        $new[$key] = array(
            'attribute_id' => $row['attribute_id'],
            'value' => $row['value'],
            'value_ro' => $row['value_ro'],
            'position' => $positions[$key],
            'num_products' => count($articol[$key])
        );
    }

    return $new;
}

function get_attribute_values_ro($category_id, $filter_ids, $product_ids): array
{
    $ci=&get_instance();

    $query = $ci->db->select("
			value_ro as value,
			attribute_id,
			product_id
		")
        ->from('product_attribute_value_cached')
        ->where_in('category_id', (is_array($category_id) ? $category_id : array($category_id)))
        ->where_in('attribute_id', $filter_ids)
        ->where_in('product_id', $product_ids)
        ->get();

    $result = $query->result();

    $data=array();
    foreach($result as $row) {
        $data[$row->product_id][$row->attribute_id][$row->value] = $row->value;
    }

    return $data;
}

function get_filters_for_category($lang, $category_id) {

    if(empty($lang) || empty($category_id)) return false;

    $ci=&get_instance();

    $query = $ci->db->select("
            name_$lang as name,
			description_$lang as description,
			values_$lang as values,
			values_ro as values_ro,
			attribute_id,
			opened,
			type
        ")
        ->from('category_attribute')
        ->where_in('category_id',$category_id)
        ->order_by('sorder asc')
        ->get();

    $result = $query->result_array();

    $data = array();

    foreach($result as $row) {
        $data[$row['attribute_id']]=$row;
    }

    return $data;
}

function get_filters_for_set($lang, $category_set_id = 0, $prod_ids = array(), $attribute_id_list = array()): array
{
    $ci=&get_instance();

    $groups_query = $ci->db->select(
        "attribute_group.name_$lang as name,
		attribute_group.id as group_id
    ")
        ->from('attribute_group')
        ->join('attribute_set_group','attribute_set_group.group_id=attribute_group.id')
        ->where_in('attribute_set_group.set_id', explode(",", $category_set_id))
        ->order_by('attribute_set_group.sorder ASC')
        ->get()
        ->result_array();

    $groups=array();

    $group_list = array(0);
    foreach($groups_query as $key=>$row) {
        $groups[$row['group_id']] = $row;
        if (!in_array($row['group_id'],$group_list)) $group_list[]=$row['group_id'];
    }

    $ci->db->select("
		attribute.id,
		attribute.in_filter,
		attribute.name_$lang as name,
		attribute.name_ro as name_ro,
		attribute.description_ru as description_ru,
		attribute.description_ro as description_ro,
		attribute_group_attribute.group_id,
		attribute_type.value as attribute_type
    ");
    $ci->db->from('attribute');
    $ci->db->join('attribute_type','attribute_type.id = attribute.type_id');
    $ci->db->join('attribute_group_attribute','attribute_group_attribute.attribute_id = attribute.id');
    $ci->db->where_in('attribute_group_attribute.group_id',$group_list);
    if(!empty($attribute_id_list)) $ci->db->where_in('attribute.id', $attribute_id_list);
    $ci->db->group_by('attribute.id');
    $ci->db->order_by('attribute_group_attribute.group_id asc,attribute_group_attribute.sorder ASC');
    $attr_query = $ci->db->get()->result_array();

    foreach($attr_query as $row) {
        $group_id = $row['group_id'];
        unset($row['group_id']);
        $groups[$group_id]['attributes'][$row['id']] = $row;
    }
    $atribute_value_query = get_attribute_values($lang, $attribute_id_list, $prod_ids);

    foreach($atribute_value_query as $atribute) {
        $atribute_value[$atribute['attribute_id']][] = array('value'=>$atribute['value'],'value_ro'=>$atribute['value_ro'],'count'=>$atribute['num_products'], 'position'=>$atribute['position']);
    }

    foreach($groups as $group_key=>$group) {
        if (empty($group['attributes'])) continue;
        foreach($group['attributes'] as $attr_key=>$attr) {
            if (!empty($atribute_value[$attr['id']])) {
                if ($attr['attribute_type'] == 'string' || $attr['attribute_type'] == 'integer') {
                    foreach($atribute_value[$attr['id']] as $val) {
                        $groups[$group_key]['attributes'][$attr_key]['values'][] = $val;
                    }
                } elseif ($attr['attribute_type'] == 'boolean') {
                    foreach($atribute_value[$attr['id']] as $val) {
                        $groups[$group_key]['attributes'][$attr_key]['values'][] = $val;
                    }
                } else {
                    $min = 999999;
                    $max = 0;
                    foreach($atribute_value[$attr['id']] as $val) {
                        $processed_val = floatval($val['value']);
                        if ($processed_val<$min) $min=floor($processed_val);
                        if ($processed_val>$max) $max=ceil($processed_val);
                    }
                    if ($max>0 && $max!==$min) {
                        $groups[$group_key]['attributes'][$attr_key]['values']['min'] = $min;
                        $groups[$group_key]['attributes'][$attr_key]['values']['max'] = $max;
                    }
                }
            }
        }
    }

    return $groups;
}
