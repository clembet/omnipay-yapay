<?php namespace Omnipay\Yapay\Message;


abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    protected $liveEndpoint = 'https://api.intermediador.yapay.com.br/api';
    protected $testEndpoint = 'https://api.intermediador.sandbox.yapay.com.br/api';
    protected $version = 3;
    protected $requestMethod = 'POST';
    protected $resource = 'transactions/payment';

    public function getData()
    {
        $this->validate('merchantToken');

        $data = [
            "token_account" => $this->getMerchantToken()
        ];

        return $data;
    }

    public function sendData($data)
    {
        $method = $this->requestMethod;
        $url = $this->getEndpoint();

        $headers = [
            'Content-Type' => 'application/json',
        ];

        //print_r([$method, $url, $headers, json_encode($data)]);//exit();
        $response = $this->httpClient->request(
            $method,
            $url,
            $headers,
            $this->toJSON($data)
            //http_build_query($data, '', '&')
        );
        //print_r($response);
        //print_r($data);

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 201 && $response->getStatusCode() != 400) {
            $array = [
                'error' => [
                    'code' => $response->getStatusCode(),
                    'message' => $response->getReasonPhrase()
                ]
            ];

            return $this->response = $this->createResponse($array);
        }

        $json = $response->getBody()->getContents();
        $array = @json_decode($json, true);
        //print_r($array);

        return $this->response = $this->createResponse(@$array);
    }

    protected function setBaseEndpoint($value)
    {
        $this->baseEndpoint = $value;
    }

    public function __get($name)
    {
        return $this->getParameter($name);
    }

    protected function setRequestMethod($value)
    {
        return $this->requestMethod = $value;
    }

    protected function decode($data)
    {
        return json_decode($data, true);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getMerchantToken()
    {
        return $this->getParameter('merchantToken');
    }

    public function setMerchantToken($value)
    {
        return $this->setParameter('merchantToken', $value);
    }

    public function getAccessToken()
    {
        return $this->getParameter('accessToken');
    }

    public function setAccessToken($value)
    {
        return $this->setParameter('accessToken', $value);
    }

    public function setOrderId($value)
    {
        return $this->setParameter('order_id', $value);
    }
    public function getOrderId()
    {
        return $this->getParameter('order_id');
    }

    public function setInstallments($value)
    {
        return $this->setParameter('installments', $value);
    }
    public function getInstallments()
    {
        return $this->getParameter('installments');
    }

    public function setSoftDescriptor($value)
    {
        return $this->setParameter('soft_descriptor', $value);
    }
    public function getSoftDescriptor()
    {
        return $this->getParameter('soft_descriptor');
    }

    public function getPaymentProvider()
    {
        return $this->getParameter('paymentProvider');
    }

    public function setPaymentProvider($value)
    {
        $this->setParameter('paymentProvider', $value);
    }

    public function getPaymentType()
    {
        return $this->getParameter('paymentType');
    }

    public function setPaymentType($value)
    {
        $this->setParameter('paymentType', $value);
    }
    
    public function getClientFingerPrint()
    {
        return $this->getParameter('clientFingerPrint');
    }

    public function setClientFingerPrint($value)
    {
        $this->setParameter('clientFingerPrint', $value);
    }

    public function getTransactionID()
    {
        return $this->getParameter('transactionId');
    }

    public function setTransactionID($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    public function getTokenTransaction()
    {
        return $this->getParameter('tokenTransaction');
    }

    public function setTokenTransaction($value)
    {
        return $this->setParameter('tokenTransaction', $value);
    }

    public function getShippingType()
    {
        return $this->getParameter('shipping_type');
    }

    public function setShippingType($value)
    {
        return $this->setParameter('shipping_type', $value);
    }

    public function getShippingPrice()
    {
        return $this->getParameter('shipping_price');
    }

    public function setShippingPrice($value)
    {
        return $this->setParameter('shipping_price', $value);
    }
    
    public function getDueDate()
    {
        $dueDate = $this->getParameter('dueDate');
        if($dueDate)
            return $dueDate;

        $time = localtime(time());
        $ano = $time[5]+1900;
        $mes = $time[4]+1+1;
        $dia = 1;// $time[3];
        if($mes>12)
        {
            $mes=1;
            ++$ano;
        }

        $dueDate = sprintf("%02d/%02d/%04d", $dia, $mes, $ano);
        $this->setDueDate($dueDate);

        return $dueDate;
    }

    public function setDueDate($value)
    {
        return $this->setParameter('dueDate', $value);
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getMethod()
    {
        return $this->requestMethod;
    }

    public function toJSON($data, $options = 0)
    {
        if (version_compare(phpversion(), '5.4.0', '>=') === true) {
            return json_encode($data, $options | 64);
        }
        return str_replace('\\/', '/', json_encode($data, $options));
    }

    public function getDataCreditCard()
    {
        $this->validate('card', 'clientIp', 'clientFingerPrint');
        $card = $this->getCard();

        //https://intermediador.dev.yapay.com.br/#/transacao-tabela-campos
        $data = [
            "finger_print"=> $this->getClientFingerPrint(),
            "customer" => $this->getCustomerData(),
            "transaction_product"=> $this->getItemData(),
            "transaction"=>[
                "order_number"=>$this->getOrderId(),
                "available_payment_methods"=>"2,3,4,5,6,7,14,15,16,18,19,21,22,23",
                "customer_ip"=>$this->getClientIp(),
                "shipping_type"=>"Sedex",
                "shipping_price"=>$this->getShippingPrice(),
                //"price_additional"=>"",
                "price_discount"=>"",
                "url_notification"=>$this->getNotifyUrl(),
                //"free"=>""//Campo Livre
                ],
            //"transaction_trace"=> [
            //    "estimated_date"=> "02/04/2050" // TODO: verificar as regras para esse campo
            //    ],
            "payment"=>[
                "payment_method_id"=>"3",
                "card_name"=>$card->getName(),
                "card_number"=>$card->getNumber(),
                "card_expdate_month"=>$card->getExpiryMonth(),
                "card_expdate_year"=>$card->getExpiryYear(),
                "card_cvv"=>$card->getCvv(),
                "split"=>$this->getInstallments()
                ]
        ];

        return $data;
    }

    public function getDataBoleto()
    {
        $this->validate('clientIp');
        $customer = $this->getCustomerData();

        $data = [
            //"finger_print"=> $this->getClientFingerPrint(),
            "customer" => $this->getCustomerData(),
            "transaction_product"=> $this->getItemData(),
            "transaction"=>[
                "order_number"=>$this->getOrderId(),
                "available_payment_methods"=>"2,3,4,5,6,7,14,15,16,18,19,21,22,23",
                "customer_ip"=>$this->getClientIp(),
                "shipping_type"=>"Sedex",
                "shipping_price"=>$this->getShippingPrice(),
                //"price_additional"=>"",
                "price_discount"=>"",
                "url_notification"=>$this->getNotifyUrl(),
                //"free"=>""//Campo Livre
                ],
            //"transaction_trace"=> [
                //"estimated_date"=> "02/04/2050" // TODO: verificar as regras para esse campo
            //    ],
            "payment"=>[
                "payment_method_id"=>"6",
                "billet_date_expiration"=>$this->getDueDate(),//TODO: formato dd/mm/yyy
                "split"=>"1"
                ]
        ];

        return $data;
    }

    public function getDataPix()
    {
        $this->validate('clientIp');

        $data = [
            //"finger_print"=> $this->getClientFingerPrint(),
            "customer" => $this->getCustomerData(),
            "transaction_product"=> $this->getItemData(),
            "transaction"=>[
                "order_number"=>$this->getOrderId(),
                "customer_ip"=>$this->getClientIp(),
                "shipping_type"=>"Sedex",
                "shipping_price"=>$this->getShippingPrice(),
                //"price_additional"=>"",
                "url_notification"=>$this->getNotifyUrl(),
                //"free"=>""//Campo Livre
                ],
            //"transaction_trace"=> [
            //    "estimated_date"=> "02/04/2050" // TODO: verificar as regras para esse campo
            //    ],
            "payment"=>[
                "payment_method_id"=>"27",
                "split"=>"1"
                ]
        ];

        return $data;
    }

    public function getCustomer()
    {
        return $this->getParameter('customer');
    }

    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
    }

    public function getCustomerData()
    {
        $this->validate('customer');
        $customer = $this->getCustomer();

        $data = [
                "contacts" => [
                        [
                            "type_contact"=>"M",
                            "number_contact"=> $customer->getPhone()
                        ]
                    ],
                "addresses" => [
                        [
                            "type_address"=>"B",
                            "postal_code"=>$customer->getBillingPostcode(),
                            "street"=>$customer->getBillingAddress1(),
                            "number"=>$customer->getBillingNumber(),
                            "completion"=>$customer->getBillingAddress2(),
                            "neighborhood"=>$customer->getBillingDistrict(),
                            "city"=>$customer->getBillingCity(),
                            "state"=>$customer->getBillingState()
                        ]
                    ],
                "name"=>$customer->getName(),
                "birth_date"=>$customer->getBirthday('d/m/Y'),
                "cpf"=>$customer->getDocumentNumber(),
                "email"=>$customer->getEmail()
            ];

        return $data;
    }

    public function getItemData()
    {
        $this->validate('items');
        $data = [];
        $items = $this->getItems();

        if ($items) {
            foreach ($items as $n => $item) {

                $item_array = [
                    "description"=>$item->getName(),
                    "quantity"=>$item->getQuantity(),
                    "price_unit"=>$item->getPrice(),
                    "code"=>$n+1,
                    //"sku_code"=>"0001",
                    //"extra"=>""
                ];

                array_push($data, $item_array);
            }
        }

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    protected function getEndpoint()
    {
        $version = $this->getVersion();
        $endPoint = ($this->getTestMode()?$this->testEndpoint:$this->liveEndpoint);
        return  "{$endPoint}/v{$version}/{$this->getResource()}";
    }
}
