<?php
namespace sameparts\php\db_query;

use config\database_list;
use config\db;
use config\table_helper\cities as tbl;
use config\table_helper\town as tbl2;
use config\table_helper\district as tbl3;
use config\table_helper\neighborhood as tbl4;
use matrix_library\php\db_helpers\results;

/*
 * tbl  cities -> İl
 * tbl2 town -> İlçe
 * tbl3 district -> Semt
 * tbl4 neighborhood -> Mahalle
 * */


class address extends helper {
    public db $db;
    public function __construct(){
       $this->db = new db(database_list::TURKEY_ADDRESS);
    }


    public function get_city(
        int $city_id = 0,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        $db = $this->db;
        if ($custom_where == ""){
            $custom_where = ($city_id > 0) ? $db->where->equals([tbl::ID => $city_id]) : "";
        }
        return $db->db_select(
            array(tbl::ALL),
            tbl::TABLE_NAME,
            "",
            "$custom_where",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public function get_town(
        int $city_id = 0,
        int $town_id = 0,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        $db = $this->db;
        if ($custom_where == ""){
            $custom_where = ($city_id > 0) ?  $db->where->equals([tbl2::CITY_ID => $city_id]) : "";
            $custom_where = ($town_id > 0) ?  $db->where->equals([tbl2::ID => $town_id]) : $custom_where;
        }
        return $db->db_select(
            array(tbl2::ALL),
            tbl2::TABLE_NAME,
            "",
            "$custom_where",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public function get_district(
        int $town_id = 0,
        int $district_id = 0,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        $db = $this->db;
        if ($custom_where == ""){
            $custom_where = ($town_id > 0) ? $db->where->equals([tbl3::TOWN_ID => $town_id]) : "";
            $custom_where = ($district_id > 0) ? $db->where->equals([tbl3::ID => $district_id]) : $custom_where;
        }
        return $db->db_select(
            array(tbl3::ALL),
            tbl3::TABLE_NAME,
            "",
            "$custom_where",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public function get_neighborhood(
        int $district_id = 0,
        int $neighborhood = 0,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        $db = $this->db;
        if ($custom_where == ""){
            $custom_where = ($district_id > 0) ?  $db->where->equals([tbl4::DISTRICT_ID => $district_id]) : "";
            $custom_where = ($neighborhood > 0) ? $db->where->equals([tbl4::ID => $neighborhood]) : $custom_where;
        }
        return $db->db_select(
            array(tbl4::ALL),
            tbl4::TABLE_NAME,
            "",
            "$custom_where",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public function get_address(
        int $city_id = 0,
        int $town_id = 0,
        int $district_id = 0,
        int $neighborhood_id = 0,
        string $custom_where = "",
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        /*
          * tbl  cities -> İl
          * tbl2 town -> İlçe
          * tbl3 district -> Semt
          * tbl4 neighborhood -> Mahalle
        */

        $db = $this->db;
        if ($custom_where == ""){
            if($city_id > 0 && $town_id > 0 && $district_id > 0 && $neighborhood_id > 0) {
                $custom_where = $db->where->equals([
                    tbl::ID => $city_id,
                    tbl2::ID => $town_id,
                    tbl3::ID => $district_id,
                    tbl4::ID => $neighborhood_id
                ]);
            }
        }

        return $db->db_select(
            array(
                tbl::CITY,
                tbl2::TOWN,
                tbl3::DISTRICT,
                tbl4::NEIGHBORHOOD,
            ),
            tbl::TABLE_NAME,
            $db->join->inner(array(
                tbl2::TABLE_NAME => [ tbl2::CITY_ID => tbl::ID],
                tbl3::TABLE_NAME => [ tbl3::TOWN_ID => tbl2::ID],
                tbl4::TABLE_NAME => [ tbl4::DISTRICT_ID => tbl3::ID],
            )),
            "$custom_where",
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );

    }

    public function get_multi_address(
        array $data = array(),
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{

        $db = $this->db;
        $where = "";
        $count = count($data) -1;
        $i = 0;
        foreach ($data as $value) {
            $where .='('.
                tbl::ID.' =  '.$value["city"].' and '.
                tbl2::ID.' = '.$value["town"].' and '.
                tbl3::ID.' = '.$value["district"].' and '.
                tbl4::ID.' = '.$value["neighborhood"].

                ')';
            if ($count > $i){
                $where .= ' OR ';
            }
            $i++;
        }

        return $db->db_select(
            array(
                $db->as_name(tbl::ID, "city_id"),
                $db->as_name(tbl2::ID, "town_id"),
                $db->as_name(tbl3::ID, "district_id"),
                $db->as_name(tbl4::ID, "neighborhood_id"),
                tbl::CITY,
                tbl2::TOWN,
                tbl3::DISTRICT,
                tbl4::NEIGHBORHOOD,
            ),
            tbl::TABLE_NAME,
            $db->join->inner(array(
                tbl2::TABLE_NAME => [ tbl2::CITY_ID => tbl::ID],
                tbl3::TABLE_NAME => [ tbl3::TOWN_ID => tbl2::ID],
                tbl4::TABLE_NAME => [ tbl4::DISTRICT_ID => tbl3::ID],
            )),
            $where,
            $group_by,
            $order_by,
            parent::check_limit($limit)
        );
    }

    public function get_neighborhood_names(
        array $data = array(),
        string $group_by = "",
        string $order_by = "",
        array $limit = [0, 0]
    ) : results{
        $db = $this->db;
        return $db->db_select([tbl4::ID,tbl4::NEIGHBORHOOD],tbl4::TABLE_NAME,where: $db->where->equals([tbl4::ID => $data]));
    }

}