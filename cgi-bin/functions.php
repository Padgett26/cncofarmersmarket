<?php

class storeProduct
{

    public $id;

    public $vendorId;

    public $name;

    public $description;

    public $quantity;

    public $size;

    public $price;

    public $picName;

    public $ref;

    function __construct ($id, $vendorId, $name, $description, $quantity, $size,
            $price, $picName, $ref)
    {
        $this->id = $id;
        $this->vendorId = $vendorId;
        $this->name = $name;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->size = $size;
        $this->price = $price;
        $this->picName = $picName;
        $this->ref = $ref;
    }

    function get_vendor ()
    {
        return $this->vendorId;
    }

    function display ()
    {
        $description1 = nl2br(
                make_links_clickable(
                        html_entity_decode($description, ENT_QUOTES)));
        $getSize = db_ccfm()->prepare(
                "SELECT size FROM productSizes WHERE id = ?");
        $getSize->execute(array(
                $size
        ));
        $getSizeR = $getSize->fetch();
        $size = $getSizeR['size'];

        $available = qtyAvailable($id);

        if ($available >= 1) {
            echo "<form action='index.php?page=Store' method='post'><tr>\n";
            echo ($picName != "noPic.png" &&
                    file_exists("img/vendors/$vendorId/thumb/$picName")) ? "<td style='border:1px solid black;'><img src='img/vendors/$vendorId/thumb/$picName' style='border:1px solid #cc4541; padding:2px; max-width:100px; max-height:100px;'></td>\n" : "<td></td>\n";
            echo "<td style='border:1px solid black;'>$name</td>\n";
            echo "<td style='border:1px solid black;'>$description1</td>\n";
            echo "<td style='border:1px solid black;'><select name='quantity' size='1'>";
            for ($j = 0; $j <= $available; $j ++) {
                echo "<option value='$j'>$j</option>";
            }
            echo "</select></td>\n";
            echo "<td style='border:1px solid black;'>$size</td>\n";
            echo "<td style='border:1px solid black;'>" . money($price) .
                    "<input type='hidden' name='price' value='$price'></td>\n";
            echo "<td style='border:1px solid black;'>";

            if (inCart($id)) {
                echo "Product in cart";
            } else {
                echo "<input type='submit' value=' Add to cart '>";
            }
            echo "</td>\n";
            echo "</tr><input type='hidden' name='toCart' value='$id'><input type='hidden' name='vId' value='$vendorId'></form>";
        }
    }
}

function isBoard ($id)
{
    $x = db_ccfm()->prepare("SELECT role FROM board WHERE id = ?");
    $x->execute(array(
            $id
    ));
    $xr = $x->fetch();
    if ($xr) {
        if ($xr['role'] == 'board')
            return true;
    }
    return false;
}

function isVendor ($id)
{
    $x = db_ccfm()->prepare(
            "SELECT COUNT(*) FROM vendors WHERE boardId = ? AND approved = '1'");
    $x->execute(array(
            $id
    ));
    $xr = $x->fetch();
    if ($xr) {
        if ($xr[0] == 1)
            return true;
    }
    return false;
}

function isCust ($id)
{
    $x = db_ccfm()->prepare("SELECT COUNT(*) FROM customers WHERE boardId = ?");
    $x->execute(array(
            $id
    ));
    $xr = $x->fetch();
    if ($xr) {
        if ($xr[0] == 1)
            return true;
    }
    return false;
}

function vendorHighlight ()
{
    $ven = db_ccfm()->query(
            "SELECT id,displayName,picName FROM vendors ORDER BY rand() LIMIT 1");
    $venRow = $ven->fetch();
    $venId = $venRow['id'];
    $venName = $venRow['displayName'];
    $venPic = $venRow['picName'];

    $vp = (file_exists("img/vendors/$venPic")) ? $venPic : "noPic.png";
    if ($vp != "noPic.png") {
        echo "<div style='text-align: center;'><a href='index.php?page=Vendors&highlight=" .
                $venId . "#highlight$venId'><img src='img/vendors/" . $vp .
                "' title='' style='max-width: 150px; max-height: 150px;' /></a></div>\n";
    }
    echo "<div style='text-align: center; color:#000000; font-weight:bold;'><a href='index.php?page=Vendors&highlight=" .
            $venId . "#highlight$venId'>" . $venName . "</a></div>\n";
}

