<?php

class ReportGroupExtCdr {
	public $calldate;
	public $src;
	public $count;
	public $medium; 
	public $sum_disposition;
}

class ReportGroupExtCdrRepository {

    protected $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

     private function read($row) {
        $result = new ReportGroupExtCdr();

        $result->calldate = $row["calldate"];
        $result->count = $row["count"];
        $result->src = $row["src"];
    	$result->medium = $row["medium"];
    	$result->sum_duration = $row["sum_duration"];

	return $result;
    }

    public function getAll($filter) {
	$src = $filter["src"];
	//$mon = $this->monitor;
        $calldate = implode(",", $filter["calldate"]);
        $calldate = substr($calldate, 0, strpos($calldate, '('));
	$calldate = date("Y-m-d", strtotime($calldate));
	if ($calldate=="1970-01-01") {
		$calldate=date("Y-m-d");
	}
        $sql = "SELECT 
			src,	
			count(*) as count, 
			sum(duration) as sum_duration, 
			sum(duration) / count(*)  as medium, 
			calldate
		FROM cdr where (calldate BETWEEN '$calldate 00:00:00' AND '$calldate 23:59:59')
		     and  (src like '%$src%')
		     and (CAST(src AS UNSIGNED)<100000)
		     and ( ( (dcontext<>'from-queue-exten-only') and(lastapp<>'PlayTones') )) 
		group by src 
		order by src";
        $q = $this->db->prepare($sql);
	error_log($sql.PHP_EOL);
        $q->execute();
        $rows = $q->fetchAll();

        $result = array();
        foreach($rows as $row) {
            array_push($result, $this->read($row));
        }
        return $result;
	//echo $result;
    }


}

?>