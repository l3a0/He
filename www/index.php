<?php

require_once '../he/vendor/autoload.php';
require_once 'HTTP/Request2.php';

// Replace <Subscription Key> with a valid subscription key.
$ocpApimSubscriptionKey = '<insert api key here>';

// You must use the same location in your REST call as you used to obtain
// your subscription keys. For example, if you obtained your subscription keys
// from westus, replace "westcentralus" in the URL below with "westus".
$uriBase = 'https://westus2.api.cognitive.microsoft.com/vision/v2.0/';

$request = new Http_Request2($uriBase . 'ocr');
$request->setMethod(HTTP_Request2::METHOD_POST);

$url = $request->getUrl();

$headers = array(
    // Request headers
    'Content-Type' => 'application/json',
    'Ocp-Apim-Subscription-Key' => $ocpApimSubscriptionKey
);
$request->setHeader($headers);

$parameters = array(
    // Request parameters
    'language' => 'unk',
    'detectOrientation' => 'true'
);
$url->setQueryVariables($parameters);

// Request body parameters
$imageUrl = 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/' .
    'Atomist_quote_from_Democritus.png/338px-Atomist_quote_from_Democritus.png';
$body = json_encode(array('url' => $imageUrl));

// Request body
$request->setBody($body);

try
{
    $response = $request->send();
    echo "<pre>" . json_encode(json_decode($response->getBody()), JSON_PRETTY_PRINT) . "</pre>";
}
catch (HttpException $ex)
{
    echo "<pre>" . $ex . "</pre>";
}
