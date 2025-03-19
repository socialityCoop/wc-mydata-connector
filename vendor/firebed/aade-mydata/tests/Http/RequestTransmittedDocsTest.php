<?php

namespace Tests\Http;

use Firebed\AadeMyData\Exceptions\MyDataException;
use Firebed\AadeMyData\Http\MyDataRequest;
use Firebed\AadeMyData\Http\RequestTransmittedDocs;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class RequestTransmittedDocsTest extends MyDataHttpTestCase
{
    /**
     * @throws MyDataException
     */
    public function test_transmitted_docs_are_received()
    {
        MyDataRequest::setHandler(new MockHandler([
            new Response(200, body: $this->getStub('request-doc-response')),
        ]));

        $request = new RequestTransmittedDocs();
        $requestedDoc = $request->handle();

        $this->assertCount(2, $requestedDoc->getInvoices());
        $this->assertCount(5, $requestedDoc->getCancelledInvoices());
        $this->assertCount(3, $requestedDoc->getPaymentMethods());
    }
}