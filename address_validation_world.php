<?php

require 'vendor/autoload.php';
include_once("shippinglivedata.php");
include_once("php/conf.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// // // //
$url = 'https://api.nextbillion.io/h/geocode?q=';  // ups URL for api request
$nextbillion_token = PARAMS['nextbillion_token'];$host_smtp = PARAMS['host_smtp'];$email = PARAMS['email'];$pwd = PARAMS['pwd']; // Credentials
// // // //

getLiveAddressSage($all_data, $url, $nextbillion_token);

/**
 * This function checks all the live orders in Production where Country is XXX and request address validation
 *
 * @param string $status API response, options are; success or failure
 * @param string $SO Sales Order Numbre
 * @param string $DocumentCreatedBy  Sales Representative name
 * @param string $host_smtp   host smtp
 * @param string $email   Who is sending the email
 * @param string $pwd   Password of who is sending the email
 * @param string $emailto   Who is receiving the email
 */

function my_mail_function($mainarray,$status_r, $SO,$DocumentCreatedBy, $host_smtp, $email, $pwd,$emailto){

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

        if($mainarray['UseInvoiceAddress'] == 0){ //UseInvoiceAddress = 0 means send to Delivery Address
            $PostCode = $mainarray['PostCode'];
            $City = $mainarray['City'];
            $State = $mainarray['State'];
            $Email = $mainarray['EmailAddress'];
        }elseif($mainarray['UseInvoiceAddress'] == 1) { //UseInvoiceAddress = 1 means send to Invoice Address
            $PostCode = $mainarray['PostCodeinv'];
            $City = $mainarray['Cityinv'];
            $State = $mainarray['Stateinv'];
            $Email = $mainarray['DefaultEmail'];
        }

        //  Display array when quality of response is less than one. If this value is equal than 1 it means that the address match 100%, otherwise the api will return some alternatives



        // The subject of the message.
		$mail->Subject = 'Address Validation Failed - Order No '.$SO.'';

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

        $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Order Taken By: </td><td>" .$DocumentCreatedBy. "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

        $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>City: </td><td text-align:left>" .$City. "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

        $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>State/Province(Only 2 Letters): </td><td>" .$State. "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

        $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Post Code: </td><td>" .$PostCode. "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

        $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Email: </td><td>" .$Email. "</td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";



        if(isset($status_r[0]['Quality'])){

            $quality_value = $status_r[0]['Quality'];

            if($quality_value < 1){


            $Alternative_city = $status_r[0]['Address']['City'];
            $Alternative_state = $status_r[0]['Address']['StateProvinceCode'];
            $Alternative_postalcode = $status_r[0]['PostalCodeLowEnd'];
            $City_alert = NULL;
            if($City !== $Alternative_city){
                $City_alert = '(Maybe that is the right city?)';
            }
            $State_alert = NULL;
            if($State !== $Alternative_state){
                $State_alert = '(Maybe that is the right state?)';
            }
            $PostCode_alert = NULL;
            if($PostCode !== $Alternative_postalcode){
                $PostCode_alert = '(Maybe that is the right state?)';
            }


            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>_____</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>It's not a 100% match, the closest alternative is:</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Alternative City: " .$Alternative_city. "".$City_alert."</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Alternative State/Province: " .$Alternative_state."". $State_alert."</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Alternative PostalCode: " .$Alternative_postalcode."". $PostCode_alert."</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";
            }
        }

        $message .= "<tr><td style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>_____</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr>";

		$message .= "<tr><td colspan=2 style='font-family:'Open Sans','Helvetica Neue','Helvetica','Arial','sans-serif';'vertical-align:top;padding-bottom:5px'><b style='font-weight:700;margin-right:10px'>Instructions: " .$instructions. "</b></td><td style='font-family':'Open Sans','Helvetica Neue','Helvetica,Arial,sans-serif;vertical-align:top;padding-bottom:5px'></td></tr></tbody></table>";

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
 * @param string $country What country we want to check
 * @param string $url     url for the shipping api address validation
 * @param string $ups_accesslicensenumber   Shipping Company License Numbre
 * @param string $ups_userid   Shipping User ID
 * @param string $ups_password   Shipping password
 */

function getLiveAddressSage($all_data, $url, $nextbillion_token){
    $host_smtp = PARAMS['host_smtp'];$email = PARAMS['email'];$pwd = PARAMS['pwd'];$emailto = PARAMS['emailto']; // Credentials
        if (is_array($all_data) || is_object($all_data))
        {
            foreach ($all_data as $value)
            {
                foreach ($value as $val)
                {

                    if(($val['UseInvoiceAddress'] == 0 &&  strlen($val['EmailAddress']) < 10) || ($val['UseInvoiceAddress'] == 1 &&  strlen($val['DefaultEmail']) < 10)){
                        $SO = $val['DocumentNo'];
                        $DocumentCreatedBy = $val['DocumentCreatedBy'];
                        $mainarray = $val;
                        $status_r = 0;
                        // my_mail_function($mainarray,$status_r, $SO, $DocumentCreatedBy,$host_smtp,$email,$pwd,$emailto);
                    }else{
                    if($val['UseInvoiceAddress'] == 0 ){ //UseInvoiceAddress = 0 means send to Delivery Address
                        $Address = NULL;
                        $PostCode =  str_replace('+','%20', urlencode($val['PostCode']));
                        $City =  str_replace('+','%20', urlencode($val['City']));
                        $State =  str_replace('+','%20', urlencode($val['State']));
                        $Country =  str_replace('+','%20', urlencode($val['Country']));
                        $SO =   str_replace('+','%20', urlencode($val['DocumentNo']));
                        $DocumentCreatedBy = $val['DocumentCreatedBy'];
                        $Email = $val['EmailAddress'];
                        $AddressLine =  str_replace('+','%20', urlencode($val['AddressLine1']));
                        $AddressLine2 =  str_replace('+','%20', urlencode($val['AddressLine2']));
                        $AddressLine .= $AddressLine2;
                        $Address = array($AddressLine,$City,$State,$Country);
                        $mainarray = $val;
                        $post_data = '' . implode(',', $Address) . '';
                        $post_data .= $nextbillion_token;
                        var_dump($post_data);
                        // var_dump($post_data);
                        // post_shipping_api($mainarray, $url,$post_data,$SO,$DocumentCreatedBy);
                    }elseif($val['UseInvoiceAddress'] == 1 ) { //UseInvoiceAddress = 1 means send to Invoice Address
                        $Address = NULL;
                        $PostCodeinv = str_replace('+','%20', urlencode($val['PostCodeinv']));
                        $Cityinv = str_replace('+','%20', urlencode($val['Cityinv']));
                        $Stateinv = str_replace('+','%20', urlencode($val['Stateinv']));
                        $Country =  str_replace('+','%20', urlencode($val['Countryinv']));
                        $SO = $val['DocumentNo'];
                        $DocumentCreatedBy = $val['DocumentCreatedBy'];
                        $mainarray = $val;
                        $Email = $val['DefaultEmail'];
                        $AddressLine1 = str_replace('+','%20', urlencode($val['AddressLine1inv']));
                        $AddressLine2 = str_replace('+','%20', urlencode($val['AddressLine2inv']));
                        $post_data = '' . implode(',', $Address) . '';
                        $post_data .= $nextbillion_token;
                        // post_shipping_api($mainarray,$url,$post_data,$SO,$DocumentCreatedBy);
                    }
                } //End condition to check if email is added
                } // End Second Foreach
            }// End First Foreach
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

function post_shipping_api($mainarray,$url,$post_data,$SO,$DocumentCreatedBy){

        $host_smtp = PARAMS['host_smtp'];$email = PARAMS['email'];$pwd = PARAMS['pwd'];$emailto = PARAMS['emailto']; // Credentials

        $post_data_str  = json_encode($post_data);

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_str);

        // $response = curl_exec($ch);

        // // Print All response success validation and failure
        // // echo '<pre>';var_dump($response);

        // $response  = json_decode($response, 1);

        // $status = $response["AddressValidationResponse"]["Response"]["ResponseStatusDescription"];
        // $status_r = NULL;
        // if(isset($response["AddressValidationResponse"]["AddressValidationResult"])){
        //     $status_r = $response["AddressValidationResponse"]["AddressValidationResult"];
        // }

        // $flag = 0;

        // if ($status == 'Success') {
        //     foreach ($response["AddressValidationResponse"]["AddressValidationResult"] as $data) {
        //         if (isset($data["Rank"])) {
        //             $flag++;
        //         }
        //     }
        // }

        // if($status == 'Failure' || $flag>0 ){
        //   my_mail_function($mainarray,$status_r, $SO, $DocumentCreatedBy,$host_smtp,$email,$pwd,$emailto);
        // }

        curl_close($ch);

    }

