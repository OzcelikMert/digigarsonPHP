<?php
namespace _superadmin\functions\product\set;

use _superadmin\sameparts\functions\sessions\get;
use config\db;
use config\settings;
use config\table_helper\product_categories as tbl;
use config\table_helper\products as tbl2;
use config\table_helper\product_option as tbl3;
use config\table_helper\product_option_items as tbl4;
use config\table_helper\product_linked_options as tbl5;

use matrix_library\php\operations\user;
use matrix_library\php\operations\variable;
use ReflectionClass;
use sameparts\php\ajax\echo_values;
use sameparts\php\helper\language_codes;

class post_keys {
    const BRANCH_ID_OWNER = "branch_id_owner",
        BRANCH_ID_TARGET = "branch_id_target";
}

class copy{
    private ReflectionClass $language;

    public function __construct(db $db, get $sessions, echo_values &$echo) {
        $this->check_values($db, $sessions, $echo);
        if($echo->status){
            $this->language = new ReflectionClass(new language_codes());
            $this->set($db, $sessions, $echo);
        }
    }

    private function set(db $db, get $sessions, echo_values &$echo){
        $table_products = $this->get_reflection_products();
        $table_product_categories = $this->get_reflection_product_categories();
        $table_product_options = $this->get_reflection_product_options();
        $table_product_option_items = $this->get_reflection_product_option_items();
        $table_product_linked_options = $this->get_reflection_product_linked_options();
        $categories_owner = $db->db_select(
            $table_product_categories,
            tbl::TABLE_NAME,
            where: $db->where->equals([
                tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID_OWNER)
            ])
        )->rows;

        $products_id = array();
        $categories_id = array();
        foreach ($categories_owner as $category_owner){
            $category_id = $category_owner["id"];
            unset($category_owner["id"]);
            $insert_id_category = $db->db_insert(
                tbl::TABLE_NAME,
                array_merge($category_owner, array(
                    tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID_TARGET)
                ))
            )->insert_id;

            array_push($categories_id, array(
                "new" => $insert_id_category,
                "old" => $category_id
            ));

            $products_owner = $db->db_select(
                $table_products,
                tbl2::TABLE_NAME,
                where: $db->where->equals([
                    tbl2::BRANCH_ID => user::post(post_keys::BRANCH_ID_OWNER),
                    tbl2::CATEGORY_ID => $category_id
                ])
            )->rows;

