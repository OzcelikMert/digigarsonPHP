<?php
namespace integrations\companies\integrated\yemek_sepeti\php\helper\service_1\helper;

use Exception;
use integrations\companies\integrated\yemek_sepeti\php\config\service;
use integrations\results;
use SoapFault;
use Throwable;

class get {
    private service $service;

    public function __construct(service $service){
        $this->service = $service;
    }

    public function products() : results{
        $result = new results();

        try{
            $result->rows = service::xml_to_array(@$this->service->client->GetMenu()->GetMenuResult->any);
            $result->status = true;
        }catch (SoapFault | Throwable $e){ $result->message = $e; }

        return $result;
    }

    public function restaurant_list() : results {
        $result = new results();

        try{
            $result->rows = service::xml_to_array(@$this->service->client->GetRestaurantList()->GetRestaurantListResult->any);
            $result->status = true;
        }catch (SoapFault | Throwable $e){ $result->message = $e; }

        return $result;
    }

    public function messages() : results {
        $result = new results();

        try{
            $result->rows = service::xml_to_array(@$this->service->client->GetAllMessagesV2()->GetAllMessagesV2Result);
            $result->status = true;
        }
        catch (SoapFault | Throwable $e){ $result->message = $e; }

        return $result;
    }

    public function payments_types() : results {
        $result = new results();

        try{
            $result->rows = service::xml_to_array(@$this->service->client->GetPaymentTypes()->GetPaymentTypesResult->any);
            $result->status = true;
        }catch (SoapFault | Throwable $e){ $result->message = $e; }

        return $result;
    }
}