function upcomingEvents ()
{
    $cal = db_ccfm()->prepare(
            "SELECT * FROM calendar WHERE eventTime > ? ORDER BY eventTime LIMIT 5");
    $cal->execute(array(
            time()
    ));
    while ($calRow = $cal->fetch()) {
        $eventId = $calRow['id'];
        $eventTime = $calRow['eventTime'];
        $eventTitle = $calRow['eventTitle'];
        echo "<tr><td style='padding:5px; color:#000000;'><a href='index.php?page=Calendar&highlight=" .
                $eventId . "'>" . date("D, M jS @ g:i a", $eventTime) .
                "</a></td><td style='padding:5px;'><a href='index.php?page=Calendar&highlight=" .
                $eventId . "'>" . $eventTitle . "</a></td></tr>\n";
    }
}

function inCart ($id)
{
    $checkCart = db_ccfm()->prepare(
            "SELECT COUNT(*) FROM onlineSales WHERE custId = ? && productId = ? && inCart = '1'");
    $checkCart->execute(array(
            $custId,
            $id1
    ));
    $ccR = $checkCart->fetch();
    $ccCount = $ccR[0];
}

function sendPWResetEmail ($toId, $firstName, $email, $verifyTime)
{
    $link = hash('sha512', ($verifyTime . $firstName . $email), FALSE);
    $message = "$firstName,\r\n\r\n
        There has been a request on the Cheyenne Co Farmer\'s Market website for a password reset for this account.  If you initiated this request, click the link below, and you will be sent to a page where you will be able enter a new password. If you did not initiate this password reset request, simple ignore this email, and your password will not be changed.\r\n\r\n
        https://cncofarmersmarket.com/index.php?page=LogIn&id=$toId&ver=$link'\r\n\r\n
        Thank you,\r\nAdmin\r\nCheyenne Co Farmer's Market";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
    $headers .= "From: Cheyenne Co Farmer's Market <board@cncofarmersmarket.com>";
    mail($email, "Cheyenne Co Farmer's Market website password reset request",
            $message, $headers);
}

function emailV ($toId, $emailFrom, $emailSubject, $emailText)
{
    foreach ($toId as $v) {
        $getE = db_ccfm()->prepare(
                "SELECT agreementEmail FROM vendors WHERE id = ?");
        $getE->execute(array(
                $v
        ));
        $getER = $getE->fetch();
        if ($getER) {
            $email = $getER['agreementEmail'];
            $text = $emailText .
                    "\r\n\r\nIf you you no longer wish to receive emails from Cheyenne County Farmers Market, you can be removed from the mailing list by clicking this link:<br>
                <a href='https://cncofarmersmarket.com/index.php?page=LogIn&email=$email&mailingList=remove'>https://cncofarmersmarket.com/index.php?page=LogIn&email=$email&mailingList=remove</a>";
            $message = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

            $subject = html_entity_decode($emailSubject, ENT_QUOTES | ENT_XML1,
                    'UTF-8');

            if ($emailFrom == "Coordinator") {
                $headers = "From: Cheyenne Co Farmer's Market Coordinator
                    <coordinator@cncofarmersmarket.com>" . "\r\n";
            } else {
                $headers = "From: Cheyenne Co Farmer's Market Board
                    <board@cncofarmersmarket.com>" . "\r\n";
            }
            $headers .= 'Reply-To: board@cncofarmersmarket.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
            mail($email, $subject, $message, $headers);
        }
    }
}

function getPicType ($imageType)
{
    switch ($imageType) {
        case "image/gif":
            $picExt = "gif";
            break;
        case "image/jpeg":
            $picExt = "jpg";
            break;
        case "image/pjpeg":
            $picExt = "jpg";
            break;
        case "image/png":
            $picExt = "png";
            break;
        default:
            $picExt = "xxx";
            break;
    }
    return $picExt;
}

function processPic ($imageName, $tmpFile, $folder)
{
    if (! is_dir("$folder")) {
        mkdir("$folder", 0777, true);
    }

    $saveto = "$folder/$imageName";

    list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
            $tmpFile) : null;
    if ($width != null && $height != null) {
        $image = new Imagick($tmpFile);
        $image->thumbnailImage(600, 600, true);
        $image->writeImage($saveto);
    }
}

