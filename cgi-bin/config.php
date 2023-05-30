<?php
session_start();

function db ()
{
    $username = "doadmin";
    $password = "AVNS__ifhVbONAt9Xjb95fkk";
    $host = "dbaas-db-10581460-do-user-13908232-0.b.db.ondigitalocean.com";
    $port = "25060";
    $database = "defaultdb";
    $sslmode = "REQUIRED";

    $db = new PDO(
            "mysql://$username:$password@$host:$port/$database?ssl-mode=$sslmode");
    return $db;
}

$db = db();

$time = time();

$weekdays = array(
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
        "Sunday"
);
$months = array(
        1 => "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
);

$domain = "cncofarmersmarket.com";

// USER SETTINGS
$useStore = 1;
setlocale(LC_MONETARY, "en_US");

$info = $db->prepare("SELECT * FROM generalInfo WHERE id = ?");
$info->execute(array(
        '1'
));
$infoR = $info->fetch();
$SAICNtax = ($infoR['SAICN_taxRate'] / 100);
$CNSAItax = ($infoR['CNSAI_taxRate'] / 100);
$FMFee = ($infoR['fmFee'] / 100);
$boothFee = $infoR['boothFee'];
$stateTaxId = $infoR['stateTaxId'];
$boardEmail = $infoR['boardEmail'];
$coordinatorEmail = $infoR['coordinatorEmail'];
$siteTitle = html_entity_decode($infoR['siteTitle'], ENT_QUOTES);

// When the store is open. 1 = monday, 2 = tuesday, etc...
$dayOfTheWeek = date('N', $time);
$firstDayOpen = 1;
$lastDayOpen = 4;
$storeOpen = ($dayOfTheWeek >= $firstDayOpen && $dayOfTheWeek <= $lastDayOpen) ? 1 : 0;

// *** Time zone ***
date_default_timezone_set('America/Chicago');

// *** page settings ***
$page = (filter_input(INPUT_GET, 'page', FILTER_SANITIZE_STRING)) ? filter_input(
        INPUT_GET, 'page', FILTER_SANITIZE_STRING) : "Home";
if (! file_exists("pages/" . $page . ".php")) {
    $page = "Home";
}

// *** User settings ***
$myId = 0;
$vendorId = 0;
$custId = 0;
// *** Log out ***
if (filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING) == 'yep') {
    $_SESSION['myId'] = 0;
    setcookie("staySignedIn", '0', $time - 1209600, "/", $domain, 0);
}

// *** Sign in ***
$loginErr = "x";
if (filter_input(INPUT_POST, 'login', FILTER_SANITIZE_NUMBER_INT) == "1") {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $login1stmt = $db->prepare("SELECT id, salt FROM board WHERE logInName = ?");
    $login1stmt->execute(array(
            $name
    ));
    $login1row = $login1stmt->fetch();
    if ($login1row) {
        $salt = $login1row['salt'];
        if ($salt >= 1) {
            $pwd = filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING);
            $hidepwd = hash('sha512', ($salt . $pwd), FALSE);
            $login2stmt = $db->prepare(
                    "SELECT id FROM board WHERE logInName = ? AND password = ?");
            $login2stmt->execute(array(
                    $name,
                    $hidepwd
            ));
            $login2row = $login2stmt->fetch();
            if ($login2row) {
                $li2Id = $login2row['id'];
                if ($li2Id >= 1) {
                    setcookie("staySignedIn", $li2Id, $time + 1209600, "/",
                            $domain, 0); // set for 14 days
                    $_SESSION['myId'] = $li2Id;
                } else {
                    $loginErr = "Your email / password combination isn't correct.";
                }
            }
        } else {
            $loginErr = "Your email / password combination isn't correct.";
        }
    }
}

