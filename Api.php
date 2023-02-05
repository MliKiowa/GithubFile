<?php
namespace TypechoPlugin\GithubFile;
/**
 *插件封装api部分
 * Class类名称(Api)
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.4.0
 * @since 1.0.0
 */
class Api
{
    private static $instances = [];
    private  $mtoken;
    private  $mapi;
    private function __construct(){}
    public function __wakeup(){}
    private function __clone(){}
    public static function getInstance(): Singleton
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }
        return self::$instances[$cls];
    }
    public function setUser(string $token): void
    {
        $this->mtoken = $token;
    }
    public function setApi(string $Api): void
    {
        $this->mApi = $Api;
    }   
    public function getUserInfo(string $Username): string
    {
        return json_decode(self::SendApi('/users/' . $Username));
    }
    public function sendApi(string $url = '', string $data = '', string $method = 'GET')
    {
        $http = \Typecho\Http\Client::get();       
        $http->setHeader('User-Agent', 'GithubFile PluginApi2 ');
        $http->setHeader('Authorization', 'token '  . $this->mtoken);
        $http->setTimeout(40);
        $http->setData($data);
        $http->setMethod($method);
        $http->send($this->mapi . $url);
        return (string)$http->getResponseBody();
    }   
    public function getUserLogin(): string
    {
        return json_decode(self::SendApi('/user'));
    }    
    public function getReposAll(string $Username): string
    {
        return self::SendApi('/users/' . $Username . '/repos');
    }
    public function getReposInfo(string $Username, string $ReposName): string
    {
        return json_decode(self::SendApi('/repos/' . $Username . '/' . $ReposName));
    }
    public function uploadFiles(string $Username, string $Repos, string $Path, string $Files): string
    {
        $data = array(
            'message' => 'Upload-GithubFile',
            'content' => base64_encode($Files)
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data), 'PUT'));
        return !isset($json['message']);
    }
    public function updateFiles(string $Username, string $Repos, string $Path, string $Files, string $Sha): bool
    {
        $data = array(
            'message' => 'Update-GithubFile',
            'content' => base64_encode($Files),
            'sha' => $Sha
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data), 'PUT'));
        return !isset($json['message']);
    }

    public function delFiles(string $Username, string $Repos, string $Path, string $Sha): bool
    {
        $data = array(
            'message' => 'Del-GithubFile',
            'sha' => $Sha
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data), 'DELETE'));
        return !isset($json['message']);
    }
    public function getSha(string $Username, string $Repos, string $Path): string
    {
        $json = (array)$this->getReposPath($Username, $Repos, $Path);
        return $json['sha'];
    }
    public function getReposPath(string $Username, string $ReposName, string $Path)
    {
        $temp = self::SendApi('/repos/' . $Username . '/' . $ReposName . '/contents' . $Path);      
        return json_decode($temp);
    }
}