function processThumbPic ($imageName, $tmpFile, $f)
{
    $folder = "$f/thumb";

    if (! is_dir("$f")) {
        mkdir("$f", 0777, true);
    }
    if (! is_dir("$folder")) {
        mkdir("$folder", 0777, true);
    }

    $saveto = "$folder/$imageName";
    list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
            $tmpFile) : null;
    if ($width != null && $height != null) {
        $image = new Imagick($tmpFile);
        $image->thumbnailImage(150, 150, true);
        $image->writeImage($saveto);
    }
}

function processPdf ($pdfName, $file)
{
    $saveto = "pdf/$pdfName.pdf";
    move_uploaded_file($file, $saveto);
}

function deletePdf ($pdfName)
{
    if (file_exists("pdf/" . $pdfName . ".pdf")) {
        unlink("pdf/" . $pdfName . ".pdf");
    }
}

function make_links_clickable ($text)
{
    return preg_replace(
            '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i',
            "<a href='$1' target='_blank' style='color:#cc4541; text-decoration:underline;'>$1</a>",
            $text);
}

function qtyAvailable ($productId, $db)
{
    $getQ = $db->prepare("SELECT quantity FROM product WHERE id = ?");
    $getQ->execute(array(
            $productId
    ));
    $getQR = $getQ->fetch();
    $qty = $getQR['quantity'];

    $q = 0;
    $f = 0;
    $getQty = $db->prepare(
            "SELECT quantity, filled FROM onlineSales WHERE productId = ? && quantity > filled");
    $getQty->execute(array(
            $productId
    ));
    while ($getQtyR = $getQty->fetch()) {
        $q += $getQtyR['quantity'];
        $f += $getQtyR['filled'];
    }
    $a = $qty - ($q - $f);
    return $a;
}

function vSalesEmail ($vId, $db)
{
    $getV = $db->prepare(
            "SELECT displayName, agreementEmail FROM vendors WHERE id = ?");
    $getV->execute(array(
            $vId
    ));
    $getVR = $getV->fetch();
    if ($getVR) {
        $name = $getVR['displayName'];
        $email = $getVR['agreementEmail'];
        $products = array();

        $mess = "$name,\n
        There has been an order placed which includes your product. Please take a look at the order and have the requested product available for distribution by Saturday at 9am, at the Farmers Market.\n
		If we are not in the Farmers Market season, please call Jason at 785-772-5151, who will organize the distribution of the sale to the customer. Please dont distribute the product directly to the customer, because there may be items in the sale from multiple vendors.\n
		Payment to you for your product will be handled after the sale to the customer.\n
        Follow the link below, make sure you are logged in to the site, and view the open orders for your product on the Vendors page.\n
        <a href='https://cncofarmersmarket.com/index.php?page=Vendors'>Click here to view the online order placed</a>\n\n
		Totals, from all open orders, that need to be delivered to the Farmers Market before Saturday at 9am:\n";
        $getP = $db->prepare(
                "SELECT productId, quantity, filled FROM onlineSales WHERE vendorId = ? AND inCart = ?");
        $getP->execute(array(
                $vId,
                '0'
        ));
        while ($getPR = $getP->fetch()) {
            $p = $getPR['productId'];
            $q = $getPR['quantity'];
            $f = $getPR['filled'];
            $t = $q - $f;
            if ($t >= .01) {
                $products[$p] = $t;
            }
        }
        foreach ($products as $k => $v) {
            $getN = $db->prepare("SELECT name FROM product WHERE id = ?");
            $getN->execute(array(
                    $k
            ));
            $getNR = $getN->fetch();
            if ($getNR) {
                $n = $getNR['name'];
                echo "Qty: $v Product: $n\n";
            }
        }
        $mess .= "\nThank you,\n
		Admin\n
		Cheyenne Co Farmer's Market";
        $message = wordwrap($mess, 70);
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $headers .= "From: Cheyenne Co Farmer's Market <noreply@cncofarmersmarket.com>" .
                "\r\n";
        mail($email, "Cheyenne Co Farmer's Market online sales", $message,
                $headers);
    }
}

function delTree ($dir)
{
    $files = array_diff(scandir($dir), array(
            '.',
            '..'
    ));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? delTree("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

function money ($amt)
{
    settype($amt, "float");
    $fmt = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
    return $fmt->formatCurrency($amt, "USD");
}