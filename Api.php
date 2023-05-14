<?php
namespace TypechoPlugin\GithubFile;

/**
 * 单例模式下的插件封装api部分
 * Class GithubFile_Api
 * @package GithubFile
 */
class Api
{
    private static $instance = null; //唯一实例

    private $mtoken; //私有属性，存储token信息
    private $mApi; //私有属性，存储api链接

    /**
     * 构造函数私有化，防止外部new实例化
     * GithubFile_Api constructor.
     */
    private function __construct() {}

    /**
     * 获取唯一实例的公开方法
     * @return Api|null
     */
    public static function getInstance(): ?Api {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 设置token信息
     * @param string $token
     */
    public function setUser(string $token): void {
        $this->mtoken = $token;
    }

    /**
     * 设置api链接
     * @param string $Api
     */
    public function setApi(string $Api): void {
        $this->mApi = $Api;
    }

    /**
     * 获取用户信息
     * @param string $Username
     * @return string
     */
    public function getUserInfo(string $Username): string {
        return json_decode($this->sendApi(sprintf('/users/%s', $Username)));
    }

    /**
     * 获取用户登录信息
     * @return string
     */
    public function getUserLogin(): string {
        return json_decode($this->sendApi('/user'));
    }

    /**
     * 获取所有仓库信息
     * @param string $Username
     * @return string
     */
    public function getReposAll(string $Username): string {
        return $this->sendApi(sprintf('/users/%s/repos', $Username));
    }

    /**
     * 获取仓库指定路径的信息
     * @param string $Username
     * @param string $ReposName
     * @param string $Path
     * @return mixed
     */
    public function getReposPath(string $Username, string $ReposName, string $Path) {
        $temp = $this->sendApi(sprintf('/repos/%s/%s/contents%s', $Username, $ReposName, $Path));
        return json_decode($temp);
    }

    /**
     * 获取指定仓库的信息
     * @param string $Username
     * @param string $ReposName
     * @return string
     */
    public function getReposInfo(string $Username, string $ReposName): string {
        return json_decode($this->sendApi(sprintf('/repos/%s/%s', $Username, $ReposName)));
    }

    /**
     * 上传指定路径的文件
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @param string $Files
     * @return string
     */
    public function uploadFiles(string $Username, string $Repos, string $Path, string $Files): string {
        $data = array(
            'message' => 'Upload-GithubFile',
            'content' => base64_encode($Files)
        );

        $json = (array)json_decode($this->sendApi(sprintf('/repos/%s/%s/contents%s', $Username, $Repos, $Path), json_encode($data), 'PUT'));
        return !isset($json['message']);
    }

    /**
     * 更新指定路径的文件
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @param string $Files
     * @param string $Sha
     * @return bool
     */
    public function updateFiles(string $Username, string $Repos, string $Path, string $Files, string $Sha): bool {
        $data = array(
            'message' => 'Update-GithubFile',
            'content' => base64_encode($Files),
            'sha' => $Sha
        );

        $json = (array)json_decode($this->sendApi(sprintf('/repos/%s/%s/contents%s', $Username, $Repos, $Path), json_encode($data), 'PUT'));
        return !isset($json['message']);
    }

    /**
     * 删除指定路径的文件
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @param string $Sha
     * @return bool
     */
    public function delFiles(string $Username, string $Repos, string $Path, string $Sha): bool {
        $data = array(
            'message' => 'Del-GithubFile',
            'sha' => $Sha
        );

        $json = (array)json_decode($this->sendApi(sprintf('/repos/%s/%s/contents%s', $Username, $Repos, $Path), json_encode($data), 'DELETE'));
        return !isset($json['message']);
    }

    /**
     * 获取指定路径的sha值
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @return string
     */
    public function getSha(string $Username, string $Repos, string $Path): string {
        $json = (array)$this->getReposPath($Username, $Repos, $Path);
        return $json['sha'];
    }

    /**
     * 发送请求的私有方法
     * @param string $url 请求链接
     * @param string $data 请求数据
     * @param string $method 请求方法
     * @return mixed
     */
    private function sendApi(string $url = '', string $data = '', string $method = 'GET') {
        $http = \Typecho\Http\Client::get();
        $http->setHeader('User-Agent', 'GithubFile PluginApi2 ');
        $http->setHeader('Authorization', 'token ' . $this->mtoken);
        $http->setTimeout(40);
        $http->setData($data);
        $http->setMethod($method);
        $http->send($this->mApi . $url);
        return (string)$http->getResponseBody();
    }

    /**
     * 构造函数私有化，防止克隆对象
     */
    private function __clone() {}

}
