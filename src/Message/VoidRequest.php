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
        $this->validate('accessToken', 'transactionId', 'amount');
        $data = [
            "access_token" => $this->getAccessToken(),// o access_token você na "API de Autorização1" para maiores informações contactar integracao@yapay.com.br
            "transaction_id" => $this->getTransactionID(),
            "reason_cancellation_id"=> "6",
            "refund_amount" => $this->getAmount()
        ];


        return $data;
    }
}
