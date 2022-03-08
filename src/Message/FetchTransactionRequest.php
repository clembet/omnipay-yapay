<?php namespace Omnipay\Yapay\Message;


//https://intermediador.dev.yapay.com.br/#/api-consultar-transacao # usado a qualquer momento
//https://intermediador.dev.yapay.com.br/#/notificacao-automatica-status-consulta-transacao  # usado ao receber uma notificação
class FetchTransactionRequest extends AbstractRequest
{
    //protected $resource = 'transactions/get_by_token_brief';
    protected $resource = 'transactions/get_by_token';
    protected $requestMethod = 'GET';

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        return [];
    }

    protected function getEndpoint()
    {
        $version = $this->getVersion();
        $endPoint = ($this->getTestMode()?$this->testEndpoint:$this->liveEndpoint);
        return  "{$endPoint}/v{$version}/{$this->getResource()}?token_account={$this->getMerchantToken()}&token_transaction={$this->getTokenTransaction()}";
    }
}
