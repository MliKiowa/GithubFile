<?php
class ImageCompressor {
    protected $quality;      // 压缩质量 (0 - 100)，默认为 75%
    protected $sourcePath;   // 源图片路径
    protected $destinationPath; // 压缩后保存路径
    protected $compressedData;  // 压缩后的图片数据

    /**
     * 构造函数
     *
     * @param string $sourcePath        源图片路径
     * @param string $destinationPath   目标图片保存路径
     * @param int    $quality           图像压缩质量 (0 - 100)
     */
    public function __construct(string $sourcePath, string $destinationPath, int $quality = 75) {
        $this->sourcePath = $sourcePath;
        $this->destinationPath = $destinationPath;
        $this->quality = $quality;
    }

    /**
     * 检查 GD 库是否可用
     *
     * @return bool      如果 GD 库不存在或没有加载则返回 false
     */
    public static function checkGD(): bool {
        return extension_loaded('gd');
    }

    /**
     * 压缩图像并返回是否成功
     *
     * @return bool      成功时返回 true，失败时返回 false
     */
    public function compress(): bool {
        if (!is_file($this->sourcePath) || !is_readable($this->sourcePath)) {  // 检查源文件是否存在或可读
            return false;
        }
        
        $imageData = getimagesize($this->sourcePath);  // 获取源图片信息
        
        switch ($imageData[2]) {  // 根据图像类型创建新的 Gd 资源对象
            case IMAGETYPE_JPEG:
                $imgSrc = imagecreatefromjpeg($this->sourcePath);
                break;
            
            case IMAGETYPE_PNG:
                $imgSrc = imagecreatefrompng($this->sourcePath);
                break;

            default:
                return false;
        }        

        // 计算目标宽度和高度
        $width = imagesx($imgSrc);
        $height = imagesy($imgSrc);
        $newWidth = 800;  // 新目标宽度最大为 800

        if ($width <= $newWidth) {
            $newHeight = $height;
        } else {
            $newHeight = floor($height * ($newWidth / $width));
        }

        // 创建一个新的 Gd 缩放资源 
        $imgDst = imagecreatetruecolor($newWidth, $newHeight);

        // 拷贝原始图像到缩放图像中，并以给定的大小填充新图像
        imagecopyresampled(
            $imgDst,   // 目标图像
            $imgSrc,   // 源图像
            0,         // 目标图像的左上角 X 坐标
            0,         // 目标图像的左上角 Y 坐标
            0,         // 源图像的左上角 X 坐标
            0,         // 源图像的左上角 Y 坐标
            $newWidth, // 目标图像的宽度
            $newHeight,// 目标图像的高度
            $width,    // 源图像的宽度
            $height    // 源图像的高度
        );
        
        // 压缩并保存新的图像文件
        switch ($imageData[2]) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($imgDst, $this->destinationPath, $this->quality);
                break;
            
            case IMAGETYPE_PNG:
                $result = imagepng($imgDst, $this->destinationPath, ceil(9 - ($this->quality / 100 * 9)));
                break;

            default:
                $result = false;
        }

        // 如果成功压缩，则把图片数据保存到属性里面
        if ($result) {
            $this->compressedData = file_get_contents($this->destinationPath);
        }

        // 销毁资源
        imagedestroy($imgSrc);
        imagedestroy($imgDst);

        return $result;
    }

    /**
     * 获取成功压缩后的图片二进制数据
     *
     * @return string|false    成功时返回二进制数据，失败时返回 false。
     */
    public function getCompressedImageData() {
        return $this->compressedData ?: false;
    }
}
