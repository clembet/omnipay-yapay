<?php namespace Omnipay\Yapay\Message;

class PurchaseRequest extends AbstractRequest
{
    protected $resource = 'transactions/payment';
    protected $requestMethod = 'POST';
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */

    public function getData()
    {
        // faz o registro do cliente, se não houver especificado
        $this->validate('customer', 'paymentType');
        $data = parent::getData();
        
        $data2 = [];
        switch(strtolower($this->getPaymentType()))
        {
            case 'creditcard':
                $data2 = $this->getDataCreditCard();
                break;

            case 'boleto':
                $data2 = $this->getDataBoleto();
                break;

            case 'pix':
                $data2 = $this->getDataPix();
                break;

            default:
                $data2 = $this->getDataCreditCard();
        }

        $data = @array_merge($data, $data2);

        return $data;
    }
}
