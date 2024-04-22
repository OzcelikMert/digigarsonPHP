<?php
namespace sameparts\php\db_query;

use config\db;
use config\table_helper\branch_work_times as tbl;
use config\table_helper\branch_takeaway_address as tbl2;
use matrix_library\php\db_helpers\results;

class branch_info extends helper {

    public static function branch_work_times(
        db $db,
        int $branch_id,
    ) : results{
        return $db->db_select(
            tbl::ALL,
            tbl::TABLE_NAME,
            where: $db->where->equals(
                [tbl::BRANCH_ID => $branch_id]
            )
        );
    }

    public static function takeaway_accepted_neighborhoods(
        db $db,
        int $branch_id,
        array $limit = [0, 0],
    ) : results{
        return $db->db_select(
            array(tbl2::ID,tbl2::NEIGHBORHOOD_ID),
            tbl2::TABLE_NAME,
            where: $db->where->equals([tbl2::BRANCH_ID => $branch_id]),
            limit: parent::check_limit($limit)
        );
    }
}
