<?php

$ROOT = $_SERVER["DOCUMENT_ROOT"];
require_once $ROOT . '/fatoora/vendor/autoload.php';
require_once $ROOT . "/fatoora/login/utils.php";
require_once $ROOT . "/fatoora/app/invoice/Invoice.php";
require_once $ROOT . "/fatoora/app/invoiceDetail/InvoiceDetail.php";

session_start();

$InvoiceRecID = $_GET['invoiceRecID'];
$invoice = (new Invoice())->findInvoiceHeaderFooter($InvoiceRecID);

$invoiceNumber    = $invoice['InvoiceNumber'];
$date             =  $invoice['Date'];
$time             =  $invoice['Time'];
$staff             =  $invoice['CashierID'];
$cash            =  $invoice['CashAmount'];
$card            =  $invoice['CardAmount'];
$balance        =  $invoice['BalanceAmount'];
$SubTotal        =  $invoice['TotalSubTotal'];
$TotalVAT        =  $invoice['TotalVATAmount'];
$GrandTotal        =  $invoice['GrandTotal'];
$CustomerName    =  $invoice['CustomerName'];
$CustomerNameAR    =  $invoice['CustomerNameAR'];
$CustomerVAT    =  $invoice['VATNumber'];
$Remarks        =  $invoice['Remarks'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>

    <style>
        body {
            width: 80mm;
            object-fit: contain;
            text-align: center;
            font-size: 9pt;
            font-family: monospace;
        }

        img {
            width: 300px;
        }

        p {
            margin: 5px;
        }

        .justify {
            justify-content: space-between;
            display: flex;
        }

        .left {
            text-align: left;

        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;

        }

        table {
            width: 100%;
            font-size: 9pt;
        }

        .index {
            vertical-align: top;
            width: 5%;
        }

        .name {
            width: 55%;
        }

        .qty {
            width: 10%;

        }

        .price {
            width: 15%;
        }

        .bold {
            font-weight: bold;
        }

        /* Page break for partial paper cut. */
        @media all {
            .page-break {
                display: none;
            }
        }

        @media print {
            .page-break {
                display: block;
                page-break-before: always;
            }
        }
    </style>
</head>

<body>
    <script>
        window.onload = function() {
            window.print()
        }
    </script>
    <div><img src="/fatoora/images/EmtyazLogo.png"></div>

    <p>العزيزية - شارع البسالة - خلف النقل الجماعي </p>
    <p>Al Azizia Behind Mass Transit.</p>
    <p>Phone.0598389595 - E-mail. g@emtyaz.sa</p>
    <hr>
    <p class="bold">VAT INVOICE <br> فاتورة ضريبية</p>
    <hr>
    <div class="justify">
        <p>Invoice Number: <?php echo $invoiceNumber; ?></p>
        <p>: رقم الفاتورة</p>
    </div>

    <div class="justify">
        <p>Invoice Date: <?php echo $date; ?></p>
        <p>:  تاريخ الفاتورة </p>
    </div>

    <div class="justify">
        <p>Invoice Time:<?php echo $time; ?></p>
        <p>: وقت الفاتورة </p>
    </div>

    <div class="justify">
        <p>Staff ID:<?php echo $staff; ?></p>
        <p>:  أمين الصندوق </p>
    </div>
    <hr>
    <table>
        <thead>
            <tr>
                <td class="left bold" colspan="2">Items</td>
                <td class="center qty bold">Qty</td>
                <td class="center price bold">Price</td>
                <td class="center price bold">Amount</td>
            </tr>
            <tr>
                <td class="left bold" colspan="2">العناصر</td>
                <td class="center qty bold">كمية</td>
                <td class="center price bold">سعر</td>
                <td class="center price bold">المبلغ الإجمالي</td>
            </tr>
        </thead>
    </table>
    <hr>
    <table>

        <?php

        $InvoiceRecID = $_GET['invoiceRecID'];
        $invoiceDetailRecords = (new InvoiceDetail())->findAllByInvoiceRecID($InvoiceRecID);

        $total = 0;
        $index = 0;
        foreach ($invoiceDetailRecords as $record) {
            $index++;
            $quantity = $record['OrderQuantity'];
            $totalAmount = $record['TotalAmount'];
            if ($record['UPCTypeRecID'] == 3) {
                $quantity = number_format($quantity, 3);
            } ?>
            <tr>
                <td class="left index"><?php echo $index; ?></td>
                <td class="left name"><?php echo $record['Barcode']; ?></td>
                <td class="left qty"><?php echo $quantity; ?></td>
                <td class="right price"><?php echo number_format($record['UnitAmountVAT'], 2); ?></td>
                <td class="right bold"><?php echo number_format($totalAmount, 2); ?></td>
            </tr>
            <tr>
                <td colspan="5" class="right bold"><?php echo $record['ProductFullNameAR']; ?></td>
            </tr>
            <tr>
                <td colspan="5" class="left"><?php echo $record['ProductFullName']; ?></td>
            </tr>
        <?php
            $total += $record['TotalAmount'];
        }


        ?>
    </table>

    <p class="left">INVOICE SUMMARY</p>
    <hr>
    <div class="justify left">
        <!-- <p>Total Without VAT<br>المجموع بدون ضريبة </p>
    <p><?php // echo number_format($SubTotal,2);
        ?> SAR</p> -->
    </div>
    <div class="justify left">
        <p>VAT 15% <br>15% ضريبة القيمة المضافة </p>
        <p><?php echo number_format($TotalVAT, 2); ?> SAR</p>
    </div>
    <div class="justify left bold">
        <p>Total with VAT <br> الإجمالي شاملاً الضريبة</p>
        <p><?php echo number_format($GrandTotal, 2); ?> SAR</p>
    </div>
    <hr>
    <div class="justify">
        <p>Cash Amount: </p>
        <p><?php echo number_format($cash, 2); ?> SAR</p>
    </div>
    <div class="justify">
        <p>Card Amount: </p>
        <p><?php echo number_format($card, 2); ?> SAR</p>
    </div>
    <div class="justify">
        <p>Balance Amount: </p>
        <p><?php echo number_format($balance, 2); ?> SAR</p> <!-- this was original -> $balance -->
    </div>
    <hr>
    <p>All the above mentioned items were completely received in</p>
    <p>good condition and non-refundable.</p>

    <p>استلمت البضاعة الموضحةأعلاه كاملة وسليمة وغير قابلة للاسترجاع</p>
    <hr>
    <p class="right bold">تفاصيل العميل</p>
    <p class="right"><?php echo $CustomerNameAR; ?></p>
    <p class="right"><?php echo $Remarks; ?></p>
    <p class="right"><?php echo $CustomerVAT; ?> :الرقم الضريبي </p>
    <hr>
    <svg id="barcode"></svg>
    <script src="/fatoora/js/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode("#barcode", "<?php echo $invoiceNumber ; ?>", {width: 1.8});
    </script>
    <!-- <p>THANK YOU FOR SHOPPING WITH EMTYAZ</p> 
    <p>نشكرك على التسوق مع امتياز</p> -->
    <hr>
    <?php

    //QRContent acc. to ZATCA

    $t1 = "EMTYAZ FOR CATERING COMPANY";
    $t2 = "300066889400003";
    $t3 = date("m/d/Y h:i:s a", time());
    $t4 = number_format($GrandTotal, 2);
    $t5 = number_format($TotalVAT, 2);


    $t1LEN = strlen($t1);
    $t2LEN = strlen($t2);
    $t3LEN = strlen($t3);
    $t4LEN = strlen($t4);
    $t5LEN = strlen($t5);


    $tlv1 = pack("H*", sprintf("%02X", "01")) . pack("H*", sprintf("%02X", $t1LEN)) . $t1;
    $tlv2 = pack("H*", sprintf("%02X", "02")) . pack("H*", sprintf("%02X", $t2LEN)) . $t2;
    $tlv3 = pack("H*", sprintf("%02X", "03")) . pack("H*", sprintf("%02X", $t3LEN)) . $t3;
    $tlv4 = pack("H*", sprintf("%02X", "04")) . pack("H*", sprintf("%02X", $t4LEN)) . $t4;
    $tlv5 = pack("H*", sprintf("%02X", "05")) . pack("H*", sprintf("%02X", $t5LEN)) . $t5;

    $QRContent       = $tlv1 . $tlv2 . $tlv3 . $tlv4 . $tlv5;


    $Invoice_QR = base64_encode($QRContent);

    //END 


    ?>
    <script src="http:"></script>

    <canvas width="100%" id="qrcode-2"></canvas>
    <script src="/fatoora/js/qrious.js"></script>
    <script type="text/javascript">
        var qrcode = new QRious({
            element: document.getElementById("qrcode-2"),
            background: '#ffffff',
            backgroundAlpha: 1,
            foreground: '#000000',
            foregroundAlpha: 1,
            level: 'H',
            padding: 14,
            size: 200,
            value: "<?php echo $Invoice_QR; ?>"
        });
    </script>
    <hr>
    <p>THANK YOU FOR SHOPPING WITH EMTYAZ</p>
    <p>نشكرك على التسوق مع امتياز</p>
    <hr>

    <br>

    <!-- Paper cut command -->
    <div class="page-break"></div>
    <!-- Paper cut command : END -->

    <hr style="border-top: 1px dashed red;">
    <div class="justify">
        <p>Invoice Number: <?php echo $invoiceNumber; ?></p>
        <p>: رقم الفاتورة</p>
    </div>

    <div class="justify">
        <p>Invoice Date: <?php echo $date; ?></p>
        <p>:  تاريخ الفاتورة </p>
    </div>

    <div class="justify">
        <p>Invoice Time:<?php echo $time; ?></p>
        <p>: وقت الفاتورة </p>
    </div>

    <div class="justify">
        <p>Staff ID:<?php echo $staff; ?></p>
        <p>:  أمين الصندوق </p>
    </div>
    <hr>
    <div class="justify left bold">
        <p>Total with VAT:SAR.<?php echo number_format($GrandTotal, 2); ?> </p>
        <p> :الإجمالي شاملاً الضريبة</p>
    </div>
    <hr>
</body>

</html>
