<?php
namespace integrations\companies\integrated\yemek_sepeti\php\helper\service_1\helper;

use integrations\companies\integrated\yemek_sepeti\php\config\service;
use integrations\results;
use SoapFault;
use Throwable;

class set {
    private service $service;

    public function __construct(service $service){
        $this->service = $service;
    }

    public function message_successful(string $message_id) : results{
        $result = new results();

        try{
            $parameters = array(
                "messageId" => $message_id
            );
            $result->message = json_encode(@$this->service->client->MessageSuccessfulV2($parameters));
            $result->status = true;
        }catch (SoapFault | Throwable $e){ $result->message = $e; }

        return $result;
    }

    public function update_order(string $order_id, string $order_state, string $reason) : results{
        $result = new results();

        try{
            $parameters = array(
                "orderId" => $order_id,
                "orderState" => $order_state,
                "reason" => $reason
            );
            $result->message = json_encode(@$this->service->client->UpdateOrder($parameters));
            $result->status = true;
        }catch (SoapFault | Throwable $e){ $result->message = $e; }

        return $result;
    }
}