<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RequestController extends AbstractController
{
    /**
     * @Route("/", name="request")
     */
    public function index()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $response["fulfillmentText"]='Hello from API';
        //Extract intent
        $intent =  $data['queryResult']['intent']['displayName'];
        switch ($intent){
            case "Weather":
                $city = $data['queryResult']['parameters']['geo-capital'];
                $encoded = json_decode(file_get_contents('http://api.openweathermap.org/data/2.5/weather?q='.$city.'&APPID=7ce9d4563a62fc5527f03b1234e972f4'),true);
                file_put_contents('filename.txt', print_r($encoded, true));
                $descript = $encoded["weather"][0]["description"];
                $tempK = $encoded["main"]["temp"];
                $tempC = $tempK - 272.15;
                $response["fulfillmentText"]='The temperature in '.$city.' is '.$tempC.'<br/>'.'And the weather is '.$descript;
                break;

            case "Covid19":
                $encoded = json_decode(file_get_contents('https://api.apify.com/v2/key-value-stores/EaCBL1JNntjR3EakU/records/LATEST?disableRedirect=true'),true);
                $total = $encoded["infected"];
                $treated = $encoded["treated"];
                $recovered = $encoded["recovered"];
                $deceased = $encoded["deceased"];
                $response["fulfillmentText"]='Today we are having:<br> -Total: '.$total.'<br/>'.'-Treated: '.$treated.' <br/>-Recovered: '.$recovered.' <br/>-Death: '.$deceased.'</br>';

                break;

        }

        //Send back response to Dialogflow
        header('Content-type:application/json;charset=utf-8');
        echo json_encode($response);

        return  $this->json([ 
        ]);
    }
}
