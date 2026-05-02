<?php
if(session_status()===PHP_SESSION_NONE) session_start();

function requireLogin(){
    if(empty($_SESSION['uid'])){
        header('Location: /ironlog/index.php'); exit;
    }
}
function uid(){ return (int)$_SESSION['uid']; }
function uname(){ return $_SESSION['uname'] ?? ''; }
function ufull(){ return $_SESSION['ufull'] ?? ''; }

function flash($type,$msg){ $_SESSION['flash']=[$type,$msg]; }
function getFlash(){
    $f=$_SESSION['flash']??null;
    unset($_SESSION['flash']);
    return $f;
}
