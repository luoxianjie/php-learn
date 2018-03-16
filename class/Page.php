<?php
// +----------------------------------------------------------------------
// | Author: jiexianluo@hotmail.com
// | Date  : 2018/2/24
// | Time  : 17:52
// +----------------------------------------------------------------------

/**
 * 分页处理类
 * <style>
 *   .page{height:25px;line-height: 25px;}
 *   .page a{display: inline-block;border:1px solid #ccc;margin:5px;min-width: 20px;min-height:25px;text-align: center;text-decoration:none;padding:0 3px;}
 *   .page span{display: inline-block;border:1px solid #ccc;margin:5px;min-width: 20px;min-height:25px;text-align: center;padding:0 3px;}
 *   .page span.current{background: #077EE3;color: #fff;border:1px solid #077EE3;}
 *   .page b{font-weight: normal;margin:5px;min-width: 20px;text-align: center}
 *   .page b i{font-style: normal}
 *   .page form{display: inline-block;margin:5px;min-width: 20px;text-align: center}
 *   .page form input[type=text]{border:1px solid #ccc;height:20px;text-indent:5px;}
 *   .page form input[type=button]{height:24px;border-style: solid;border-width: 1px;margin-left:5px;}
 * </style>
 */
class page
{
    public $totalNum;       //总记录数
    public $listRows;       //每页记录数
    public $pageNum;        //总页数
    public $currPage;       //当前页数

    public $url;

    public function __construct($total, $listRows = 10)
    {
        $currPage = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $this->totalNum = $total;
        $this->listRows = $listRows;
        $this->pageNum  = ceil($total / $listRows);
        $this->currPage = min($this->pageNum, max(1, $currPage));
    }


    /**
     * @param $i 页码 若无表示uri不带page参数
     * @return string
     */
    private function getUri($i = '')
    {
        $request_url = $_SERVER['REQUEST_URI'];
        $url_arr = parse_url($request_url);

        $query_str = @$url_arr['query'];
        if(empty($query_str)){
            $uri = 'http://'.$_SERVER['HTTP_HOST'].'?page='.$i;
        }else{
            parse_str($query_str, $query_arr);
            unset($query_arr['page']);
            if(empty($query_arr))
                $uri = 'http://'.$_SERVER['HTTP_HOST'].'?page='.$i;
            else
                $uri = 'http://'.$_SERVER['HTTP_HOST'].'?'.http_build_query($query_arr).'&page='.$i;
        }

        if(empty($i)){
            $uri = substr($uri,0,strripos($uri,'page'));
        }

        return rtrim($uri,'&?');
    }

    private function first()
    {
        if($this->currPage > 1){
            return "<a href='".$this->getUri(1)."'>首页</a>";
        }else{
            return "<span>首页</span>";
        }
    }

    private function last()
    {
        if($this->currPage < $this->pageNum){
            return "<a href='".$this->getUri($this->pageNum)."'>尾页</a>";
        }else{
            return "<span>尾页</span>";
        }
    }

    private function prev()
    {
        if($this->currPage > 1){
            return "<a href='".$this->getUri($this->currPage-1)."'>上一页</a>";
        }
    }

    private function next()
    {
        if($this->currPage < $this->pageNum){
            return "<a href='".$this->getUri($this->currPage+1)."'>下一页</a>";
        }
    }

    /**
     * 中间显示五页
     * @return string
     */
    private function pageList()
    {
        $pageStr = '';
        for($i = $this->currPage-2; $i <= $this->currPage+2; $i++){
            if($i > 0 && $i <= $this->pageNum){
                if($i == $this->currPage){
                    $pageStr .= "<span class='current'>{$i}</span>";
                }else{
                    $pageStr .= "<a href='".$this->getUri($i)."'>{$i}</a>";
                }
            }
        }
        return $pageStr;
    }

    private function record()
    {
        return "<b>共<i>{$this->pageNum}</i>页<i>{$this->totalNum}条记录</i> 当前第<i>{$this->currPage}</i>页</b>";
    }

    private function jump()
    {
        $jumpStr = '<form method="post" action="'.$this->getUri().'">';
        $jumpStr .= '<input type="text" name="page" value="" />';
        $jumpStr .= '<input type="button" value="Go" onclick="this.form.submit()"/>';
        $jumpStr .= '</form>';

        return $jumpStr;
    }

    public function show($i = 1)
    {
        switch($i){
            case 1:
                echo $this->first().$this->prev().$this->pageList().$this->next().$this->last().$this->record().$this->jump();
                break;
            default:
                echo $this->first().$this->prev().$this->pageList().$this->next().$this->last().$this->record().$this->jump();
                break;
        }
    }
}