<?php namespace Omnipay\Yapay\Message;

/**
 *  O Cancelamento é aplicavel a transações do mesmo dia sendo autorizadas ou aprovadas
 *  O Estono é aplicável para transações onde virou o dia, seguindo o processo do adquirente
 * <code>
 *   // Do a refund transaction on the gateway
 *   $transaction = $gateway->void(array(
 *       'transactionId'     => $transactionCode,
 *   ));
 *
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *   }
 * </code>
 */

 //https://intermediador.dev.yapay.com.br/#/api-cancelar-transacao
class VoidRequest extends AbstractRequest
{
    protected $resource = 'transactions/cancel';
    protected $requestMethod = 'PATCH';


    public function getData()
    {
        $this->validate('transactionId', 'amount');
        $data = parent::getData();
        $data['transaction_id'] = $this->getTransactionID();
        $data['refund_amount'] = $this->getAmount();

        return $data;
    }   
}
