<?php
// =======================================================
// *** GENERAL FORMS ***
// =======================================================

// *** reset password ***
if (filter_input(INPUT_POST, 'pwdreset', FILTER_SANITIZE_NUMBER_INT) >= 1) {
    $pwd1 = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING);
    $pwd2 = filter_input(INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING);
    $rId = filter_input(INPUT_POST, 'pwdreset', FILTER_SANITIZE_NUMBER_INT);
    if ($pwd1 == $pwd2 && $pwd1 != "" && $pwd1 != " ") {
        $l = $db->prepare("SELECT salt FROM board WHERE id = ?");
        $l->execute(array(
                $rId
        ));
        $lRow = $l->fetch();
        $salt = $lRow['salt'];
        if ($salt == '0') {
            $salt = mt_rand(100000, 999999);
        }
        $hidepwd = hash('sha512', ($salt . $pwd1), FALSE);
        $pwdUp = $db->prepare(
                "UPDATE board SET salt = ?, password = ? WHERE id = ?");
        $pwdUp->execute(array(
                $salt,
                $hidepwd,
                $rId
        ));
    } else {
        $pwdResetErr = "Your passwords didn't match.";
        $page = "LogIn";
    }
}

// Feedback page
$submitted = "";
if (filter_input(INPUT_POST, 'feedbackUp', FILTER_SANITIZE_NUMBER_INT) == '1') {
    $feedName = filter_input(INPUT_POST, 'fName', FILTER_SANITIZE_STRING);
    $feedEmail = filter_input(INPUT_POST, 'fEmail', FILTER_SANITIZE_EMAIL);
    $feedPhone = filter_input(INPUT_POST, 'fPhone', FILTER_SANITIZE_STRING);
    $feedText = filter_input(INPUT_POST, 'fText', FILTER_SANITIZE_STRING);

    $feed = $db->prepare(
            "INSERT INTO feedback VALUES(NULL, ?, ?, ?, '','0',?,?,'0')");
    $feed->execute(array(
            $time,
            $feedName,
            $feedText,
            $feedPhone,
            $feedEmail
    ));
    $submitted = "<div style='text-align: center; color: #cc4541; font-size: 1.25em; margin:20px 0px 20px 0px;'>Thank you for your Feedback.</div>";
}

// Invoice page
$filled = array();
if (filter_input(INPUT_POST, 'tally', FILTER_SANITIZE_NUMBER_INT) == 1) {
    foreach ($_POST as $key => $val) {
        $v = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
        if ($v != "" && $v != " ") {
            if (preg_match("/^qty([1-9][0-9]*)$/", $key, $match)) {
                $q = $match[1];
                $setQ = $db->prepare(
                        "UPDATE onlineSales SET quantity = ? WHERE id = ?");
                $setQ->execute(array(
                        $v,
                        $q
                ));
            }
        }
    }
    foreach ($_POST as $key => $val) {
        $v = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
        if ($v != "" && $v != " ") {
            if (preg_match("/^filled([1-9][0-9]*)$/", $key, $match)) {
                $q = $match[1];
                $getF = $db->prepare(
                        "SELECT quantity, productId FROM onlineSales WHERE id = ?");
                $getF->execute(array(
                        $q
                ));
                $getFR = $getF->fetch();
                $qty = $getFR['quantity'];
                $productId = $getFR['productId'];

                $f = ($v <= $qty) ? $v : $qty;
                $setF = $db->prepare(
                        "UPDATE onlineSales SET filled = ? WHERE id = ?");
                $setF->execute(array(
                        $f,
                        $q
                ));

                $UP = $db->prepare(
                        "UPDATE product SET quantity = (quantity - ?) WHERE id = ?");
                $UP->execute(array(
                        $f,
                        $productId
                ));
            }
        }
    }
}

