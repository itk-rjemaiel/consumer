<?php

namespace Itkg\Consumer;

use Itkg\Consumer\ClientInterface;
use Itkg\Consumer\Request;
use Itkg\Consumer\Response;
use Itkg\Consumer\Service\Event\FilterServiceEvent;
use Itkg\Consumer\Service\Events;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Service
{
    protected $loggers;
    protected $identifier;
    protected $request;
    protected $response;
    protected $client;
    protected $exception;
    protected $eventDispatcher;

    public function __construct($identifier, Request $request, Response $response,
        ClientInterface $client, $loggers = array())
    {
        $this->identifier = $identifier;
        $this->request = $request;
        $this->response = $response;
        $this->client = $client;
        $this->loggers = $loggers;
    }

    public function before($datas = array())
    {
        $this->request->bind($datas);
        $this->sendEvent(Events::BIND_REQUEST);
        $this->client->init($this->request);
        $this->initLoggers();
    }

    public function call($datas = array())
    {
        $this->before($datas);

        try {
            $this->sendEvent(Events::PRE_CALL);
            $this->client->call();
            $this->sendEvent(Events::SUCCESS_CALL);
        }catch(\Exception $e) {
            $this->exception = $e;
            $this->sendEvent(Events::FAIL_CALL);
        }
        $this->sendEvent(Events::POST_CALL);
        $this->after();

        return $this->response;
    }

    public function setEventDispatcher(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    public function sendEvent($eventType)
    {
        $this->eventDispatcher->dispatch($eventType, new FilterServiceEvent($this));

    }

    public function after()
    {
        if($this->exception) {
            throw $this->exception;
        }

        $this->response->bind($this->client->getResponse());
        $this->sendEvent(Events::BIND_RESPONSE);
    }

    public function initLoggers()
    {
        foreach($this->getLoggers() as $logger) {
            $logger->init($this->identifier);
        }
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getLoggers()
    {
        if(!is_array($this->loggers)) {
            $this->loggers = array();
        }
        return $this->loggers;
    }

    public function setLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    public function getException()
    {
        return $this->exception;
    }

    public function setException(\Exception $exception)
    {
        $this->exception = $exception;
    }
}