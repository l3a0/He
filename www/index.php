<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
    <input type="file" name="pictures" accept="image/*" />
    <input type="submit" value="upload" />
</form>

<?php

require_once '../he/vendor/autoload.php';
require_once 'HTTP/Request2.php';

if ($_FILES['pictures']['tmp_name']) {
    $request = buildRequest();

    try {
        $response = $request->send();
        $responseBody = json_decode($response->getBody());

        echo '<table>';
        echo '<tr>';

        echo '<td valign="top" width="25%">';
        printText($responseBody);
        echo '</td>';

        echo '<td>';
        printImage();
        echo '</td>';

        echo '</tr>';
        echo '</table>';

        printResponse($responseBody);
    } catch (HttpException $error) {
        printError($error);
    }
}

function buildRequest()
{
    // Replace <Subscription Key> with a valid subscription key.
    $ocpApimSubscriptionKey = '<Subscription Key>';

    // You must use the same location in your REST call as you used to obtain
    // your subscription keys. For example, if you obtained your subscription keys
    // from westus, replace "westcentralus" in the URL below with "westus".
    $uriBase = 'https://westus2.api.cognitive.microsoft.com/vision/v2.0/';

    $request = new Http_Request2($uriBase . 'ocr');
    $request->setMethod(HTTP_Request2::METHOD_POST);

    $url = $request->getUrl();

    $headers = array(
        // Request headers
        'Content-Type' => 'application/octet-stream',
        'Ocp-Apim-Subscription-Key' => $ocpApimSubscriptionKey
    );
    $request->setHeader($headers);

    $parameters = array(
        // Request parameters
        'language' => 'unk',
        'detectOrientation' => 'true'
    );
    $url->setQueryVariables($parameters);

    // Request body
    $data = file_get_contents($_FILES['pictures']['tmp_name']);
    $request->setBody($data);

    return $request;
}

function printText($responseBody)
{
    echo '<p>';
    foreach ($responseBody->regions[0]->lines as $line) {
        foreach ($line->words as $word) {
            echo $word->text . ' ';
        }
    }
    echo '</p>';
}

function printResponse($responseBody)
{
    echo "<pre>" . json_encode($responseBody, JSON_PRETTY_PRINT) . "</pre>";
}

function printError($error)
{
    echo "<pre>" . $error . "</pre>";
}

function printImage()
{ 
    $data = file_get_contents($_FILES['pictures']['tmp_name']);
    echo '<img src="data:image/jpeg;base64,' . base64_encode( $data ).'" />';
}