// Invoice page
if (filter_input(INPUT_POST, 'finalize', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $ckNumber = filter_input(INPUT_POST, 'ckNumber', FILTER_SANITIZE_NUMBER_INT);
    $subtotal = filter_input(INPUT_POST, 'subtotal',
            FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $delivery = filter_input(INPUT_POST, 'delivery',
            FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    foreach ($_POST as $k => $val) {
        $v = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
        if ($v != "" && $v != " ") {
            if (preg_match("/^order([1-9][0-9]*)$/", $k, $match)) {
                $q = $match[1];

                $getAmt = $db->prepare(
                        "SELECT vendorId, productId, price, filled FROM onlineSales WHERE id = ?");
                $getAmt->execute(array(
                        $q
                ));
                $gaR = $getAmt->fetch();
                $amt = ($gaR['price'] * $v);
                $vendorId = $gaR['vendorId'];
                $productId = $gaR['productId'];
                $fill = $gaR['filled'];

                $checkTax = $db->prepare(
                        "SELECT handlingOwnTaxes FROM vendors WHERE id = ?");
                $checkTax->execute(array(
                        $vendorId
                ));
                $ctR = $checkTax->fetch();
                $handlingOwnTaxes = $ctR['handlingOwnTaxes'];

                $taxR = $db->prepare("SELECT ref FROM product WHERE id = ?");
                $taxR->execute(array(
                        $productId
                ));
                $taxRR = $taxR->fetch();
                if ($taxRR) {
                    $taxRate = $taxRR['ref'];
                    $t = ($taxRate == "cnsai") ? ($amt * $CNSAItax) : ($amt *
                            $SAICNtax);
                } else {
                    $t = ($amt * $SAICNtax);
                    $taxRate = "saicn";
                }
                $f = ($amt * $FMFee);
                $a = ($handlingOwnTaxes == 1) ? $amt - $f : $amt - ($f + $t);

                if ($v == $fill) {
                    if ($handlingOwnTaxes == 0) {
                        $createX2 = $db->prepare(
                                "INSERT INTO transactions VALUES(NULL,?,?,'2',?,?,'0',?,'0',?,?)");
                        $createX2->execute(
                                array(
                                        $C,
                                        $q,
                                        $subtotal,
                                        $t,
                                        $time,
                                        $vendorId,
                                        $taxRate
                                ));
                    }

                    $createX1 = $db->prepare(
                            "INSERT INTO transactions VALUES(NULL,?,?,'1',?,?,'0','0','0',?,'0')");
                    $createX1->execute(
                            array(
                                    $C,
                                    $q,
                                    $subtotal,
                                    $a,
                                    $vendorId
                            ));

                    $createX3 = $db->prepare(
                            "INSERT INTO transactions VALUES(NULL,?,?,'3',?,?,'0',?,'0',?,'0')");
                    $createX3->execute(
                            array(
                                    $C,
                                    $q,
                                    $subtotal,
                                    $f,
                                    $time,
                                    $vendorId
                            ));

                    $getX1 = $db->prepare(
                            "SELECT id FROM transactions WHERE custId = ? && onlineSalesId = ? && amount = ? ORDER BY id DESC LIMIT 1");
                    $getX1->execute(array(
                            $C,
                            $q,
                            $a
                    ));
                    $X1R = $getX1->fetch();
                    $xId = $X1R['id'];

                    $setXid = $db->prepare(
                            "UPDATE onlineSales SET transactionDate = ?, paid = '1', transactionsId = ?, ckNumber = ? WHERE id = ?");
                    $setXid->execute(array(
                            $time,
                            $xId,
                            $ckNumber,
                            $q
                    ));
                }
            }
        }
    }
    if ($delivery != 0) {
        $createX5 = $db->prepare(
                "INSERT INTO transactions VALUES(NULL,?,'0','5',?,?,'0',?,'0',?,'0')");
        $createX5->execute(array(
                $C,
                $subtotal,
                $delivery,
                $time,
                $vendorId
        ));
    }
}

// Legal page
if (filter_input(INPUT_POST, 'appUp', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $jobId = filter_input(INPUT_POST, 'jobId', FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $a = (filter_input(INPUT_POST, 'about', FILTER_SANITIZE_STRING)) ? filter_input(
            INPUT_POST, 'about', FILTER_SANITIZE_STRING) : "x";
    $r = (filter_input(INPUT_POST, 'resume', FILTER_SANITIZE_STRING)) ? filter_input(
            INPUT_POST, 'resume', FILTER_SANITIZE_STRING) : "x";
    $about = htmlEntities(trim($a), ENT_QUOTES);
    $resume = htmlEntities(trim($r), ENT_QUOTES);
    $k = $db->prepare(
            "INSERT INTO applications VALUES(NULL,?,?,?,?,?,?,?,'0','0')");
    $k->execute(
            array(
                    $jobId,
                    $name,
                    $address,
                    $email,
                    $phone,
                    $about,
                    $resume
            ));
    echo "<div style='text-align: center; color: #cc4541; font-size: 1.25em; margin-bottom: 20px;'>Your application has been submitted. Thank you.</div>";
}

// Legal page
if (filter_input(INPUT_POST, 'unsubscribe', FILTER_SANITIZE_NUMBER_INT) == '1') {
    $unsubEmail = filter_input(INPUT_POST, 'unsubEmail', FILTER_SANITIZE_STRING);
    $d = $db->prepare(
            "UPDATE vendors SET mailingList = ? WHERE agreementEmail = ?");
    $d->execute(array(
            '0',
            $unsubEmail
    ));
    echo "<div style='text-align: center; color: #cc4541; font-size: 1.25em; margin-bottom: 20px;'>The email address $unsubEmail has been removed from the mailing list</div>";
}

// LogIn page
if (filter_input(INPUT_GET, 'mailingList', FILTER_SANITIZE_STRING) == "remove") {
    $removeML = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_STRING);
    $ml = $db->prepare(
            "UPDATE vendors SET mailingList = '0' WHERE agreementEmail = ?");
    $ml->execute(array(
            $removeML
    ));
    $err = "You have been removed from the mailing list. You should no longer receive emails from the Cheyenne County Farmers Market.";
}

// LogIn page
if (filter_input(INPUT_GET, 'ver', FILTER_SANITIZE_STRING)) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
    $ver = filter_input(INPUT_GET, 'ver', FILTER_SANITIZE_STRING);
    $a = $db->prepare(
            "SELECT logInName, email, verifyTime FROM board WHERE id = ?");
    $a->execute(array(
            $id
    ));
    $aRow = $a->fetch();
    $lin = $aRow['logInName'];
    $e = $aRow['email'];
    $vt = $aRow['verifyTime'];
    $check = hash('sha512', ($vt . $lin . $e), FALSE);
    if ($check == $ver) {
        $displayReset = 1;
        $rId = $id;
        $b = $db->prepare("UPDATE board SET verifyTime = ? WHERE id = ?");
        $b->execute(array(
                '0',
                $id
        ));
    } else {
        $err = "There was a problem processing your request.";
    }
}

// PWReset page
if (filter_input(INPUT_POST, 'sendEmail', FILTER_SANITIZE_STRING) == "1") {
    $user = filter_input(INPUT_POST, 'logInName', FILTER_SANITIZE_STRING);
    $email = strtolower(
            filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING));
    $a = $db->prepare("SELECT COUNT(*) FROM board WHERE email=? && logInName=?");
    $a->execute(array(
            $email,
            $user
    ));
    $aRow = $a->fetch();
    if ($aRow[0] == 0) {
        echo "That email address or user name is incorrect";
        $t = "forgot";
    } elseif ($aRow[0] == 1) {
        echo "Sending a verification email to this address. Please click on the link in the email to reset your password.<br>If you don't see the email after a minute or so, check your junk folder.";
        $c = $db->prepare("SELECT id FROM board WHERE email=? && logInName=?");
        $c->execute(array(
                $email,
                $user
        ));
        $cRow = $c->fetch();
        $id = $cRow["id"];
        sendPWResetEmail($id, $user, $email, $time);
        $d = $db->prepare("UPDATE board SET verifyTime = ? WHERE id=?");
        $d->execute(array(
                $time,
                $id
        ));
    } elseif ($aRow[0] >= 2) {
        echo "There is a problem with this email address, please contact an admin to re-set up your account.";
        $b = $db->prepare("DELETE FROM board WHERE email = ?");
        $b->execute(array(
                $email
        ));
    } else {
        echo "Something crazy happened. Please try entering your email address again.";
        $t = "forgot";
    }
}

// Store page
// Add to cart
if (filter_input(INPUT_POST, 'toCart', FILTER_SANITIZE_NUMBER_INT)) {
    $prodId = filter_input(INPUT_POST, 'toCart', FILTER_SANITIZE_NUMBER_INT);
    $vId = filter_input(INPUT_POST, 'vId', FILTER_SANITIZE_NUMBER_INT);
    $prodQty = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);
    $a = qtyAvailable($prodId, $db);
    $qty = ($prodQty <= $a) ? $prodQty : $a;
    $toCart = $db->prepare(
            "INSERT INTO onlineSales VALUES(NULL,?,?,?,?,?,?,'0','0','1','0','0','0')");
    $toCart->execute(array(
            $vId,
            $prodId,
            $custId,
            $time,
            $qty,
            $price
    ));
}

// Store page
// Process Cart
if (filter_input(INPUT_POST, 'placeOrder', FILTER_SANITIZE_NUMBER_INT) == $custId) {
    $dm = filter_input(INPUT_POST, 'deliveryMethod', FILTER_SANITIZE_NUMBER_INT);

    $vSales = array();
    $getC = $db->prepare(
            "SELECT id, vendorId, productId, quantity FROM onlineSales WHERE custId = ? && inCart = '1'");
    $getC->execute(array(
            $custId
    ));
    while ($getCR = $getC->fetch()) {
        $gcId = $getCR['id'];
        $vSales[] = $getCR['vendorId'];
        $productId = $getCR['productId'];
        $quantity = $getCR['quantity'];

        $order1 = $db->prepare(
                "UPDATE onlineSales SET inCart = '0', transactionDate = ?, deliveryMethod = ? WHERE id = ?");
        $order1->execute(array(
                $time,
                $dm,
                $gcId
        ));
    }

    $ven = array_unique($vSales);

    foreach ($ven as $k => $v) {
        vSalesEmail($v, $db);
    }
}

// Store page
// make changes to an existing in cart order
if (filter_input(INPUT_POST, 'updateOrder', FILTER_SANITIZE_NUMBER_INT) ==
        $custId) {
    foreach ($_POST as $key => $val) {
        $v = filter_var($val, FILTER_SANITIZE_NUMBER_INT);
        if (preg_match("/^quantity([1-9][0-9]*)$/", $key, $match)) {
            $q = $match[1];
            if ($v == 0) {
                $order2 = $db->prepare("DELETE FROM onlineSales WHERE id = ?");
                $order2->execute(array(
                        $q
                ));
            } else {
                $US = $db->prepare(
                        "UPDATE onlineSales SET quantity = ? WHERE id = ?");
                $US->execute(array(
                        $v,
                        $q
                ));
            }
        }
        if (preg_match("/^del([1-9][0-9]*)$/", $key, $match)) {
            $q = $match[1];
            if ($v == 1) {
                $order2 = $db->prepare("DELETE FROM onlineSales WHERE id = ?");
                $order2->execute(array(
                        $q
                ));
            }
        }
    }
}

// Store page
// delete and order
if (filter_input(INPUT_POST, 'deleteOrder', FILTER_SANITIZE_NUMBER_INT) ==
        $custId) {
    $order3 = $db->prepare(
            "DELETE FROM onlineSales WHERE custId = ? && inCart = '1'");
    $order3->execute(array(
            $custId
    ));
}

// Store page
// change delivery method settings
if (filter_input(INPUT_POST, 'deliveryUp', FILTER_SANITIZE_NUMBER_INT) == 1) {
    $nId = 0;
    foreach ($_POST as $key => $val) {
        $v = filter_var($val, FILTER_SANITIZE_STRING);
        if (preg_match("/^[a-zA-Z]+([1-9][0-9]*)$/", $key, $match)) {
            $q = ($match[1] == 0) ? $nId : $match[1];

            if ($q == 0) {
                $delCreate = $db->query(
                        "INSERT INTO deliveryMethods VALUES(NULL,'','','0','0')");
                $delGet = $db->query(
                        "SELECT id FROM deliveryMethods ORDER BY id DESC LIMIT 1");
                $delGetR = $delGet->fetch();
                $nId = $delGetR['id'];
            }
        }
        if (preg_match("/^deliveryMethod([1-9][0-9]*)$/", $key, $match)) {
            $q = ($match[1] == 0) ? $nId : $match[1];

            $US = $db->prepare(
                    "UPDATE deliveryMethods SET deliveryMethod = ? WHERE id = ?");
            $US->execute(array(
                    $v,
                    $q
            ));
        }
        if (preg_match("/^deliveryFee([1-9][0-9]*)$/", $key, $match)) {
            $q = ($match[1] == 0) ? $nId : $match[1];

            $US = $db->prepare(
                    "UPDATE deliveryMethods SET deliveryFee = ? WHERE id = ?");
            $US->execute(array(
                    $v,
                    $q
            ));
        }
        if (preg_match("/^del([1-9][0-9]*)$/", $key, $match)) {
            $q = $match[1];
            if ($v == 1) {
                $order2 = $db->prepare(
                        "DELETE FROM deliveryMethods WHERE id = ?");
                $order2->execute(array(
                        $q
                ));
            }
        }
    }
}

// =======================================================
// *** IS BOARD FORMS ***
// =======================================================

if (isBoard($myId)) {
    // Links page
    if (filter_input(INPUT_POST, 'linkDown', FILTER_SANITIZE_NUMBER_INT)) {
        $linkId = filter_input(INPUT_POST, 'linkDown',
                FILTER_SANITIZE_NUMBER_INT);
        $b = $db->prepare("DELETE FROM links WHERE id = ?");
        $b->execute(array(
                $linkId
        ));
    }

    // Links page
    if (filter_input(INPUT_POST, 'linkUp', FILTER_SANITIZE_STRING)) {
        $linkId = filter_input(INPUT_POST, 'linkUp', FILTER_SANITIZE_STRING);
        $t = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $l = filter_input(INPUT_POST, 'link', FILTER_SANITIZE_STRING);
        $d = filter_var(htmlEntities(trim($_POST['description']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);

        if ($linkId == 'new') {
            $b = $db->prepare(
                    "INSERT INTO links VALUES" . "(NULL,?,?,?,'0','0')");
            $b->execute(array(
                    $t,
                    $d,
                    $l
            ));
        } else {
            $b = $db->prepare(
                    "UPDATE links SET title = ?, description = ?, link = ? WHERE id = ?");
            $b->execute(array(
                    $t,
                    $d,
                    $l,
                    $linkId
            ));
        }
    }

    // Invoice page
    if (filter_input(INPUT_POST, 'editInfo', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $editId = filter_input(INPUT_POST, 'editInfo',
                FILTER_SANITIZE_NUMBER_INT);
        $editName = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $editEmail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $editPhone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $editAddress1 = filter_input(INPUT_POST, 'address1',
                FILTER_SANITIZE_STRING);
        $editAddress2 = filter_input(INPUT_POST, 'address2',
                FILTER_SANITIZE_STRING);

        $setInfo = $db->prepare(
                "UPDATE customers SET name = ?, email = ?, phone = ?, address1 = ?, address2 = ? WHERE id = ?");
        $setInfo->execute(
                array(
                        $editName,
                        $editEmail,
                        $editPhone,
                        $editAddress1,
                        $editAddress2,
                        $editId
                ));
    }

    // Feedback page
    if (filter_input(INPUT_POST, 'deleteReply', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $id = filter_input(INPUT_POST, 'deleteReply', FILTER_SANITIZE_NUMBER_INT);
        $i = $db->prepare("DELETE FROM feedback WHERE id = ?");
        $i->execute(array(
                $id
        ));
    }

    // Feedback page
    if (filter_input(INPUT_POST, 'dReply', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $id = filter_input(INPUT_POST, 'dReply', FILTER_SANITIZE_NUMBER_INT);
        $i = $db->prepare("UPDATE feedback SET hidden = ? WHERE id = ?");
        $i->execute(array(
                '1',
                $id
        ));
    }

    // Feedback page
    if (filter_input(INPUT_POST, 'fReply', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $id = filter_input(INPUT_POST, 'fReply', FILTER_SANITIZE_NUMBER_INT);
        $reply = filter_input(INPUT_POST, 'reply', FILTER_SANITIZE_STRING);
        $i = $db->prepare(
                "UPDATE feedback SET reply = ?, repliedTo = ? WHERE id = ?");
        $i->execute(array(
                $reply,
                '1',
                $id
        ));

        $j = $db->prepare("SELECT * FROM feedback WHERE id =?");
        $j->execute(array(
                $id
        ));
        $jRow = $j->fetch();
        $fTime = $jRow['time'];
        $fName = $jRow['name'];
        $fText = $jRow['feedback'];
        $fReply = $jRow['reply'];
        $fEmail = $jRow['email'];
        $fPhone = $jRow['phone'];

        $mess = "$fName\n$fEmail\n$fPhone\n\nYour feedback sent in @ " .
                date("D, M jS @ g:i a", $fTime) .
                ":\n$fText\n\nReply:\n$fReply\n\n\nThe Farmer\'s Market Board";
        $message = wordwrap(
                html_entity_decode($mess, ENT_QUOTES | ENT_XML1, 'UTF-8'), 70);
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
        $headers .= "From: Cheyenne Co Farmer's Market <board@cncofarmersmarket.com>" .
                "\r\n";
        mail($fEmail,
                "Reply to your feedback on the Cheyenne Co Farmer's Market website",
                $message, $headers);
    }

    // Feedback page
    if (filter_input(INPUT_POST, 'faqDown', FILTER_SANITIZE_NUMBER_INT)) {
        $faqId = filter_input(INPUT_POST, 'faqDown', FILTER_SANITIZE_NUMBER_INT);
        $b = $db->prepare("DELETE FROM faq WHERE id = ?");
        $b->execute(array(
                $faqId
        ));
    }

    // Feedback page
    if (filter_input(INPUT_POST, 'faqUp', FILTER_SANITIZE_STRING)) {
        $faqId = filter_input(INPUT_POST, 'faqUp', FILTER_SANITIZE_STRING);
        $t = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $d = filter_var(htmlEntities(trim($_POST['description']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);

        if ($faqId == 'new') {
            $b = $db->prepare("INSERT INTO faq VALUES" . "(NULL,?,?,'0')");
            $b->execute(array(
                    $t,
                    $d
            ));
        } else {
            $b = $db->prepare(
                    "UPDATE faq SET title = ?, description = ? WHERE id = ?");
            $b->execute(array(
                    $t,
                    $d,
                    $faqId
            ));
        }
    }

    // Board page
    if (filter_input(INPUT_POST, 'updateBoard', FILTER_SANITIZE_STRING)) {
        $position = filter_input(INPUT_POST, 'updateBoard',
                FILTER_SANITIZE_STRING);
        $newId = filter_input(INPUT_POST, 'displayName', FILTER_SANITIZE_STRING);
        $get1 = $db->prepare("SELECT id FROM board WHERE position = ?");
        $get1->execute(array(
                $position
        ));
        $get1r = $get1->fetch();
        $oldId = ($get1r) ? $get1r['id'] : 0;
        $newType = 'customer';
        if (isVendor($oldId)) {
            $newType = 'vendor';
        }
        $up1 = $db->prepare(
                "UPDATE board SET role = ?, position = '' WHERE id = ?");
        $up1->execute(array(
                $newType,
                $oldId
        ));
        $up2 = $db->prepare(
                "UPDATE board SET role = 'board', position = ? WHERE id = ?");
        $up2->execute(array(
                $position,
                $newId
        ));
    }

    // Board page
    if (filter_input(INPUT_POST, 'payVendors', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $vendorId = filter_input(INPUT_POST, 'payVendors',
                FILTER_SANITIZE_NUMBER_INT);
        $paid = filter_input(INPUT_POST, 'paid', FILTER_SANITIZE_NUMBER_INT);
        $ckNumber = filter_input(INPUT_POST, 'ckNumber',
                FILTER_SANITIZE_NUMBER_INT);

        if ($paid == 1) {
            $getT = $db->prepare(
                    "SELECT id, amount FROM transactions WHERE vendorId = ? && amount > paid && xType = ?");
            $getT->execute(array(
                    $vendorId,
                    '1'
            ));
            while ($getTR = $getT->fetch()) {
                $tId = $getTR['id'];
                $tAmount = $getTR['amount'];
                $getT2 = $db->prepare(
                        "UPDATE transactions SET paid = ?, ckNumber = ? WHERE id = ?");
                $getT2->execute(array(
                        $tAmount,
                        $ckNumber,
                        $tId
                ));
            }
        }
    }

    // Board page
    if (filter_input(INPUT_POST, 'payTaxes', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $paid = filter_input(INPUT_POST, 'paid', FILTER_SANITIZE_NUMBER_INT);
        $ckNumber = filter_input(INPUT_POST, 'ckNumber',
                FILTER_SANITIZE_NUMBER_INT);
        $start = filter_input(INPUT_POST, 'start', FILTER_SANITIZE_NUMBER_INT);
        $end = filter_input(INPUT_POST, 'end', FILTER_SANITIZE_NUMBER_INT);

        if ($paid == 1) {
            $getT = $db->prepare(
                    "SELECT id, amount FROM transactions WHERE xType = ? && paidDate > ? && paidDate < ?");
            $getT->execute(array(
                    '2',
                    $start,
                    $end
            ));
            while ($getTR = $getT->fetch()) {
                $tId = $getTR['id'];
                $tAmount = $getTR['amount'];
                $getT2 = $db->prepare(
                        "UPDATE transactions SET paid = ?, ckNumber = ? WHERE id = ?");
                $getT2->execute(array(
                        $tAmount,
                        $ckNumber,
                        $tId
                ));
            }
        }
    }

    // Board page
    if (filter_input(INPUT_POST, 'MSup', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $d1 = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_NUMBER_INT);
        $d2 = explode("-", $d1);
        $date = mktime(12, 0, 0, $d2[1], $d2[2], $d2[0]);
        $vendor = filter_input(INPUT_POST, 'vendor', FILTER_SANITIZE_NUMBER_INT);
        $SAICNsales = filter_input(INPUT_POST, 'SAICNsales',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $CNSAIsales = filter_input(INPUT_POST, 'CNSAIsales',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $checkNum = filter_input(INPUT_POST, 'checkNum',
                FILTER_SANITIZE_NUMBER_INT);
        $feeOnly = (filter_input(INPUT_POST, 'feeOnly',
                FILTER_SANITIZE_NUMBER_INT) == 1) ? 1 : 0;

        $SAICNamount = ($SAICNsales * $SAICNtax);
        $CNSAIamount = ($CNSAIsales * $CNSAItax);
        if ($FMFee >= .001) {
            $FMamount = (($SAICNamount + $CNSAIamount) * $FMFee);
        } else {
            $FMamount = $boothFee;
        }

        if ($feeOnly == 0) {
            if ($SAICNamount >= 0.01) {
                $getT1 = $db->prepare(
                        "INSERT INTO transactions VALUES(NULL,'0','0','2',?,?,'0',?,?,?,?)");
                $getT1->execute(
                        array(
                                $sales,
                                $SAICNamount,
                                $date,
                                $checkNum,
                                $vendor,
                                "saicn"
                        ));
            }
            if ($CNSAIamount >= 0.01) {
                $getT1 = $db->prepare(
                        "INSERT INTO transactions VALUES(NULL,'0','0','2',?,?,'0',?,?,?,?)");
                $getT1->execute(
                        array(
                                $sales,
                                $CNSAIamount,
                                $date,
                                $checkNum,
                                $vendor,
                                "cnsai"
                        ));
            }
        }

        $getT2 = $db->prepare(
                "INSERT INTO transactions VALUES(NULL,'0','0','3',?,?,'0',?,?,?,'0')");
        $getT2->execute(array(
                $sales,
                $FMamount,
                $date,
                $checkNum,
                $vendor
        ));
        $mseOpen = 1;
    }

    // Board page
    if (filter_input(INPUT_POST, 'donation', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $amount = filter_input(INPUT_POST, 'donationAmt',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $ckNumber = filter_input(INPUT_POST, 'ckNumber',
                FILTER_SANITIZE_NUMBER_INT);

        $getT2 = $db->prepare(
                "INSERT INTO transactions VALUES(NULL,'0','0','4','0.00',?,'0',?,?,'0','0')");
        $getT2->execute(array(
                $amount,
                $time,
                $ckNumber
        ));
    }

    // Board page
    if (filter_input(INPUT_POST, 'giUp', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $saicntax = filter_input(INPUT_POST, 'saicntaxRate',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $cnsaitax = filter_input(INPUT_POST, 'cnsaitaxRate',
                FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $fmf = filter_input(INPUT_POST, 'fmFee', FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION);
        $boo = filter_input(INPUT_POST, 'boothFee', FILTER_SANITIZE_NUMBER_FLOAT,
                FILTER_FLAG_ALLOW_FRACTION);
        $sta = strtolower(
                filter_input(INPUT_POST, 'stateTaxId', FILTER_SANITIZE_STRING));
        $boa = strtolower(
                filter_input(INPUT_POST, 'boardEmail', FILTER_SANITIZE_STRING));
        $coo = strtolower(
                filter_input(INPUT_POST, 'coordinatorEmail',
                        FILTER_SANITIZE_STRING));
        $st = htmlentities(
                filter_input(INPUT_POST, 'siteTitle', FILTER_SANITIZE_STRING),
                ENT_QUOTES);

        $giUp = $db->prepare(
                "UPDATE generalInfo SET SAICN_taxRate = ?, CNSAI_taxRate = ?, fmFee = ?, boothFee = ?, stateTaxId = ?, boardEmail = ?, coordinatorEmail = ?, siteTitle = ? WHERE id = ?");
        $giUp->execute(
                array(
                        $saicntax,
                        $cnsaitax,
                        $fmf,
                        $boo,
                        $sta,
                        $boa,
                        $coo,
                        $st,
                        '1'
                ));
    }

    // Calendar page
    if (filter_input(INPUT_POST, 'changeEvent', FILTER_SANITIZE_STRING)) {
        $eId = filter_input(INPUT_POST, 'changeEvent', FILTER_SANITIZE_STRING); // event
                                                                                // id,
                                                                                // or
                                                                                // 'new'
        $eDate = filter_input(INPUT_POST, 'eDate', FILTER_SANITIZE_STRING); // date
                                                                            // portion
                                                                            // of
                                                                            // time,
                                                                            // format
                                                                            // 2019-05-30
        $eTime = filter_input(INPUT_POST, 'eTime', FILTER_SANITIZE_STRING); // time
                                                                            // portion
                                                                            // of
                                                                            // time.
                                                                            // format
                                                                            // 05:30
                                                                            // 24-hour
                                                                            // time.
        $a1 = htmlEntities(trim($_POST['eventTitle']), ENT_QUOTES);
        $eventTitle = filter_var($a1, FILTER_SANITIZE_STRING); // event title
        $calPic = filter_input(INPUT_POST, 'calPic', FILTER_SANITIZE_STRING); // picture
                                                                              // name
                                                                              // to
                                                                              // use,
                                                                              // 'new',
                                                                              // or
                                                                              // 'noPic.jpg'
        $a2 = htmlEntities(trim($_POST['eventDesc']), ENT_QUOTES);
        $eventDesc = filter_var($a2, FILTER_SANITIZE_STRING); // event text

        $d = explode("-", $eDate);
        $t = explode(":", $eTime);
        $eventTime = mktime($t[0], $t[1], 00, $d[1], $d[2], $d[0]);

        if ($eId == 'new') {
            $e = $db->prepare(
                    "INSERT INTO calendar VALUES" .
                    "(NULL, ?, ?, ?, ?, '0', '0')");
            $e->execute(array(
                    $eventTime,
                    $eventTitle,
                    $eventDesc,
                    $calPic
            ));

            $e2 = $db->prepare(
                    "SELECT id FROM calendar WHERE eventTime = ? AND eventTitle = ? AND eventDesc = ? AND picName = ? ORDER BY id DESC LIMIT 1");
            $e2->execute(array(
                    $eventTime,
                    $eventTitle,
                    $eventDesc,
                    $calPic
            ));
            $e2R = $e2->fetch();
            if ($e2R) {
                $eId = $e2R['id'];
            }
        } else {
            $g = $db->prepare(
                    "UPDATE calendar SET eventTime = ?, eventTitle = ?, eventDesc = ?, picName = ? WHERE id = ?");
            $g->execute(
                    array(
                            $eventTime,
                            $eventTitle,
                            $eventDesc,
                            $calPic,
                            $eId
                    ));
        }

        if ($calPic == 'new') {
            $tmpFile = $_FILES["image"]["tmp_name"];
            list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                    $tmpFile) : null;
            if ($width != null && $height != null) {
                $imageType = getPicType($_FILES["image"]['type']);
                $imageName = $time . "." . $imageType;
                processPic("$domain/img/calendar", $imageName, $tmpFile, 600,
                        150);
                $p1stmt = $db->prepare(
                        "UPDATE calendar SET picName=? WHERE id=?");
                $p1stmt->execute(array(
                        $imageName,
                        $eId
                ));
            }
        }
    }

    // Legal page
    if (filter_input(INPUT_POST, 'minUp', FILTER_SANITIZE_STRING)) {
        $minUp = filter_input(INPUT_POST, 'minUp', FILTER_SANITIZE_STRING);
        $M = filter_input(INPUT_POST, 'minM', FILTER_SANITIZE_NUMBER_INT);
        $D = filter_input(INPUT_POST, 'minD', FILTER_SANITIZE_NUMBER_INT);
        $Y = filter_input(INPUT_POST, 'minY', FILTER_SANITIZE_NUMBER_INT);
        $date = mktime(9, 0, 0, $M, $D, $Y);
        $text = filter_var(htmlEntities(trim($_POST['text']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $delId = filter_input(INPUT_POST, 'delId', FILTER_SANITIZE_NUMBER_INT);

        if ($minUp == 'new') {
            $b = $db->prepare("INSERT INTO minutes VALUES(NULL,?,?,'0','0')");
            $b->execute(array(
                    $date,
                    $text
            ));
        } elseif ($minUp == 'del') {
            $c = $db->prepare("DELETE FROM minutes WHERE id = ?");
            $c->execute(array(
                    $delId
            ));
        }
    }

    // Legal page
    if (filter_input(INPUT_POST, 'docUp', FILTER_SANITIZE_STRING)) {
        $docUp = filter_input(INPUT_POST, 'docUp', FILTER_SANITIZE_STRING);
        $docName = filter_input(INPUT_POST, 'docName', FILTER_SANITIZE_STRING);
        $text = filter_var(htmlEntities(trim($_POST['text']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $display = (filter_input(INPUT_POST, 'display',
                FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";

        if ($docUp == 'new') {
            $b = $db->prepare("INSERT INTO legalDocs VALUES(NULL,?,?,?,'0')");
            $b->execute(array(
                    $docName,
                    $text,
                    $display
            ));
        } else {
            $c = $db->prepare(
                    "UPDATE legalDocs SET docName = ?, text = ?, display = ? WHERE id = ?");
            $c->execute(array(
                    $docName,
                    $text,
                    $display,
                    $docUp
            ));
        }
    }

    // Legal page
    if (filter_input(INPUT_POST, 'pdfUp', FILTER_SANITIZE_STRING) == 'new') {
        $pdfName = filter_input(INPUT_POST, 'pdfName', FILTER_SANITIZE_STRING);
        $saveto = "pdf/$pdfName.pdf";
        move_uploaded_file($_FILES['pdfImage']['tmp_name'], $saveto);
        if (file_exists("pdf/$pdfName.pdf")) {
            $pdfstmt = $db->prepare("INSERT INTO pdf VALUES(NULL,?,?,'0')");
            $pdfstmt->execute(array(
                    $pdfName,
                    $time
            ));
        }
    }

    // Legal page
    if (filter_input(INPUT_POST, 'pdfDel', FILTER_SANITIZE_NUMBER_INT)) {
        $delId = filter_input(INPUT_POST, 'pdfDel', FILTER_SANITIZE_NUMBER_INT);
        $d = $db->prepare("SELECT pdfName FROM pdf WHERE id = ?");
        $d->execute(array(
                $delId
        ));
        $dRow = $d->fetch();
        $pn = $dRow['pdfName'];
        if (file_exists("pdf/$pn.pdf")) {
            unlink("pdf/$pn.pdf");
        }
        $f = $db->prepare("DELETE FROM pdf WHERE id = ?");
        $f->execute(array(
                $delId
        ));
    }

    // Legal page
    if (filter_input(INPUT_POST, 'jobUp', FILTER_SANITIZE_STRING)) {
        $jobUp = filter_input(INPUT_POST, 'jobUp', FILTER_SANITIZE_STRING);
        $jobName = filter_input(INPUT_POST, 'jobName', FILTER_SANITIZE_STRING);
        $display = filter_input(INPUT_POST, 'display',
                FILTER_SANITIZE_NUMBER_INT);
        $M = filter_input(INPUT_POST, 'jobM', FILTER_SANITIZE_NUMBER_INT);
        $D = filter_input(INPUT_POST, 'jobD', FILTER_SANITIZE_NUMBER_INT);
        $Y = filter_input(INPUT_POST, 'jobY', FILTER_SANITIZE_NUMBER_INT);
        $endDate = mktime(23, 59, 59, $M, $D, $Y);
        $jobDesc = filter_var(htmlEntities(trim($_POST['jobDesc']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);

        if ($jobUp == 'new') {
            $b = $db->prepare("INSERT INTO jobs VALUES(NULL,?,?,?,?,'0','0')");
            $b->execute(array(
                    $jobName,
                    $jobDesc,
                    $display,
                    $endDate
            ));
        } else {
            $c = $db->prepare(
                    "UPDATE jobs SET jobName = ?, jobDesc = ?, display = ?, endDate = ? WHERE id = ?");
            $c->execute(array(
                    $jobName,
                    $jobDesc,
                    $display,
                    $endDate,
                    $jobUp
            ));
        }
    }

    // News page
    if (filter_input(INPUT_POST, 'confdelpost', FILTER_SANITIZE_NUMBER_INT)) {
        $id = filter_input(INPUT_POST, 'confdelpost', FILTER_SANITIZE_NUMBER_INT);
        $stmt1 = $db->prepare("DELETE FROM news WHERE id=?");
        $stmt1->execute(array(
                $id
        ));
        echo "Post deleted...";
    }

    // News page
    if (filter_input(INPUT_POST, 'postnote', FILTER_SANITIZE_STRING)) {
        $id = filter_input(INPUT_POST, 'postnote', FILTER_SANITIZE_STRING);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = filter_var(htmlEntities(trim($_POST['content']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $delpic1 = (filter_input(INPUT_POST, 'delpic1',
                FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
        $delpic2 = (filter_input(INPUT_POST, 'delpic2',
                FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';
        $delpost = (filter_input(INPUT_POST, 'delpost',
                FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';

        if ($id == "new") {
            $stmt2 = $db->prepare(
                    "INSERT INTO news VALUES" . "(NULL,?,?,?,?,?,?,'0')");
            $stmt2->execute(
                    array(
                            $time,
                            $title,
                            $content,
                            "noPic.png",
                            "noPic.png",
                            $myId
                    ));
            $getId = $db->prepare(
                    "SELECT id FROM news WHERE time = ? && title = ? && text = ? ORDER BY id DESC LIMIT 1");
            $getId->execute(array(
                    $time,
                    $title,
                    $content
            ));
            $getRow = $getId->fetch();
            $id = $getRow['id'];
            echo "Post added...";
        } else {
            if ($delpost == "1") {
                echo "Are you sure you want to delete this post? <form action='index.php?page=News' method='post'><input type='hidden' name='confdelpost' value='$id' /><input type='submit' value=' YES ' /></form> <form action='index.php?page=News' method='post'><input type='submit' value=' NO ' /></form>";
            } else {
                if ($delpic1 == '1') {
                    $stmt3 = $db->prepare(
                            "SELECT pic1Name FROM news WHERE id=?");
                    $stmt3->execute(array(
                            $id
                    ));
                    $row3 = $stmt3->fetch();
                    $delpicname1 = $row3['pic1Name'];
                    if (file_exists("img/pagePics/$delpicname1")) {
                        unlink("img/pagePics/$delpicname1");
                    }
                    if (file_exists("img/pagePics/thumb/$delpicname1")) {
                        unlink("img/pagePics/thumb/$delpicname1");
                    }
                    $stmt4 = $db->prepare(
                            "UPDATE news SET pic1Name=? WHERE id=?");
                    $stmt4->execute(array(
                            'noPic.png',
                            $id
                    ));
                }
                if ($delpic2 == '1') {
                    $stmt5 = $db->prepare(
                            "SELECT pic2Name FROM news WHERE id=?");
                    $stmt5->execute(array(
                            $id
                    ));
                    $row5 = $stmt5->fetch();
                    $delpicname2 = $row5['pic2Name'];
                    if (file_exists("img/pagePics/$delpicname2")) {
                        unlink("img/pagePics/$delpicname2");
                    }
                    if (file_exists("img/pagePics/thumb/$delpicname2")) {
                        unlink("img/pagePics/thumb/$delpicname2");
                    }
                    $stmt6 = $db->prepare(
                            "UPDATE news SET pic2Name=? WHERE id=?");
                    $stmt6->execute(array(
                            'noPic.png',
                            $id
                    ));
                }
                $stmt7 = $db->prepare(
                        "UPDATE news SET time = ?, title = ?, text = ? WHERE id=?");
                $stmt7->execute(array(
                        $time,
                        $title,
                        $content,
                        $id
                ));
                echo "Post updated...<br>";
            }
        }
        if (isset($_FILES['image1']['tmp_name'])) {
            $tmpFile = $_FILES["image1"]["tmp_name"];
            $folder = "img/pagePics";
            list ($width1, $height1) = (getimagesize($tmpFile) != null) ? getimagesize(
                    $tmpFile) : null;
            if ($width1 != null && $height1 != null) {
                $image1Type = getPicType($_FILES["image1"]['type']);
                $image1Name = (time() + 1) . "." . $image1Type;
                processPic("$domain/img/pagePics", $image1Name, $tmpFile, 600,
                        150);
                $p1stmt = $db->prepare("UPDATE news SET pic1Name=? WHERE id=?");
                $p1stmt->execute(array(
                        $image1Name,
                        $id
                ));
            }
        }

        if (isset($_FILES['image2']['tmp_name'])) {
            $tmpFile = $_FILES["image2"]["tmp_name"];
            list ($width2, $height2) = (getimagesize($tmpFile) != null) ? getimagesize(
                    $tmpFile) : null;
            if ($width2 != null && $height2 != null) {
                $image2Type = getPicType($_FILES["image2"]['type']);
                $image2Name = (time() + 2) . "." . $image2Type;
                processPic("$domain/img/pagePics", $image2Name, $tmpFile, 600,
                        150);
                $p1stmt = $db->prepare("UPDATE news SET pic2Name=? WHERE id=?");
                $p1stmt->execute(array(
                        $image2Name,
                        $id
                ));
            }
        }
    }

    // Pics page
    if (filter_input(INPUT_POST, 'ytUp', FILTER_SANITIZE_STRING)) {
        $vid = filter_input(INPUT_POST, 'ytUp', FILTER_SANITIZE_STRING);
        $d = filter_input(INPUT_POST, 'd', FILTER_SANITIZE_NUMBER_INT);
        $m = filter_input(INPUT_POST, 'm', FILTER_SANITIZE_NUMBER_INT);
        $y = filter_input(INPUT_POST, 'y', FILTER_SANITIZE_NUMBER_INT);
        $displayTime = mktime(12, 0, 0, $m, $d, $y);
        $youtubeCode = filter_input(INPUT_POST, 'youtubeCode',
                FILTER_SANITIZE_STRING);
        $caption = filter_var(htmlEntities(trim($_POST['caption']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $del = (filter_input(INPUT_POST, 'del', FILTER_SANITIZE_NUMBER_INT) ==
                '1') ? '1' : '0';

        if ($del == '1') {
            $v1 = $db->prepare("DELETE FROM media WHERE id = ?");
            $v1->execute(array(
                    $vid
            ));
        } else {
            if ($vid == "new") {
                $v2 = $db->prepare(
                        "INSERT INTO media VALUES(NULL,?,'0',?,?,'0','0')");
                $v2->execute(array(
                        $displayTime,
                        $youtubeCode,
                        $caption
                ));
            } else {
                $v3 = $db->prepare(
                        "UPDATE media SET displayTime = ?, youtubeCode = ?, caption = ? WHERE id = ?");
                $v3->execute(
                        array(
                                $displayTime,
                                $youtubeCode,
                                $caption,
                                $vid
                        ));
            }
        }
    }

    // Pics page
    if (filter_input(INPUT_POST, 'picUp', FILTER_SANITIZE_STRING)) {
        $pid = filter_input(INPUT_POST, 'picUp', FILTER_SANITIZE_STRING);
        $d = filter_input(INPUT_POST, 'd', FILTER_SANITIZE_NUMBER_INT);
        $m = filter_input(INPUT_POST, 'm', FILTER_SANITIZE_NUMBER_INT);
        $y = filter_input(INPUT_POST, 'y', FILTER_SANITIZE_NUMBER_INT);
        $displayTime = mktime(12, 0, 0, $m, $d, $y);
        $caption = filter_var(htmlEntities(trim($_POST['caption']), ENT_QUOTES),
                FILTER_SANITIZE_STRING);
        $del = (filter_input(INPUT_POST, 'del', FILTER_SANITIZE_NUMBER_INT) ==
                '1') ? '1' : '0';

        if ($pid == "new") {
            $p3 = $db->prepare(
                    "INSERT INTO media VALUES(NULL,?,'0','0',?,'0','0')");
            $p3->execute(array(
                    $displayTime,
                    $caption
            ));
            $p5 = $db->prepare(
                    "SELECT id FROM media WHERE displayTime = ? && caption = ? ORDER BY id DESC LIMIT 1");
            $p5->execute(array(
                    $displayTime,
                    $caption
            ));
            $p5Row = $p5->fetch();
            $eId = $p5Row['id'];

            if (isset($_FILES['image1']['tmp_name'])) {
                $tmpFile = $_FILES["image1"]["tmp_name"];
                list ($width1, $height1) = (getimagesize($tmpFile) != null) ? getimagesize(
                        $tmpFile) : null;
                if ($width1 != null && $height1 != null) {
                    $image1Type = getPicType($_FILES["image1"]['type']);
                    $image1Name = $time . "." . $image1Type;
                    processPic("$domain/img/pagePics", $image1Name, $tmpFile,
                            600, 150);
                    $p1stmt = $db->prepare(
                            "UPDATE media SET picName=? WHERE id=?");
                    $p1stmt->execute(array(
                            $image1Name,
                            $eId
                    ));
                }
            }
        } else {
            if ($del == '1') {
                $p1 = $db->prepare("SELECT picName FROM media WHERE id = ?");
                $p1->execute(array(
                        $pid
                ));
                $p1row = $p1->fetch();
                $pn = $p1row['picName'];

                $p2 = $db->prepare("DELETE FROM media WHERE id = ?");
                $p2->execute(array(
                        $pid
                ));
                if (file_exists("img/pagePics/$pn")) {
                    unlink("img/pagePics/$pn");
                }
                if (file_exists("img/pagePics/thumb/$pn")) {
                    unlink("img/pagePics/thumb/$pn");
                }
            }

            $p4 = $db->prepare(
                    "UPDATE media SET displayTime = ?, caption = ? WHERE id = ?");
            $p4->execute(array(
                    $displayTime,
                    $caption,
                    $pid
            ));
        }
    }
}