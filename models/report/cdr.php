<?php

class ReportCdr {
	public $calldate;
	public $clid;
	public $src;
	public $dst;
	public $dcontext;
	public $duration;
	public $billsec; 
	public $disposition;
}

class ReportCdrRepository {

    protected $db;


    public function __construct(PDO $db) {
        $this->db = $db;
    }

     private function read($row) {
        $result = new ReportCdr();

        $result->calldate = $row["calldate"];
        $result->clid = $row["clid"];
        $result->src = $row["src"];
        $result->dst = $row["dst"];
    	$result->dcontext = $row["dcontext"];
    	$result->duration = $row["duration"];
    	$result->billsec = $row["billsec"];
        $result->recordingfile = $row["recordingfile"];
        $result->disposition = $row["disposition"];

	return $result;
    }

    public function getAll($filter) {
	$clid = $filter["clid"];
	$src = $filter["src"];
	$dst = $filter["dst"];
	$dcontext = $filter["dcontext"];
	//$mon = $this->monitor;
        $calldate = implode(",", $filter["calldate"]);
        $calldate = substr($calldate, 0, strpos($calldate, '('));
	$calldate = date("Y-m-d", strtotime($calldate));
	if ($calldate=="1970-01-01") {
		$calldate=date("Y-m-d");
	}
        $sql = "SELECT 
		calldate,
		clid,
		src,
		dst,
		dcontext,
		duration,
		billsec, 
		recordingfile,
		disposition
		FROM cdr where (calldate BETWEEN '$calldate 00:00:00' AND '$calldate 23:59:59')
		and  (clid like '%$clid%')
		and  (src like '%$src%')
		and  (dst like '%$dst%')
		and  (dcontext like '%$dcontext%')
		and ( ( (dcontext<>'from-queue-exten-only') and(lastapp<>'PlayTones') ) or (LEFT(src , 1)='1')) 
		order by calldate desc";
        $q = $this->db->prepare($sql);
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