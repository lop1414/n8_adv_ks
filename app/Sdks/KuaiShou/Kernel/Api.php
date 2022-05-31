<?php

namespace App\Sdks\KuaiShou\Kernel;

use App\Sdks\KuaiShou\Config;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Utils;

class Api
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ClientInterface
     */
    protected $client;



    public function __construct(ClientInterface $client = null, Config $config = null)
    {
        $this->client = $client ?: new Client();
        $this->config = $config ?: new Config();
    }


    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * 检查必填参数
     * @param $requiredParams
     * @param $params
     * @throws Exception
     */
    public function checkRequiredParam(array $requiredParams,array $params){
        foreach ($requiredParams as  $requiredParam){
            if(!isset($params[$requiredParam])){
                throw new Exception('缺少必填参数: '.$requiredParam,1);
            }
        }
    }

    /**
     * 检查是否为文件对象
     * @param $file
     * @throws Exception
     */
    public function checkFileObject($file){
        if (!($file instanceof \SplFileInfo)) {
            throw new \Exception('file 必须是SplFileInfo的实例',1);
        }
    }


    /**
     * 生成表单请求内容
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function makeMultipartContents(array $params): array
    {
        $multipartContents = [];
        foreach ($params as $formParamName => $formParamValue) {
            if($formParamName == 'file'){
                $this->checkFileObject($formParamValue);
                $multipartContents[] = [
                    'name'      => $formParamName,
                    'contents'  => Utils::tryFopen($formParamValue->getRealPath(),'rb'),
                    'filename'  => $formParamValue->getFilename()
                ];
                continue;
            }

            $multipartContents[] = [
                'name'      => $formParamName,
                'contents'  => $formParamValue
            ];
        }
        return $multipartContents;
    }



    /**
     * @param $response
     * @return mixed
     * @throws Exception
     */
    public function handleResponse($response){
        $statusCode = $response->getStatusCode();

        if ($statusCode != 200 ) {
            throw new Exception('HTTP 请求异常',$statusCode);
        }

        $responseData = json_decode($response->getBody()->getContents(),true);
        if (!isset($responseData['code'])) {
            throw new Exception("api response has not code field.");
        }

        if ($responseData['code'] != 0) {
            throw new Exception('api error message : '.$responseData['message'],$responseData['code']);
        }

        return $responseData['data'];
    }
}
