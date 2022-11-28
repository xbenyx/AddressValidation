<?php

require 'vendor/autoload.php';
include_once("shippinglivedata.php");
include_once("php/conf.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

getLiveAddressSage($all_data);

/**
 * This function checks all the live orders in Production where Country is XXX and request address validation
 *
 * @param array $mainarray Object array of doc
 * @param array $original_address object array of the original address
 * @param array $other_address object array of the address gotten form api
 * @param string $status  Stateus of the address (alternate, mismatched, notfound)
 */

function my_mail_function($mainarray, $original_address, $other_address, $status)
{

    $host_smtp = PARAMS['host_smtp'];
    $email = PARAMS['email'];
    $pwd = PARAMS['pwd'];
    $emailto = PARAMS['emailto'];

    $SO =  $mainarray['DocumentNo'];
    $DocumentCreatedBy = $mainarray['DocumentCreatedBy'];

    // Usar la funcion PHPmailer para enviar correos
    $mail = new PHPMailer(true);

    // SMTP SETTING
    $mail->IsSMTP();
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'tls';
    $mail->Host = $host_smtp;
    $mail->Port = 587;
    $mail->Username = $email;
    $mail->Password = $pwd;

    //Recipients
    $mail->setFrom($email, 'Shipping Error.');

    $mail->addAddress($emailto, 'Important');

    // Data about postalcode, city, state or email

    $PostCode = $original_address['postal_code'];
    $City = $original_address['city'];
    $State = $original_address['state'];
    $Email = $original_address['email'];


    // The subject of the message.
    $mail->Subject = 'Address Validation Failed - Order No ' . $SO . '';

    $instructions = 'This tool validates the address using City, State/Province and PostalCode. State/Province is 2 letters and field in Sage is County,ie. CA/NY/FL..... PostalCode is numeric, please avoid blank spaces or special characters.';

    // The message in HTML


    $message = '<div style="margin:0;padding:0;color:#333;font-style:normal;line-height:1.42857143;font-size:14px;font-family:Helvetica,Arial,sans-serif;font-weight:400;text-align:left;background-color:#f5f5f5"> ';

    $message .= '<table width="100%" style="border-collapse:collapse;margin:0 auto">
        <tbody><tr>
            <td align="center" style="font-family:Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:30px;width:100%">
                <table align="center" style="border-collapse:collapse;margin:0 auto;text-align:left;width:660px">
                    <tbody><tr>
                        <td style="font-family:Helvetica,Arial,sans-serif;vertical-align:top;background-color:#f5f5f5;padding:25px">
                            <a href="add here" style="color:#006bb4;text-decoration:none" target="_blank" data-saferedirecturl="https://www.google.com/url?q=http://192.168.1.20:9080/returns/&amp;source=gmail&amp;ust=1612601796007000&amp;usg=AFQjCNE-9sJxAcKiv0WIspaH5uSkuwfkOQ">
							<img width="180"

                            border="0" style="border:0;height:auto;line-height:100%;outline:none;text-decoration:none" class="CToWUd">
                            </a>
                        </td>
                    </tr>
                    <tr>
                    <td style="font-family:Helvetica,Arial,sans-serif;vertical-align:top;background-color:#fff;padding:25px">';

    $message .= '<table style="border-collapse:collapse;margin-bottom:10px width:50%">';

    $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='margin-top:0;margin-bottom:10px;text-align:center'><u>ADDRESS WRONG</u></b></td><td style='margin-top:0;margin-bottom:10px;text-align:center'></td></tr><br>";

    $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Order Taken By: </td><td>" . $DocumentCreatedBy . "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

    $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>City: </td><td text-align:left>" . $City . "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

    $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>State/Province(Only 2 Letters): </td><td>" . $State . "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

    $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Post Code: </td><td>" . $PostCode . "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

    $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Email: </td><td>" . $Email . "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";


    if ($status == 'alternate' || $status == 'mismatched') {

        $Alternative_city = $other_address['city'];;
        $Alternative_state = $other_address['state'];
        $Alternative_postalcode = $other_address['postal_code'];

        if ($status == 'alternate') {
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>_____</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>It's not a 100% match, the closest alternative is:</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Alternative City: " . $Alternative_city . "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Alternative State/Province: " . $Alternative_state . "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Alternative PostalCode: " . $Alternative_postalcode . "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
        }
         elseif ($status == 'mismatched') {

            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>_____</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>The address seems to be correct but mismatched in the entry, Here is the proper address</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>City: " . $Alternative_city . "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>State/Province: " . $Alternative_state . "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>PostalCode: " . $Alternative_postalcode . "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
        }
    }

    $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>_____</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

    $message .= "<tr><td colspan=2 style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Instructions: " . $instructions . "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr></tbody></table>";

    $message .= "<tr><td style='font-family:Helvetica,Arial,sans-serif;vertical-align:top;background-color:#f5f5f5;padding:25px'><p style='margin-top:0;margin-bottom:10px;text-align:center'>Kind regards,</p><p style='margin-top:0;margin-bottom:10px;text-align:center'></p>
			  <tr>
				<td bgcolor='#1f3855' style='padding: 12px 18px 12px 18px; border-radius:3px' align='center'><a href='add target here' target='_blank' style='font-size: 16px; font-family: Helvetica, Arial, sans-serif; font-weight: normal; color: #ffffff; text-decoration: none; display: inline-block;'></a></td>
			  </tr>
		</td></tr><br><br>
        <tr><td align='center' style='font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;padding:35px'><p style='font-family:Avenir,Helvetica,sans-serif;box-sizing:border-box;line-height:1.5em;margin-top:0;color:#aeaeae;font-size:12px;text-align:center'></p></td></tr>
        ";

    $message .= "</tbody></table></td></tr></tbody></table></div>";

    $mail->Body = $message;

    $mail->isHTML(true);

    $mail->send();
}



/**
 * This function checks all the live orders in Production where Country is XXX and request address validation
 *
 * @param string  $all_data All Sales Order in Production with delivery address or invoice address
 */

function getLiveAddressSage($all_data)
{

    if (is_array($all_data) || is_object($all_data)) {
        foreach ($all_data as $value) {
            foreach ($value as $val) {

                if (($val['UseInvoiceAddress'] == 0 &&  strlen($val['EmailAddress']) < 10) || ($val['UseInvoiceAddress'] == 1 &&  strlen($val['DefaultEmail']) < 10)) {
                    $mainarray = $val;
                    //my_mail_function($mainarray, null, null, "");
                } else {
                    if ($val['UseInvoiceAddress'] == 0) { //UseInvoiceAddress = 0 means send to Delivery Address

                        $PostCode =  trim($val['PostCode']);
                        $City =  trim($val['City']);
                        $State =  trim($val['State']);
                        $Country =  trim($val['Country']);
                        $AddressLine =  trim($val['AddressLine1']);
                        $AddressLine2 =  trim($val['AddressLine2']);
                        $AddressLine .= ' ' . $AddressLine2;

                        $Email = $val['EmailAddress'];

                        $mainarray = $val;

                        post_shipping_api($mainarray, $AddressLine, $PostCode, $City, $State, $Country, $Email);
                    } elseif ($val['UseInvoiceAddress'] == 1) { //UseInvoiceAddress = 1 means send to Invoice Address
                        $PostCodeinv = trim($val['PostCodeinv']);
                        $Cityinv = trim($val['Cityinv']);
                        $Stateinv = trim($val['Stateinv']);
                        $Countryinv =  trim($val['Countryinv']);

                        $Email = $val['DefaultEmail'];

                        $mainarray = $val;

                        $AddressLine1 = trim($val['AddressLine1inv']);
                        $AddressLine2 = trim($val['AddressLine2inv']);

                        $AddressLine = $AddressLine1 . ' ' . $AddressLine2;

                        post_shipping_api($mainarray, $AddressLine, $PostCodeinv, $Cityinv, $Stateinv, $Countryinv, $Email);
                    }
                } //End condition to check if email is added
            } // End Second Foreach
        } // End First Foreach
    }
}

/**
 * This function adds the post to the API to Validate Address, if its an error sends an email to the Sales Department
 *
 * @param string  $url Link for the API connection
 * @param array $post_data Array with the request information, includes City, StateProvince and PostalCode
 * @param integer $SO      A Sales Order Number for Reference
 * @param string $DocumentCreatedBy   Who is the Sales Representative
 */


function post_shipping_api($mainarray, $AddressLine, $PostCode, $City, $State, $Country, $Email)
{


    $original_address = [
        "country" => $Country,
        "state" => $State,
        "city" => $City,
        "postal_code" => $PostCode,
        "street" => $AddressLine,
        "email" => $Email,
    ];


    // Should we add postCode? Where in the array?
    $AddressQry = implode(", ", array($AddressLine, $City, $State, $Country));

    // We can add 'in' as a parameter if we have the iso-3 country code
    $params = array('q' => $AddressQry, 'key' => PARAMS['nextbillion_token'], 'limit' => 2);

    $endpoint = PARAMS['nextbillion_endpoint'];

    $url = $endpoint . '?' . http_build_query($params);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    curl_close($ch);

    //print $response;

    $rdata = json_decode($response, true);

    if (!isset($rdata['items'])) {

        // There is error. API key limit or some others
        // Handle error

        return;
    }

    $this_address = null;
    $row_count = 0;
    foreach ($rdata['items'] as $item) {
        $queryScore = $item['scoring']['queryScore'];
        if ($queryScore == 1) {
            // Address is valid
            // What happen next?

            return;
        }

        $countryLabel = $item['address']['countryName'];
        $stateLabel = $item['address']['state'];
        $cityLabel = $item['address']['city'];
        $postalCodeLabel = $item['address']['postalCode'];

        $streetLabel = $item['address']['street'];

        // Let match each of the labels

        $country_match = word_match($countryLabel, $Country);
        $state_match = word_match($stateLabel, $State);
        $city_match = word_match($cityLabel, $City);
        $postal_code_match = word_match($postalCodeLabel, $PostCode);

        // Can we say addressline is thesame as street address?
        $address_match = word_match($streetLabel, $AddressLine);

        // Now we test if the labels are all correct
        // Any need to add postalcode in the match
        // Address seems to be far differ,
        // You can add address and postalcode if you feel so
        if ($country_match && $state_match && $city_match) {
            // I guess the address is valid
            // Though the score was not 1 (100%)

            return;
        }

        // Address seems not to be valid, Lets check if address mismatch
        // Any need to add the street/address here, remove form array if appropraiate
        $apiLabels = [$countryLabel, $stateLabel, $cityLabel, $postalCodeLabel, $streetLabel];

        $country_found = find_word_match($apiLabels, $Country);
        $state_found = find_word_match($apiLabels, $State);
        $city_found = find_word_match($apiLabels, $City);
        $postal_code_found = find_word_match($apiLabels, $PostCode);


        // Now we test if the labels are all found
        // Any need to add postalcode in the match
        // You can add postalcode if you feel so
        if ($country_found && $state_found && $city_found) {

            //This means address is correct but fields are mismatched
            $this_address = [
                "country" => $countryLabel,
                "state" => $stateLabel,
                "city" => $cityLabel,
                "postal_code" => $postalCodeLabel,
                //"street" => $streetLabel,   We didn't actually match street here
            ];

            // We can send our Email with the Alternate address details
            my_mail_function($mainarray, $original_address, $this_address, 'mismatched');

            return;
        }


        // If we are here probably the first item should be the most
        // nearly matched alternate address
        //This means address is correct but fields are mismatched
        if ($row_count == 0) {
            $this_address = [
                "country" => $countryLabel,
                "state" => $stateLabel,
                "city" => $cityLabel,
                "postal_code" => $postalCodeLabel,
                //"street" => $streetLabel,   We didn't actually match street here
            ];
        }

        $row_count++;
    } // End of forach loop

    // No address found
    if ($this_address == null) {

        my_mail_function($mainarray, $original_address, null, 'notfound');
    } else {
        // We have an alternate address
        my_mail_function($mainarray, $original_address, $this_address, 'alternate');
    }
}


/**
 * This function chececk if two words are equal or similar
 * Can be improve to ignore spelling mistakes or space
 *
 * @param string  $word1 first word
 * @param string $words2 second word
 *
 * @return bolean return true or false
 */

function word_match($word1, $word2)
{

    // This is just a simple implementation of word match
    // What is is just a minor space difference or spelling mistake
    // Maybe are are word processor for this

    return (strtolower($word1) == strtolower($word2));
}


/**
 * This function chececk if a words is  equal or similar
 * to any of the word in an array
 * Can be improve to ignore spelling mistakes or space
 *
 * @param string  $word first word
 * @param array $words Array of words
 *
 * @return boolean Return true if found else false
 */
function find_word_match($words, $word)
{

    foreach ($words as $wd) {
        $found = word_match($wd, $word);
        if ($found) {
            return $found;
        }
    }

    return false;
}