<?php
include('db.php');

function getSems() {
    global $db;
    $stm = $db->query("SELECT * FROM sems ORDER BY sem_code DESC");
    return $stm->fetchAll(PDO::FETCH_OBJ);
}

function getSemName($sem_code) {
    global $db;
    $stm = $db->prepare("SELECT * FROM sems WHERE sem_code=?");
    $stm->execute([$sem_code]);
    return $stm->fetch(PDO::FETCH_OBJ)->sem;
}

function generate($sem_code) {
    global $db;
    $stm = $db->prepare("SELECT si.idnum, si.lname, si.fname, si.mi, se.enrol_id, se.rate, si.addt FROM stud_info si
        LEFT JOIN stud_enrol se ON se.idnum = si.idnum
        WHERE se.sem_code=? AND se.en_status<>'withdrawn'");
    $stm->execute([$sem_code]);
    return $stm->fetchAll(PDO::FETCH_OBJ);
}

function computeUnits($idnum, $sem_code) {
    global $db;
    $stm = $db->prepare("SELECT SUM(cl.punits) AS 'units' FROM mdc.class cl 
            LEFT JOIN sub_enrol se ON se.class_code = cl.class_code
            WHERE se.idnum = :idnum AND se.sem_code = :sem_code");
    $stm->execute([
        ':idnum' => $idnum,
        ':sem_code' => $sem_code
    ]);

    return $stm->fetch(PDO::FETCH_OBJ);
}

function getFees($idnum, $sem_code) {
    global $db;
    $stm = $db->prepare("SELECT acc.acct_title, acc.amount FROM acct_item acc
        LEFT JOIN stud_enrol se ON se.enrol_id = acc.enrol_id
        WHERE se.idnum = :idnum AND se.sem_code = :sem_code");
    $stm->execute([':idnum'=>$idnum, ':sem_code'=>$sem_code]);
    $data = $stm->fetchAll(PDO::FETCH_OBJ);
    $output = [];
    foreach($data as $d) {
        $output[$d->acct_title] = $d->amount;
    }
    return $output;
}

function getAmount($title, $fees) {
    return array_key_exists($title, $fees) ? $fees[$title] : 0;
}