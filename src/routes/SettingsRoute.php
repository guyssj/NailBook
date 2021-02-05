<?php


use Slim\Http\Response as Response;
use Slim\Http\Request as Request;
use BookNail\ResultAPI;
use BookNail\Settings;

$app->get('/admin/GetAllSettings', function (Request $request, Response $response) {
    $Settings = new Settings();
    $resultObj = new ResultAPI();
    try {
        $results = $Settings->get_All_Settings();
        $resultObj->set_result($results);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $th) {
        $resultObj->set_result($results);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($results);
        return $response->withJson($resultObj);
    }
});

$app->get('/api/GetSetting', function (Request $request, Response $response) {
    $Settings = new Settings();
    $resultObj = new ResultAPI();
    $Settings->SettingName = $request->getParam('SettingName');

    try {
        $results = $Settings->get_Setting($Settings->SettingName);
        $resultObj->set_result($results);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $th) {
        $resultObj->set_result($results);
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($results);
        return $response->withJson($resultObj);
    }
});

$app->put('/admin/UpdateSetting', function (Request $request, Response $response) {
    $resultObj = new ResultAPI();
    $settings = $request->getParsedBody();

    try {
        foreach ($settings as $key => $value) {
            if ($value['SettingValue'] == true && $value['SettingName'] == Settings::SEND_SMS_APP)
                $value['SettingValue'] = "1";
            else if ($value['SettingValue'] == false)
                $value['SettingValue'] = "0";
            $Settings = new Settings();
            $Settings->from_array($value);
            $Settings->update_setting();
        }
        $resultObj->set_result(1);
        $resultObj->set_statusCode($response->getStatusCode());
        echo json_encode($resultObj, JSON_UNESCAPED_UNICODE);
    } catch (Exception $th) {
        $resultObj->set_result($th->getMessage());
        $response = $response->withStatus(500);
        $resultObj->set_statusCode($response->getStatusCode());
        $resultObj->set_ErrorMessage($th->getMessage());
        return $response->withJson($resultObj);
    }
});
