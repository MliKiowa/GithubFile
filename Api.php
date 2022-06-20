<?php
if (!defined('__TYPECHO_ROOT_DIR__')) {
    exit;
}
class GithubFile_Api implements Typecho_Plugin_Interface {
    private $_Username;
    private $_Password;
    private $_Api;
    public static function SetUser(string $Username, string $Password) {
        $this->_Username = $Username;
        $this->_Password = $Password;
    }
    public static function SetApi(string $Api) {
        $this->_Api = $Api;
    }
    public static function SendApi(string $Url = '', string $Data = '', string $Menthod = 'GET') {
        $http = Typecho_Http_Client::get();
        $http->setMethod($Method);
        $http->setHeader('User-Agent', 'GithubFile PluginApi2 ');
        $http->setHeader('Authorization', 'Basic ' . base64_encode($this->_Username . ':' . $this->_Password));
        $http->setData($Data);
        $result = $http->send($this->Api . $Url);
        return $result;
    }
    public static function UserInfo(string $Username) {
        return json_decode(self::SendApi('/users/' . $Username));
    }
    public static function UserLogin() {
        return json_decode(self::SendApi('/user'));
    }
    public static function ReposAll(string $Username) {
        return self::SendApi('/users/' . $Username . '/repos');
    }
    public static function ReposInfo(string $Username, string $ReposName) {
        return json_decode(self::SendApi('/repos/' . $Username . '/' . $ReposName));
    }
    public static function ReposPath(string $Username, string $ReposName, string $Path) {
        return json_decode(self::SendApi('/repos/' . $Username . '/' . $ReposName . '/contents' . $Path));
    }
    public static function FilesUpload(string $Username, string $Repos, string $Path, string $Files) {
        $data = array(
            'message' => 'Upload-GithubFile',
            'content' => base64_encode($files)
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data) , 'PUT'));
        return !isset($json['message']);
    }
    public static function FilesUpdata(string $Username, string $Repos, string $Path, string $Files, string $Sha) {
        $data = array(
            'message' => 'Updata-GithubFile',
            'content' => base64_encode($files) ,
            'sha' => $Sha
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data) , 'PUT'));
        return !isset($json['message']);
    }
    public static function FilesDel(string $Username, string $Repos, string $Path, string $Sha) {
        $data = array(
            'message' => 'Del-GithubFile',
            'sha' => $Sha
        );
        $json = (array)json_decode(self::SendApi('/repos/' . $Username . '/' . $Repos . '/contents' . $Path, json_encode($data) , 'DELETE'));
        return !isset($json['message']);
    }
    public static function GetSha(string $Username, string $Repos, string $Path) {
        $json = (array)self::ReposPath($Username, $Repos, $Path);
        return $json['sha'];
    }
}