            foreach ($products_owner as $product_owner){
                $product_id = $product_owner["id"];
                unset($product_owner["id"]);
                $product_owner["image"] = (filter_var($product_owner["image"], FILTER_VALIDATE_URL))
                    ? $product_owner["image"]
                    : $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"]."/images/branches/".user::post(post_keys::BRANCH_ID_OWNER)."/product/".$product_owner["image"];
                $insert_id_product = $db->db_insert(
                    tbl2::TABLE_NAME,
                    array_merge($product_owner, array(
                        tbl2::BRANCH_ID => user::post(post_keys::BRANCH_ID_TARGET),
                        tbl2::CATEGORY_ID => $insert_id_category
                    ))
                )->insert_id;

                array_push($products_id, array(
                    "new" => $insert_id_product,
                    "old" => $product_id
                ));
            }
        }

        $options_owner = $db->db_select(
            $table_product_options,
            tbl3::TABLE_NAME,
            where: $db->where->equals([
                tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID_OWNER)
            ])
        )->rows;

        foreach ($options_owner as $option_owner){
            $option_id = $option_owner["id"];
            unset($option_owner["id"]);
            $insert_id_option = $db->db_insert(
                tbl3::TABLE_NAME,
                array_merge($option_owner, array(
                    tbl3::BRANCH_ID => user::post(post_keys::BRANCH_ID_TARGET)
                ))
            )->insert_id;

            $option_items_owner = $db->db_select(
                $table_product_option_items,
                tbl4::TABLE_NAME,
                where: $db->where->equals([
                tbl4::BRANCH_ID => user::post(post_keys::BRANCH_ID_OWNER),
                tbl4::OPTION_ID => $option_id
            ])
            )->rows;

            foreach ($option_items_owner as $option_item_owner) {
                $db->db_insert(
                    tbl4::TABLE_NAME,
                    array_merge($option_item_owner, array(
                        tbl4::BRANCH_ID => user::post(post_keys::BRANCH_ID_TARGET),
                        tbl4::OPTION_ID => $insert_id_option
                    ))
                );
            }

            $linked_options_owner = $db->db_select(
                $table_product_linked_options,
                tbl5::TABLE_NAME,
                where: $db->where->equals([
                    tbl5::BRANCH_ID => user::post(post_keys::BRANCH_ID_OWNER),
                    tbl5::OPTION_ID => $option_id
                ])
            )->rows;

            foreach ($linked_options_owner as $linked_option_owner) {
                $db->db_insert(
                    tbl5::TABLE_NAME,
                    array_merge($linked_option_owner, array(
                        tbl5::BRANCH_ID => user::post(post_keys::BRANCH_ID_TARGET),
                        tbl5::OPTION_ID => $insert_id_option
                    ))
                );
            }
        }

        foreach ($products_id as $id){
            $db->db_update(
                tbl5::TABLE_NAME,
                array(
                    tbl5::PRODUCT_ID => $id["new"]
                ),
                where: $db->where->equals([
                tbl5::BRANCH_ID => user::post(post_keys::BRANCH_ID_TARGET),
                tbl5::PRODUCT_ID   => $id["old"]
            ])
            );
        }

        foreach ($categories_id as $id){
            $db->db_update(
                tbl::TABLE_NAME,
                array(
                    tbl::MAIN_ID => $id["new"]
                ),
                where: $db->where->equals([
                    tbl::BRANCH_ID => user::post(post_keys::BRANCH_ID_TARGET),
                    tbl::MAIN_ID   => $id["old"]
                ])
            );
        }
    }

    private function get_reflection_product_categories() : array{
        $reflection = new ReflectionClass(new tbl());
        $columns = array();
        foreach($reflection->getConstants() as $column){
            if(
                $column == tbl::ALL ||
                $column == tbl::BRANCH_ID ||
                $column == tbl::TABLE_NAME
            ) continue;

            if($column == tbl::NAME){
                foreach($this->language->getConstants() as $language){
                    array_push($columns, $column.$language);
                }
                continue;
            }

            array_push($columns, $column);
        }
        return $columns;
    }

    private function get_reflection_products() : array{
        $reflection = new ReflectionClass(new tbl2());
        $columns = array();
        foreach($reflection->getConstants() as $column){
            if(
                $column == tbl2::ALL ||
                $column == tbl2::BRANCH_ID ||
                $column == tbl2::TABLE_NAME ||
                $column == tbl2::CATEGORY_ID
            ) continue;

            if($column == tbl2::NAME || $column == tbl2::COMMENT ){

                foreach($this->language->getConstants() as $language){
                    array_push($columns, $column.$language);
                }
                continue;
            }

            array_push($columns, $column);
        }
        return $columns;
    }

    private function get_reflection_product_options() : array{
        $reflection = new ReflectionClass(new tbl3());
        $columns = array();
        foreach($reflection->getConstants() as $column){
            if(
                $column == tbl3::ALL ||
                $column == tbl3::BRANCH_ID ||
                $column == tbl3::TABLE_NAME
            ) continue;

            if($column == tbl3::NAME){

                foreach($this->language->getConstants() as $language){
                    array_push($columns, $column.$language);
                }
                continue;
            }

            array_push($columns, $column);
        }
        return $columns;
    }

    private function get_reflection_product_option_items() : array{
        $reflection = new ReflectionClass(new tbl4());
        $columns = array();
        foreach($reflection->getConstants() as $column){
            if(
                $column == tbl4::ALL ||
                $column == tbl4::BRANCH_ID ||
                $column == tbl4::ID ||
                $column == tbl4::TABLE_NAME ||
                $column == tbl4::OPTION_ID
            ) continue;

            if($column == tbl4::NAME){

                foreach($this->language->getConstants() as $language){
                    array_push($columns, $column.$language);
                }
                continue;
            }

            array_push($columns, $column);
        }
        return $columns;
    }

    private function get_reflection_product_linked_options() : array{
        $reflection = new ReflectionClass(new tbl5());
        $columns = array();
        foreach($reflection->getConstants() as $column){
            if(
                $column == tbl5::ALL ||
                $column == tbl5::BRANCH_ID ||
                $column == tbl5::ID ||
                $column == tbl5::TABLE_NAME ||
                $column == tbl5::OPTION_ID
            ) continue;

            array_push($columns, $column);
        }
        return $columns;
    }

    private function check_values(db $db, get $sessions, echo_values &$echo){
        if(variable::is_empty(
            user::post(post_keys::BRANCH_ID_TARGET),
            user::post(post_keys::BRANCH_ID_OWNER)
        )){
            $echo->error_code = settings::error_codes()::EMPTY_VALUE;
        }

        if($echo->error_code != settings::error_codes()::SUCCESS) $echo->status = false;
    }
}