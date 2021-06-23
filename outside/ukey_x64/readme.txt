1、创建文件夹并ukey驱动放到目录下
    mkdir -p /work/lib/usbkey/
    拷贝文件 cp ./lib/libgm3000.1.0.so /work/lib/usbkey/
    赋予执行权限 chmod -R +x /work/lib/usbkey/
2、src为源码目录
    cd ./src
    执行make进行编译，成功后在当前目录下生成一个libusbkey.so
3、usbkey.py为python测试代码