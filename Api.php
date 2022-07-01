<?php

/**
 *插件封装api部分
 * Class类名称(GithubFile_Api)
 * @package GithubFile
 * @author Mlikiowa<nineto0@163.com>
 * @version 1.1.0
 * @since 1.0.0
 */
class GithubFile_Api implements Typecho_Plugin_Interface
{
    private string $mUsername;
    private string $mPassword;
    private string $mApi;

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @param string $Password
     */
    public function setUser(string $Username, string $Password): void
    {
        $this->mUsername = $Username;
        $this->mPassword = $Password;
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Api
     */
    public function setApi(string $Api): void
    {
        $this->mApi = $Api;
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @return mixed
     */
    public function getUserInfo(string $Username): string
    {
        return json_decode(self::SendApi('/users/' . $Username));
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Url
     * @param string $Data
     * @param string $Method
     * @return mixed
     */
    public function sendApi(string $Url = '', string $Data = '', string $Method = 'GET'): string
    {
        $http = Typecho_Http_Client::get();
        $http->setMethod($Method);
        $http->setHeader('User-Agent', 'GithubFile PluginApi2 ');
        $http->setHeader('Authorization', 'Basic ' . base64_encode($this->mUsername . ':' . $this->mPassword));
        $http->setData($Data);
        return $http->send($this->mApi . $Url);
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @return mixed
     */
    public function getUserLogin(): string
    {
        return json_decode(self::SendApi('/user'));
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @return mixed
     */
    public function getReposAll(string $Username): string
    {
        return self::SendApi('/users/' . $Username . '/repos');
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @param string $ReposName
     * @return mixed
     */
    public function getReposInfo(string $Username, string $ReposName): string
    {
        return json_decode(self::SendApi('/repos/' . $Username . '/' . $ReposName));
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @param string $Files
     * @return string
     */
    public function uploadFiles(string $Username, string $Repos, string $Path, string $Files): string
    {
        $data = array(
            'message' => 'Upload-GithubFile',
            'content' => base64_encode($Files)
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data), 'PUT'));
        return !isset($json['message']);
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @param string $Files
     * @param string $Sha
     * @return bool
     */
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

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @param string $Sha
     * @return bool
     */
    public function delFiles(string $Username, string $Repos, string $Path, string $Sha): bool
    {
        $data = array(
            'message' => 'Del-GithubFile',
            'sha' => $Sha
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data), 'DELETE'));
        return !isset($json['message']);
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @param string $Repos
     * @param string $Path
     * @return mixed
     */
    public function getSha(string $Username, string $Repos, string $Path): string
    {
        $json = (array)$this->getReposPath($Username, $Repos, $Path);
        return $json['sha'];
    }

    /**
     * Notes:
     * User: Mlikiowa<nineto0@163.com>
     * Date: 2022-06-22
     * Time:17:05
     * @param string $Username
     * @param string $ReposName
     * @param string $Path
     * @return mixed
     */
    public function getReposPath(string $Username, string $ReposName, string $Path): string
    {
        return json_decode(self::SendApi('/repos/' . $Username . '/' . $ReposName . '/contents' . $Path));
    }
}