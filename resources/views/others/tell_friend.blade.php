<?php
if (count($_POST)) {
    foreach (array('fmail1', 'email', 'name', 'cc') as $key) {
        $_POST[$key] = strip_tags($_POST[$key]);
    }
    if (!is_secure($_POST)) {
        die("Hackers begone");
    }

    $thankyoupage = "thankyou.htm";
// Subject line for the recommendation - change to suit
    $tsubject = "A web page recommendation from $_POST[name]";
//$headers['Cc'] = $_POST['email'];
// Change the text below for the email 
// Be careful not to change anyt "$_POST[value]" bits

    $ttext = "
Hi,

$_POST[name], whose email address is $_POST[email] thought you may be interested in this web page. 

$_POST[refurl]

$_POST[name] has used our Tell-a-Friend form to send you this note.

We look forward to your visit!

				";
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        $ttext = "
Hi,

$_POST[name], whose email address is $_POST[email] thought you may be interested in this web page. 

$_POST[refurl]

\"$_POST[message]\"

$_POST[name] has used our Tell-a-Friend form to send you this note.

We look forward to your visit!

				";
    }
    $cc = "";
    if (isset($_POST['cc']) and !empty($_POST['cc'])) {
        $cc = $_POST['cc'];
    }
    $headers = 'From: ' . $_POST['email'] . "\r\n" . 'Cc: ' . $cc . "\r\n";

# This sends the note to the addresses submitted
    if (mail("$_POST[fmail1]", $tsubject, $ttext, $headers)) {
# After submission, the thank you page
        echo "<h4>The email has been sent successfully</h4>";
    } else {
        echo "<h4>Some error happened, please try again later</h4>";
    }
//header("Location: $thankyoupage");
    exit;
}

# Nothing further can be changed. Leave the below as is

function is_secure($ar)
{
    $reg = "/(Content-Type|Bcc|MIME-Version|Content-Transfer-Encoding)/i";
    if (!is_array($ar)) {
        return preg_match($reg, $ar);
    }
    $incoming = array_values_recursive($ar);
    foreach ($incoming as $k => $v) if (preg_match($reg, $v)) return false;
    return true;
}

function array_values_recursive($array)
{
    $arrayValues = array();
    foreach ($array as $key => $value) {
        if (is_scalar($value) || is_resource($value)) {
            $arrayValues[] = $value;
            $arrayValues[] = $key;
        } elseif (is_array($value)) {
            $arrayValues[] = $key;
            $arrayValues = array_merge($arrayValues, array_values_recursive($value));
        }
    }
    return $arrayValues;
}
?>
<html>
<head>
    <title>Recommendation form</title>
    <script language="javascript">
        <!--

        function reset() {
            document.tellafriend.name.value = "";
            document.tellafriend.email.value = "";
            document.tellafriend.fmail1.value = "";
        }

        function validate() {


            if (document.tellafriend.fmail1.value.length == 0) {
                alert("You'll need to enter an email address");
                return false;
            }

            if (document.tellafriend.email.value.length == 0) {
                alert("You forget to enter your email address");
                return false;
            }
            if (document.tellafriend.name.value.length == 0) {
                alert("You forgot to enter your name");
                return false;
            }

            document.tellafriend.submit()
            return true;
        }

        //-->
    </script>
</head>
<body onload="reset()" topmargin="0" leftmargin="0">
<p>
<center>
</center>
<table width="450" cellpadding="0" cellspacing="0" align="center">
    <tr valign="top">
        <td valign="middle" align="center">&nbsp;
            Complete the details below to send a link to the page:
            <? $refurl = $_SERVER['HTTP_REFERER']; ?>
            <form name="tellafriend" action="" method="post" onsubmit="return checkfields()">&nbsp;
                <div align="center">
                    <input type="text" name="refurl" readonly value="<? print $refurl;?>" style="width: 100%;">
                    <center>
                        <table border="0" cellpadding="10" cellspacing="0">
                            <tr>
                                <td> *Your name:</td>
                                <td>
                                    <input size="30" name="name" maxlength="45">
                                </td>
                            </tr>
                            <tr>
                                <td>*Your email:</td>
                                <td>
                                    <input size="30" name="email" maxlength="45">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <p align="center">Enter your Colleague's email addresses:</td>
                            </tr>
                            <tr>
                                <td>*email:</td>
                                <td>
                                    <input size="30" name="fmail1" maxlength="50">
                                </td>
                            </tr>
                            <tr>
                                <td>CC:</td>
                                <td>
                                    <input size="30" name="cc" maxlength="50">
                                </td>
                            </tr>
                            <tr>
                                <td>Message:</td>
                                <td>
                                    <textarea rows="6" cols="32" name="message" maxlength="50"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input onclick="validate();" type="button" value="Send">
                                </td>
                            </tr>
                        </table>
                    </center>
                </div>
                <input type="hidden" name="_token" value="{{csrf_token()}}">
            </form>
        </td>
    </tr>
    <tr valign="top">
        <td valign="middle" align="center">
            &nbsp;
        </td>
    </tr>
</table>
</body>
</html>
