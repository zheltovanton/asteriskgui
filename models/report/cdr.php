<?php

$config = include("../../db/config.php");

class ReportCdr {
	public $calldate;
	public $clid;
	public $src;
	public $dst;
	public $dcontext;
	public $duration;
	public $billsec; 
	public $disposition;
	public $dstchannel;
	public $lastapp;
	public $lastdata;
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
	$result->dstchannel = $row["dstchannel"];
	$result->lastapp = $row["lastapp"];
	$result->lastdata = $row["lastdata"];

	return $result;
    }

    public function getAll($filter) {
	$text = $filter["text"];

        //$callstart = implode(",", $filter["start"]);
        //$callstart = substr($callstart, 0, strpos($callstart, '('));
	$callstart = date("Y-m-d", strtotime($filter["start"]));
	if ($callstart=="1970-01-01") {
		$callstart=date("Y-m-d");
	}
        $start = $callstart;

        //$callend = implode(",", $filter["end"]);
        //$callend = substr($callend, 0, strpos($callend, '('));
	$callend = date("Y-m-d", strtotime($filter["end"]));
	if ($callend=="1970-01-01") {
		$callend=date("Y-m-d");
	}
        $end = $callend;


        $sql = "SELECT 
		calldate,
		clid,
		src,
		dst,
		dcontext,
		dstchannel,
		lastapp,
		lastdata,
		duration,
		billsec, 
		recordingfile,
		disposition
		FROM cdr where (calldate BETWEEN '$start 00:00:00' AND '$end 23:59:59')
		and (concat(clid, src, dst, dcontext, dstchannel, lastapp, lastdata) like '%$text%')
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