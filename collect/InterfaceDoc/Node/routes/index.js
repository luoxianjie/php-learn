var express = require('express');
var router = express.Router();
var config = require('../config');

/* GET home page. */
router.get('/', function(req, res, next) {
    const fs = require('fs');
    const path = require('path');
    const marked = require('marked');
    const browserSync = require('browser-sync');

    //接收需要转换的文件路径
    const target = path.join(__dirname, process.argv[2] || '../../doc.md');
    //最终生成的html文件的位置
    const filename = target.replace(path.extname(target), '.html');
    //获取html文件名
    const docpath = path.basename(filename);
    //获取模板文件
    const template = fs.readFileSync('views/index.ejs',{encoding:"utf-8"});

    // php处理请求地址
    const php_host = config.php_host || 'http://project.com';
    // js前台文件同步地址
    const js_host  = config.js_host || 'http://192.168.13.55';

    //通过browser-sync创建一个文件服务器
    browserSync({
        notify: false,
        server: path.dirname(target),//网站根目录
        index: docpath             //默认文档
    });

    //监视文件变化，可以理解为当文件发生变化（需要保存才能触发文件变化)，interval时间间隔后调用回调函数
    fs.watchFile(target, { interval: 200 }, function (cur, prev){
        // 判断文件的最后修改时间是否改变，减少不必要的转换
        if (cur.mtime === prev.mtime) {
            return false;
        }
        reload();
    });

    function reload() {
        fs.readFile(target, 'utf8', function (err, content){
            if (err) {
                throw err;
            }
            var html = marked(content);

                html = template.replace('{{{content}}}', html)
                    .replace(/{{{php_host}}}/g,php_host).replace(/{{{js_host}}}/g,js_host);

                fs.writeFile(filename, html, function (err){
                    if (err) {
                        throw err;
                    }
                    browserSync.reload(docpath);
                    console.log('updated@' + new Date());
                })

        })
    }

    reload();
});

module.exports = router;
