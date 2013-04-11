
<?php

//去掉开头结尾的双引号
function remove_quote_1(&$str) {
    if (preg_match("/^\"/",$str)){
        $str = substr($str, 1, strlen($str) - 1);
    }
    //判断字符串是否以'"'结束
    if (preg_match("/\"$/",$str)){
        $str = substr($str, 0, strlen($str) - 1);;
    }
    return $str;
}
//
//去掉开头结尾的单引号
function remove_quote_2(&$str) {
    if (preg_match("/^\'/",$str)){
        $str = substr($str, 1, strlen($str) - 1);
    }
    //判断字符串是否以'"'结束
    if (preg_match("/\'$/",$str)){
        $str = substr($str, 0, strlen($str) - 1);;
    }
    return $str;
}

//获取所有需要的链接
function get_links($myfile){

    //读取文件内容，放入字符串中
    $file_handle = fopen($myfile, "r");
    $txt = '';
    while (!feof($file_handle)) {
        $line = fgets($file_handle);
        $txt .= $line;
    }
    fclose($file_handle);


    //echo "<br/>获取所有的href 后面的内容<br/>";
    $matches = array();
    preg_match_all ("/\ href=\'(.+?)\'/", $txt, $matches);
    $links = $matches[1];

    preg_match_all ("/\ href=\"(.+?)\"/", $txt, $matches);
    $links = array_merge($matches[1],$links);

    //echo "<br/>获取所有的src后面的内容<br/>";
    $matches = array();
    preg_match_all ("/\ src=\'(.+?)\'/", $txt, $matches);
    $src = $matches[1];

    preg_match_all ("/\ src=\"(.+?)\"/", $txt, $matches);
    $src = array_merge($matches[1],$src);

    //echo "<br/>已获取所有的css文件中url后的内容，在\$src 中<br/>";
    $matches = array();
    preg_match_all ("/url\((.+?)\)/", $txt, $matches);
    $css_url = $matches[1];

    //去掉双引号
    foreach($css_url as $key=>$var){
    //去掉双引号
        $css_url[$key] = remove_quote_1($var);
    //去掉单引号
        $css_url[$key] = remove_quote_2($var);
    }

    //合并得到的url地址
    $all_links = array_merge($links,$src,$css_url);

    //去掉javascript的内容
    foreach($all_links as $key=>$var){
        if(preg_match("/^javascript:/",$var)){
            unset($all_links[$key]);
        }
    }

    //返回
    return $all_links;
}
//
//获取文件后缀名
function  extend_1( $file_name )
{
    $retval = "" ;
    $pt = strrpos ( $file_name ,  "." );
    if  ( $pt )  $retval = substr ( $file_name ,  $pt +1,  strlen ( $file_name ) -  $pt );
    return  ( $retval );
}

//递归获取目录下面的所有文件并输出
function scanfiles($dir){
    if ( ! is_dir($dir) ) return array();
    $dir  = rtrim(str_replace('\\','/',$dir),'/').'/';
    $dirs = array($dir);
    $rt   = array();
    do{
        $dir = array_pop($dirs);
        $tmp = scandir($dir);
        foreach ( $tmp as $f ){
            if ( $f == '.' || $f == '..') continue;
            $path = $dir . $f;
            if ( is_dir($path) ) {
                array_push($dirs,$path.'/');
            } else if(is_file($path)) {
                $rt[] = $path;
            }
        }
    } while ( $dirs );
    return $rt;
}



//获取所有的文件,第一个参数为文件夹名,第二个参数为需要找的文件的后缀名
function get_files($dir,$houzhui){

    //2、循环的读取目录下的所有文件,放入数组
    $arr = scanfiles($dir);

    foreach($arr as $key=>$var){

        //如果文件后缀名不符合条件，则删除
        if(!in_array(extend_1($var),$houzhui)){

            //4、进行处理, 加入数组中
            unset($arr[$key]);
        }
    }
    return $arr;
}

?>