// *** create customer log in ***
if (filter_input(INPUT_POST, 'createLogIn', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $displayName = filter_input(INPUT_POST, 'displayName',
            FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address1 = filter_input(INPUT_POST, 'address1', FILTER_SANITIZE_STRING);
    $address2 = filter_input(INPUT_POST, 'address2', FILTER_SANITIZE_STRING);
    $logInName = filter_input(INPUT_POST, 'logInName', FILTER_SANITIZE_STRING);
    $pwd1 = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING);
    $pwd2 = filter_input(INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING);
    if ($pwd1 == $pwd2 && $pwd1 != "" && $pwd1 != " ") {
        $salt = mt_rand(100000, 999999);
        $hidepwd = hash('sha512', ($salt . $pwd1), FALSE);
        $l = $db->prepare(
                "INSERT INTO board VALUES(NULL,?,?,'customer','',?,?,?,'0')");
        $l->execute(array(
                $salt,
                $hidepwd,
                $displayName,
                $logInName,
                $email
        ));
        $l2 = $db->prepare(
                "SELECT id FROM board WHERE password = ? ORDER BY id DESC LIMIT 1");
        $l2->execute(array(
                $hidepwd
        ));
        $l2R = $l2->fetch();
        if ($l2R) {
            $boardId = $l2R['id'];
            $custUp = $db->prepare(
                    "INSERT INTO customers VALUES(NULL,?,?,?,?,?,?,'0')");
            $custUp->execute(
                    array(
                            $displayName,
                            $email,
                            $phone,
                            $address1,
                            $address2,
                            $boardId
                    ));
            $l3 = $db->prepare("SELECT id FROM customers WHERE boardId = ?");
            $l3->execute(array(
                    $boardId
            ));
            $l3R = $l3->fetch();
            if ($l3R) {
                $_SESSION['myId'] = $boardId;
                setcookie("staySignedIn", $boardId, $time + 1209600, "/",
                        $domain, 0);
            }
        }
    } else {
        $pwdResetErr = "Your passwords didn't match.";
        $page = "LogIn";
    }
}

if (! isset($_SESSION['myId'])) {
    if (! isset($_COOKIE['staySignedIn'])) {
        $myId = 0;
    } else {
        $myId = $_COOKIE['staySignedIn'];
    }
} else {
    $myId = $_SESSION['myId'];
}

$getV = $db->prepare("SELECT id FROM vendors WHERE boardId = ?");
$getV->execute(array(
        $myId
));
$getVR = $getV->fetch();
if ($getVR) {
    $vendorId = $getVR['id'];
}

$getC = $db->prepare("SELECT id FROM customers WHERE boardId = ?");
$getC->execute(array(
        $myId
));
$getCR = $getC->fetch();
if ($getCR) {
    $custId = $getCR['id'];
}

if ($page == "News") {
    $h = (filter_input(INPUT_GET, 'articleId', FILTER_SANITIZE_NUMBER_INT)) ? filter_input(
            INPUT_GET, 'articleId', FILTER_SANITIZE_NUMBER_INT) : 0;
    if ($h == 0) {
        $stmt = $db->query("SELECT * FROM news ORDER BY time DESC LIMIT 1");
        $row = $stmt->fetch();
        $artId = $row['id'];
        $artTitle = $row['title'];
        $ac = html_entity_decode($row['text'], ENT_QUOTES);
        if (strlen($ac) >= 250) {
            $subContent1 = substr($ac, 0, 249);
            $posContent1 = strripos($subContent1, " ");
            $artContent = substr($ac, 0, $posContent1);
        } else {
            $artContent = $ac;
        }
        $artPic = $row['pic1Name'];
    } else {
        $stmt = $db->prepare("SELECT * FROM news WHERE id = ?");
        $stmt->execute(array(
                $h
        ));
        $row = $stmt->fetch();
        $artId = $row['id'];
        $artTitle = $row['title'];
        $ac = html_entity_decode($row['text'], ENT_QUOTES);
        if (strlen($ac) >= 250) {
            $subContent1 = substr($ac, 0, 249);
            $posContent1 = strripos($subContent1, " ");
            $artContent = substr($ac, 0, $posContent1);
        } else {
            $artContent = $ac;
        }
        $artPic = $row['pic1Name'];
    }
}