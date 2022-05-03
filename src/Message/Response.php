<?php namespace Omnipay\Yapay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Pagarme Response
 *
 * This is the response class for all Pagarme requests.
 *
 * @see \Omnipay\Pagarme\Gateway
 */
class Response extends AbstractResponse
{
    /**
     * Is the transaction successful?
     *
     * @return bool
     */
    public function isSuccessful()
    {
        //$result = $this->data;
        if(isset($this->data['error_response']))
            return false;

        if (isset($this->data['data_response']['transaction']['status_id']) && isset($this->data['data_response']['transaction']['token_transaction']))
            return true;

        return false;
    }

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionID()
    {
        if(isset($this->data['data_response']['transaction']['transaction_id']))
            return @$this->data['data_response']['transaction']['transaction_id'];

        return NULL;
    }

    public function getTransactionAuthorizationCode()
    {
        if(isset($this->data['data_response']['transaction']['token_transaction']))
            return @$this->data['data_response']['transaction']['token_transaction'];

        return NULL;
    }

    public function getStatus()//https://intermediador.dev.yapay.com.br/#/tabelas?id=tabela-4-status-da-transa%c3%a7%c3%a3o
    {
        $status = null;
        if(isset($this->data['data_response']['transaction']['status_id']))
            $status = @$this->data['data_response']['transaction']['status_id'];
        else
        {
            if(isset($this->data['Status']))
                $status = @$this->data['Status'];
        }

        return $status;
    }

    public function isPaid()
    {
        $status = $this->getStatus();
        $error = trim(@$this->data['data_response']['transaction']['payment']['payment_response']);
        $response_code = trim(@$this->data['data_response']['transaction']['payment']['payment_response_code']);
        return ($status==6 && ((strcmp($response_code, "00") == 0) || (strlen($error) <= 0)));//Aprovada
    }

    public function isAuthorized()
    {
        //$status = $this->getStatus();
        return false;
    }

    public function isPending()
    {
        $status = $this->getStatus();
        $error = trim(@$this->data['data_response']['transaction']['payment']['payment_response']);
        return ($status==4 && (strlen($error) <= 0));//Aguardando Pagamento
    }

    public function isVoided()
    {
        $status = $this->getStatus();
        return ($status==7||$status==24);//Cancelada
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage()//https://intermediador.dev.yapay.com.br/#/transacao-erros
    {
        //print_r($this->data);
        if(isset($this->data['error']))
            return "{$this->data['error']['code']} - {$this->data['error']['message']}";

        if(isset($this->data['error_response']))
        {

            if(isset($this->data['error_response ']['validation_errors']))
                return @$this->data['error_response ']['validation_errors']['code']." - ".@$this->data['error_response ']['validation_errors']['message']." ".@$this->data['error_response ']['validation_errors']['field'].", ".@$this->data['error_response ']['validation_errors']['message_complete'];

                if(isset($this->data['error_response ']['general_errors']))
                return @$this->data['error_response ']['validation_errors']['code']." - ".@$this->data['error_response ']['validation_errors']['message'];
        }

        if(isset($this->data['data_response']['transaction']['payment']['payment_response']) && (strlen($this->data['data_response']['transaction']['payment']['payment_response'])>5))
            return $this->data['data_response']['transaction']['payment']['payment_response'];

        return null;
    }

    public function getBoleto()
    {
        $data = $this->getData();
        $boleto = array();
        $boleto['boleto_url'] = @$data['data_response']['transaction']['payment']['url_payment'];
        $boleto['boleto_url_pdf'] = @$data['data_response']['transaction']['payment']['url_payment'];
        $boleto['boleto_barcode'] = @$data['data_response']['transaction']['payment']['linha_digitavel'];
        $boleto['boleto_expiration_date'] = NULL;
        $boleto['boleto_valor'] = @$data['data_response']['transaction']['payment']['price_payment']*1.0;
        $boleto['boleto_transaction_id'] = @$data['data_response']['transaction']['transaction_id'];// token_transaction
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }

    public function getPix()
    {
        $data = $this->getData();
        $boleto = array();
        $boleto['pix_qrcodebase64image'] = $this->getBase64ImageFromUrl(@$data['data_response']['transaction']['payment']['qrcode_path']);
        $boleto['pix_qrcodestring'] = @$data['data_response']['transaction']['payment']['qrcode_original_path'];
        $boleto['pix_valor'] = @$data['data_response']['transaction']['payment']['price_payment']*1.0;
        $boleto['pix_transaction_id'] = @$data['data_response']['transaction']['transaction_id'];// token_transaction
        //@$this->setTransactionReference(@$data['transaction_id']);

        return $boleto;
    }

    public function getBase64ImageFromUrl($url)
    {
        $type = @pathinfo($url, PATHINFO_EXTENSION);
        if(strcmp($type, 'svg')==0)
            $type = 'svg+xml';
        $data = @file_get_contents($url);
        if (!$data)
            return NULL;

        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }
}