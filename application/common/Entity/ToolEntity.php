<?php
namespace app\common\Entity;

class ToolEntity{
    /**
     * 导出excel(csv)
     * @data 导出数据
     * @headlist 第一行,列名
     * @fileName 输出Excel文件名
     */
    static function csv_export($data = array(), $headlist = array(), $fileName) {

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$fileName.'.csv"');
        header('Cache-Control: max-age=0');

        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');

        //输出Excel列名信息
        foreach ($headlist as $key => $value) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $headlist[$key] = iconv('utf-8', 'gbk', $value);
        }

        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $headlist);

        //计数器
        $num = 0;

        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 5000;

        //逐行取出数据，不浪费内存
        $count = count($data);
        for ($i = 0; $i < $count; $i++) {

            $num++;

            //刷新一下输出buffer，防止由于数据过多造成问题
            if ($limit == $num) {
                ob_flush();
                flush();
                $num = 0;
            }

            $row = $data[$i];
            foreach ($row as $key => $value) {
                $row[$key] = iconv('utf-8', 'gbk', $value);
            }

            fputcsv($fp, $row);
        }
    }


    /**
     * @param $key
     * @param int $device_num
     * @return int
     * 根据key获取随机设备id,目前支持16台设备散列
     */
    static function getDeviceId($key,$device_num=1){
        $str = substr(md5($key),-1);

        switch ($str){
            case 'a':
                return 11%$device_num;
                break;
            case 'b':
                return 12%$device_num;
                break;
            case 'c':
                return 13%$device_num;
                break;
            case 'd':
                return 14%$device_num;
                break;
            case 'e':
                return 15%$device_num;
                break;
            case 'f':
                return 16%$device_num;
                break;
            default:
                return $str%$device_num+1;
                break;
        }
    }


    /**
     * @param $base_img
     * @return string
     * base64 图片上传
     */
    static function base64ImgSave($base_img){
        if(strpos($base_img, 'base64') ===false) return $base_img;
        $base_img = substr($base_img,strpos($base_img, ',')+1);
        $base_img = str_replace(' ','+',$base_img);

        $path = "/tmp/uploads/".date("Ymd")."/";
        $real_path = env('root_path').'public'.$path;
        if (!is_dir($real_path)) mkdir($real_path,0777,true);

        $fileName = md5(uniqid()).".jpg";
        $filepath = $real_path.$fileName;

        file_put_contents($filepath, base64_decode($base_img));

        return $path.$fileName;
    }
}