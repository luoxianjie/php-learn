### 环境搭建

Node环境搭建（略），node环境搭建好后，进入Node目录 npm install 安装项目依赖。
配置PHP服务，将项目剪切到www目录下，配置一个虚拟主机，根目录指向本项目根目录。

### 生成markdown文档

在浏览器或者控制台运行index.php文件，将在项目根目录生成一个doc.md文件

### 生成html文件

#### 打开Node目录，修改config.js文件中相应配置

#### 控制台进入Node目录，执行npm start 命令，将在项目根目录生成一个doc.html文件

#### 打开浏览器输入上一步browsersync服务创建的local服务器地址，如http://192.168.13.55:3000

### 生成响应结果

点击接口参数下方`输入参数生成响应`按钮，在出现的输入框中输入参数，点击下方`点击获取响应结果`按钮
异步获取响应结果，修改参数后再次点击按钮，可以获取最新响应结果

### 配置说明

```
/Node/config.js 中 js_host 为browsersync服务创建的本地服务器ip地址，如192.168.13.55
/Node/config.js 中 php_host 对应PHP处理程序服务器地址
index.php 中 __INTERFACE_DIR__ 为接口所在目录路径
index.php 中 __MARKDOWN__       为markdown文件路径
index.php 中 __INTERFACE_URI__ 为要处理的接口地址
```








