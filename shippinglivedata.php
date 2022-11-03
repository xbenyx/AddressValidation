<?php

include_once("php/connect.php");

/**
 * This query returns all the address information from SAGE200, Billing Address and Delivery Address
 *
 */

$tsql = "SELECT DISTINCT [DocumentNo]
            ,ISNULL(dd.PostalName, '') AS PostalName
            ,ISNULL(dd.AddressLine1, '') AS AddressLine1
            ,ISNULL(dd.AddressLine2, '') AS AddressLine2
            ,ISNULL(dd.PostCode, '') AS PostCode
            ,ISNULL(dd.City, '') AS City
            ,ISNULL(dd.County, '') AS State
            ,ISNULL(dd.Country, '') AS Country
            ,ISNULL(dd.Contact, '') AS Contact
            ,ISNULL(dd.TelephoneNo, '') AS TelephoneNo
            ,ISNULL(dd.EmailAddress, '') AS EmailAddress
            ,ISNULL(ca.CustomerAccountName, '') AS CustomerAccountNameinv
            ,ISNULL(lo.AddressLine1, '') AS AddressLine1inv
            ,ISNULL(lo.AddressLine2, '') AS AddressLine2inv
            ,ISNULL(lo.PostCode, '') AS PostCodeinv
            ,ISNULL(lo.City, '') AS Cityinv
            ,ISNULL(lo.County, '') AS Stateinv
            ,ISNULL(lo.Country, '') AS Countryinv
            ,ISNULL( TRIM(CONCAT(ca.MainTelephoneAreaCode,ca.MainTelephoneCountryCode,+ca.MainTelephoneSubscriberNumber)), '') AS MainTelephoneSubscriberNumber
            ,ISNULL(cont.DefaultEmail, '') AS DefaultEmail
            ,so.UseInvoiceAddress
            ,so.DocumentCreatedBy
    FROM [MULLANIE].[dbo].[SOPOrderReturnLine] AS sl
    LEFT JOIN [MULLANIE].[dbo].[SiWorksOrder] AS wo ON sl.SOPOrderReturnLineID = wo.SOPOrderReturnLineId
    LEFT JOIN [MULLANIE].[dbo].[SOPOrderReturn] AS so ON sl.SOPOrderReturnID = so.SOPOrderReturnID
    LEFT JOIN [MULLANIE].[dbo].[SLCustomerAccount] AS ca ON so.[CustomerID] = ca.[SLCustomerAccountID]
    LEFT JOIN [MULLANIE].[dbo].[SLCustomerLocation] AS lo ON lo.[SLCustomerAccountID] = ca.[SLCustomerAccountID]
    LEFT JOIN [MULLANIE].[dbo].[SYSCountryCode] AS cc ON ca.[SYSCountryCodeID] = cc.[SYSCountryCodeID]
    LEFT JOIN [MULLANIE].[dbo].[SOPDocDelAddress] AS dd ON so.[SOPOrderReturnID] = dd.[SOPOrderReturnID]
    LEFT JOIN [MULLANIE].[dbo].[SYSCountryCode] AS dc ON dd.CountryCodeID = dc.SYSCountryCodeID
    LEFT JOIN [MULLANIE].[dbo].[StockItem] AS MLSKU ON wo.StockItemID = MLSKU.ItemID
    LEFT JOIN [MULLANIE].[dbo].[ProductGroup] AS MLGroup ON MLSKU.ProductGroupID = MLGroup.ProductGroupID
    LEFT JOIN [MULLANIE].[dbo].[BomRecord] ON [MULLANIE].[dbo].[BomRecord].[Reference] = sl.[ItemCode]
    LEFT JOIN [MULLANIE].[dbo].[swapout] sw ON ca.CustomerAccountNumber = sw.account AND MLSKU.Code LIKE (sw.product + '%') AND MLSKU.Code = sw.component
    LEFT JOIN [MULLANIE].[dbo].[StockItem] AS swstk ON sw.component_swap_for = swstk.Code
    LEFT JOIN [MULLANIE].[dbo].[BinItem] AS swbin ON swstk.[ItemID] = swbin.[ItemID]
    LEFT JOIN [MULLANIE].[dbo].[SLCustomerContactDefaultsVw] cont ON cont.SLCustomerAccountID = ca.SLCustomerAccountID
    WHERE [WOStatus] NOT IN ('Deleted')
    AND Level = '0'
    AND so.[DocumentTypeID] = '0'
    AND so.[InvoiceCreditStatusID] <> '2'
    AND so.[CancelledStatusID] IN ('0','1')
    AND so.[DocumentStatusID] = '0'
    AND cont.ContactRoleName = 'Account'
	AND cont.IsPreferredContactForRole = 1
    AND so.[DocumentNo] NOT LIKE '%#%'
";
$getResults = $conn->prepare($tsql);
$getResults->execute();
$results = $getResults->fetchAll(PDO::FETCH_BOTH);
$all_sales = array();


$all_data = array($results);
