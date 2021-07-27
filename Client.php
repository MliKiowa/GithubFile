<?php
/**
 * Http客户端
 *
 * @author qining
 * @category typecho
 * @package Http
 */
 require_once "Client/Exception.php";
 require_once "Client/Adapter.php";
 require_once "Client/Adapter/Curl.php";
 require_once "Client/Adapter/Socket.php";
class _Http_Client
{
    /** HTTP方法 */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_TRACE = 'TRACE';
    const METHOD_PUT   =   'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_HEAD = 'HEAD';
    /** 定义行结束符 */
    const EOL = "\r\n";

    /**
     * 获取可用的连接
     *
     * @access public
     * @return _Http_Client_Adapter
     */
    public static function get()
    {
        $adapters = func_get_args();

        if (empty($adapters)) {
            $adapters = array();
            $adapterFiles = glob(dirname(__FILE__) . '/Client/Adapter/*.php');
            foreach ($adapterFiles as $file) {
                $adapters[] = substr(basename($file), 0, -4);
            }
        }

        foreach ($adapters as $adapter) {
            $adapterName = '_Http_Client_Adapter_' . $adapter;        
            if (call_user_func(array($adapterName, 'isAvailable'))) {
                return new $adapterName();
            }
        }
        return false;
    }
}
