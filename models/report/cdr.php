<?php

$config = include("../../db/config.php");

class ReportCdr {
    public $calldate;
    public $clid;
    public $clidrb;
    public $src;
    public $cc;
    public $dst;
    public $ext;
    public $whois;
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
        if (strlen($row["clid"])<3) {
            $result->clid = $row["whois"];
        } else  {
            $result->clid = $row["clid"];
        }
        $result->src = $row["src"];
        if (strlen($row["clidrb"])>2) {
            $result->clidrb = "V";
        }
        $result->cc = $row["cc"];
        $result->src = $row["src"];

        $result->dst = $row["dst"];
        $result->ext = $row["ext"];
        $result->whois = $row["whois"];
        $result->dcontext = $row["dcontext"];
        $result->duration = $row["duration"];
        $result->billsec = $row["billsec"];
        $result->recordingfile = $row["recordingfile"];
        $result->dstchannel = $row["dstchannel"];
        $result->lastapp = $row["lastapp"];
        $result->lastdata = $row["lastdata"];
        if (strlen($row["cc"])>1) {
            $result->disposition = "ANSWERED";
        }else{
            $result->disposition = "NO ANSWER";
        }

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
          (SELECT min(cid_num) from cel where eventtype='ANSWER' and cdr.uniqueid=cel.linkedid) as cc,
          (SELECT max(cid_num) from cel where eventtype='ANSWER' and cdr.uniqueid=cel.linkedid) as whois,
          (SELECT max(cid_ani) from cel where eventtype='ANSWER' and cdr.uniqueid=cel.linkedid) as whois_man,
          min(calldate) as calldate, 
          src, dst, max(dst) as ext, 
          (SELECT cid_name from cel where cdr.uniqueid=cel.linkedid and cid_name like '%:%'  order by calldate desc limit 1) as clidrb, 
          (SELECT cid_name from cel where cdr.uniqueid=cel.linkedid and length(cid_name)>2 and cid_name not like '% %' order by calldate desc limit 1) as clid2,
          sum(duration) as duration, 
          max(disposition) as disposition, 
          channel, 
          RIGHT(LEFT(channel, 7),3) as number,
          dcontext, 
          did, 
          clid,
          recordingfile
		FROM cdr 
		where 
		((recordingfile LIKE 'q-%')or(recordingfile LIKE 'in-%')) and (calldate BETWEEN '$start 00:00:00' AND '$end 23:59:59')
		and (concat(clid, src, dst, dcontext, dstchannel, lastapp, lastdata) like '%$text%')
		and (dst!='s') and (dst!='t') and (dst!='i') and (dst!='hangup')and (dst not like 'ivr%')
		and (dst not like 'black%')
            GROUP BY cdr.recordingfile
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