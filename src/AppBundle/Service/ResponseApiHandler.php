<?php

namespace AppBundle\Service;

use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Response;

class ResponseApiHandler
{
    public $serializer;
    public $errorMessage = false;
    public $successResult = false;

    public $data = null;
    public $status = Response::HTTP_OK;
    public $headers = [
        'Content-Type' => 'application/json',
    ];

    const METHOD_DM_FIRST = 'FIRST';
    const METHOD_DM_LAST  = 'LAST';
    public $currentMethodDM = self::METHOD_DM_FIRST;

    public function __construct()
    {
        $this->serializer = SerializerBuilder::create()->build();
    }


    /**
     * Выставление всех http заголовков страницы
     *
     * @param array $headers
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
    }


    /**
     * Добавление http заголовка страницы к уже имеющимся
     *
     * @param array $headers
     */
    public function addHeaders($headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }


    /**
     * Присваивание способа вывода ошибок
     *
     * @param string $method
     */
    public function setMethodDM($method)
    {
        $this->currentMethodDM = $method;
    }


    /**
     * Присваивание сообщения об ошибки
     *
     * @param string $message
     * @param bool|int $statusCode
     */
    public function setMessage($message, $statusCode = false)
    {
        if ($this->currentMethodDM == self::METHOD_DM_FIRST && $this->errorMessage) {
            return;
        }

        if ($statusCode) {
            $this->setStatusCode($statusCode);
        }

        $this->errorMessage = $message;
    }


    /**
     * Проверка на существование ошибок
     *
     * @return bool
     */
    public function existError()
    {
        return !!$this->errorMessage;
    }


    /**
     * Присваивание данных ответа
     *
     * @param array|string|int $data
     */
    public function setData($data)
    {
        $this->data = $data;
        $this->setSuccessResult();
    }


    /**
     * Присваивание успешного выполнения запроса
     */
    public function setSuccessResult()
    {
        $this->successResult = true;
    }


    /**
     * Проверка наличия данных ответа
     *
     * @return bool
     */
    public function existData()
    {
        return !empty($this->data);
    }


    /**
     * Присваивание кода ответа
     *
     * @param int|bool $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->status = $statusCode;
    }


    /**
     * Получение полного ответа сервера
     *
     * @return Response
     */
    public function getResponse()
    {
        $serializeJson = ['status' => $this->status];

        if ($this->existError() || !$this->successResult) {
            $serializeJson['message'] = $this->errorMessage ? $this->errorMessage : null;
        } else {
            $serializeJson['data'] = ($this->data || is_array($this->data)) ? $this->data : null;
        }

        $jsonContent = $this->serializer->serialize($serializeJson, 'json');

        return new Response($jsonContent, $this->status, $this->headers);
    }
}
