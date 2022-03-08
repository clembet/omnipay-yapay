<?php namespace Omnipay\Yapay;

use Omnipay\Common\AbstractGateway;

/**
 * https://intermediador.dev.yapay.com.br/#/transacao-fingerprint
 * https://intermediador.dev.yapay.com.br/#/transacao-cartao-credito
 * https://intermediador.dev.yapay.com.br/#/transacao-boleto
 * https://intermediador.dev.yapay.com.br/#/transacao-pix
 * 
 * <script src="https://static.traycheckout.com.br/js/finger_print.js" type="text/javascript"></script>
 * <script>window.yapay.FingerPrint({ env: 'sandbox' });</script>
 * <script type="text/javascript">jQuery(document).FingerPrint().getFingerPrint();                </script>
 * 
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface authorize(array $parameters = [])
 * @method \Omnipay\Common\Message\RequestInterface capture(array $parameters = [])
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     * @return string
     */
    public function getName()
    {
        return 'Yapay';
    }

    /**
     * Define gateway parameters, in the following format:
     *
     * [
     *     'apiKey' => '', // string The Merchant Key
     * ];
     * @return array
     */
    public function getDefaultParameters()
    {
        return [
            'merchantToken' => '',
            'testMode' => false,
        ];
    }

    public function getMerchantToken()
    {
        return $this->getParameter('merchantToken');
    }

    public function setMerchantToken($value)
    {
        return $this->setParameter('merchantToken', $value);
    }

    public function parseResponse($data)
    {
        $request = $this->createRequest('\Omnipay\Yapay\Message\PurchaseRequest', []);
        return new \Omnipay\Yapay\Message\Response($request, (array)$data);
    }

    /**
     * Authorize Request
     *
     * An Authorize request is similar to a purchase request but the
     * charge issues an authorization (or pre-authorization), and no money
     * is transferred.  The transaction will need to be captured later
     * in order to effect payment. Uncaptured charges expire in 5 days.
     *
     * Either a card object or card_id is required by default. Otherwise,
     * you must provide a card_hash, like the ones returned by Yapay
     *
     * Yapay gateway supports only two types of "payment_method":
     *
     * * credit_card
     *
     * Optionally, you can provide the customer details to use the antifraude
     * feature. These details is passed using the following attributes available
     * on credit card object:
     *
     * * firstName
     * * lastName
     * * address1 (must be in the format "street, street_number and neighborhood")
     * * address2 (used to specify the optional parameter "street_complementary")
     * * postcode
     * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
     *
     * @param array $parameters
     * @return \Omnipay\Yapay\Message\AuthorizeRequest
     */
    /*public function authorize(array $parameters = [])//ok
    {
        return $this->createRequest('\Omnipay\Yapay\Message\AuthorizeRequest', $parameters);
    }*/

    public function acceptNotification(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Yapay\Message\NotificationRequest', $parameters);
    }

    /**
     * Capture Request
     *
     * Use this request to capture and process a previously created authorization.
     *
     * @param array $parameters
     * @return \Omnipay\Yapay\Message\CaptureRequest
     */
    /*public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Yapay\Message\CaptureRequest', $parameters);
    }*/

    /**
     * Purchase request.
     *
     * To charge a credit card  you create a new transaction
     * object. If your MerchantID is in test mode, the supplied card won't actually
     * be charged, though everything else will occur as if in live mode.
     *
     * Either a card object or card_id is required by default. Otherwise,
     * you must provide a card_hash, like the ones returned by Yapay
     *
     * Yapay gateway supports only one type of "payment_method":
     *
     * * credit_card
     *
     *
     * Optionally, you can provide the customer details to use the antifraude
     * feature. These details is passed using the following attributes available
     * on credit card object:
     *
     * * firstName
     * * lastName
     * * address1 (must be in the format "street, street_number and neighborhood")
     * * address2 (used to specify the optional parameter "street_complementary")
     * * postcode
     * * phone (must be in the format "DDD PhoneNumber" e.g. "19 98888 5555")
     *
     * @param array $parameters
     * @return \Omnipay\Yapay\Message\PurchaseRequest
     */
    public function purchase(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Yapay\Message\PurchaseRequest', $parameters);
    }


    /**
     * Void Transaction Request
     *
     *
     *
     * @param array $parameters
     * @return \Omnipay\Yapay\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Yapay\Message\VoidRequest', $parameters);
    }

    public function fetchTransaction(array $parameters = [])
    {
        return $this->createRequest('\Omnipay\Yapay\Message\FetchTransactionRequest', $parameters);
    }
}
