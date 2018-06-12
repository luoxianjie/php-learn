### 财务相关接口

#### 余额列表

接口地址: /finance/balance

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
order|数据排序（默认 ASC 正序，可选 DESC 倒序）
type|筛选类型，如0:全部,1:充值 2:退款 3:余额支付4:提现
begintime|起始时间  可选
endtime|结束时间  可选

<div class='btn response-btn' data-method='GET' data-url='/finance/balance'>输入参数生成响应</div>

<div class='response-area'></div>


#### 提现列表

接口地址: /finance/earn

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
order|数据排序（默认 ASC 正序，可选 DESC 倒序）

<div class='btn response-btn' data-method='GET' data-url='/finance/earn'>输入参数生成响应</div>

<div class='response-area'></div>


#### 提现操作

接口地址: /finance/cashout

请求方式: POST

接口参数: 

名称 | 说明
:---:|:---:
realname|收款人
bnum|账号
province|开户行省
city|开户行城市
bank|开户行详情
money|提现金额
note|备注

<div class='btn response-btn' data-method='POST' data-url='/finance/cashout'>输入参数生成响应</div>

<div class='response-area'></div>


#### 获取省

接口地址: /finance/province

请求方式: GET

接口参数: 无


<div class='btn response-btn' data-method='GET' data-url='/finance/province'>输入参数生成响应</div>

<div class='response-area'></div>


#### 获取市

接口地址: /finance/city

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
province|省id

<div class='btn response-btn' data-method='GET' data-url='/finance/city'>输入参数生成响应</div>

<div class='response-area'></div>


#### 发票列表

接口地址: /finance/invoice

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
type|发票类型  0:全部，1:增值发票，2:普通电子发票，3:不需要发票
status|发票状态  0:全部，1:未开票，2:待审核，3:待开票，4:已开票，5:作废

<div class='btn response-btn' data-method='GET' data-url='/finance/invoice'>输入参数生成响应</div>

<div class='response-area'></div>


#### 发票详情

接口地址: /finance/invoice_detail

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
id|发票id

<div class='btn response-btn' data-method='GET' data-url='/finance/invoice_detail'>输入参数生成响应</div>

<div class='response-area'></div>


#### 优惠券列表

接口地址: /finance/bonus

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
type|类型 0:全部，1:未使用，2:已使用，3:已过期

<div class='btn response-btn' data-method='GET' data-url='/finance/bonus'>输入参数生成响应</div>

<div class='response-area'></div>


#### 积分列表

接口地址: /finance/score

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
type|类型 0:全部，1:获取记录，2:扣除记录

<div class='btn response-btn' data-method='GET' data-url='/finance/score'>输入参数生成响应</div>

<div class='response-area'></div>


### 默认控制器

#### 首页幻灯片

接口地址: /index/banner

请求方式: GET

接口参数: 无


<div class='btn response-btn' data-method='GET' data-url='/index/banner'>输入参数生成响应</div>

<div class='response-area'></div>


### 订单相关接口

#### 订单列表

接口地址: /order/list

请求方式: GET

接口参数: 无


<div class='btn response-btn' data-method='GET' data-url='/order/list'>输入参数生成响应</div>

<div class='response-area'></div>


#### 删除订单

接口地址: /order/delete

请求方式: GET

接口参数: 无


<div class='btn response-btn' data-method='GET' data-url='/order/delete'>输入参数生成响应</div>

<div class='response-area'></div>


#### 订单详情

接口地址: /order/detail

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
id|订单ID

<div class='btn response-btn' data-method='GET' data-url='/order/detail'>输入参数生成响应</div>

<div class='response-area'></div>


#### 取消订单

接口地址: /order/cancel

请求方式: POST

接口参数: 

名称 | 说明
:---:|:---:
id|integer 订单ID
reason|string  选择的原因
content|string  其他原因

<div class='btn response-btn' data-method='POST' data-url='/order/cancel'>输入参数生成响应</div>

<div class='response-area'></div>


#### 返单列表

接口地址: /order/fandan

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
order|数据排序（默认 ASC 正序，可选 DESC 倒序）

<div class='btn response-btn' data-method='GET' data-url='/order/fandan'>输入参数生成响应</div>

<div class='response-area'></div>


#### 返单详情

接口地址: /order/fandan_detail

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
id|integer	订单id

<div class='btn response-btn' data-method='GET' data-url='/order/fandan_detail'>输入参数生成响应</div>

<div class='response-area'></div>


#### 退款列表

接口地址: /order/refund

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
order|数据排序（默认 ASC 正序，可选 DESC 倒序）

<div class='btn response-btn' data-method='GET' data-url='/order/refund'>输入参数生成响应</div>

<div class='response-area'></div>


#### 订单收货地址

接口地址: /order/address

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
id|订单id

<div class='btn response-btn' data-method='GET' data-url='/order/address'>输入参数生成响应</div>

<div class='response-area'></div>


#### 修改订单收货地址

接口地址: /order/address

请求方式: POST

接口参数: 

名称 | 说明
:---:|:---:
id|订单id
orderman|下单联系人
ordertel|下单联系人手机
recevman|收货人
recevtel|收货人手机
address|收货人地址

<div class='btn response-btn' data-method='POST' data-url='/order/address'>输入参数生成响应</div>

<div class='response-area'></div>


### 账号通道

#### 验证微信code信息

接口地址: /passport/auth

请求方式: POST

接口参数: 无


<div class='btn response-btn' data-method='POST' data-url='/passport/auth'>输入参数生成响应</div>

<div class='response-area'></div>


#### 登陆验证

接口地址: /passport/login

请求方式: POST

接口参数: 无


<div class='btn response-btn' data-method='POST' data-url='/passport/login'>输入参数生成响应</div>

<div class='response-area'></div>


### 客户服务相关接口

#### 投诉列表

接口地址: /service/complaint

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
order|数据排序（默认 ASC 正序，可选 DESC 倒序）

<div class='btn response-btn' data-method='GET' data-url='/service/complaint'>输入参数生成响应</div>

<div class='response-area'></div>


#### 提交投诉

接口地址: /service/complaint

请求方式: POST

接口参数: 

名称 | 说明
:---:|:---:
oid|订单号
subject|投诉主题
content|投诉内容

<div class='btn response-btn' data-method='POST' data-url='/service/complaint'>输入参数生成响应</div>

<div class='response-area'></div>


#### 投诉详情

接口地址: /service/complaint_detail

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
id|投诉id主键

<div class='btn response-btn' data-method='GET' data-url='/service/complaint_detail'>输入参数生成响应</div>

<div class='response-area'></div>


#### 删除投诉

接口地址: /service/complaint_delete

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
id|投诉id主键

<div class='btn response-btn' data-method='GET' data-url='/service/complaint_delete'>输入参数生成响应</div>

<div class='response-area'></div>


#### 生产稿列表

接口地址: /service/pcbfile

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
order|数据排序（默认 ASC 正序，可选 DESC 倒序）

<div class='btn response-btn' data-method='GET' data-url='/service/pcbfile'>输入参数生成响应</div>

<div class='response-area'></div>


#### 申请生产稿

接口地址: /service/pcbfile_apply

请求方式: POST

接口参数: 

名称 | 说明
:---:|:---:
id|生产稿id主键

<div class='btn response-btn' data-method='POST' data-url='/service/pcbfile_apply'>输入参数生成响应</div>

<div class='response-area'></div>


### 设置相关接口

#### 地址列表

接口地址: /setting/address

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
offset|数据偏移量 （默认 0）
limit|数据返回限制条数（默认10，最大100）
order|数据排序（默认 ASC 正序，可选 DESC 倒序）

<div class='btn response-btn' data-method='GET' data-url='/setting/address'>输入参数生成响应</div>

<div class='response-area'></div>


#### 添加地址

接口地址: /setting/address

请求方式: POST

接口参数: 

名称 | 说明
:---:|:---:
orderman|下单人
ordertel|下单人电话
recevman|收货人
recevtel|电话
province|region_id
city|region_id
district|街道地址
extaddress|详细地址
shipping_id|快递
default|设为默认

<div class='btn response-btn' data-method='POST' data-url='/setting/address'>输入参数生成响应</div>

<div class='response-area'></div>


#### 删除地址

接口地址: /setting/address_delete

请求方式: GET

接口参数: 

名称 | 说明
:---:|:---:
id|地址id

<div class='btn response-btn' data-method='GET' data-url='/setting/address_delete'>输入参数生成响应</div>

<div class='response-area'></div>


#### 获取个人资料

接口地址: /setting/profile

请求方式: GET

接口参数: 无


<div class='btn response-btn' data-method='GET' data-url='/setting/profile'>输入参数生成响应</div>

<div class='response-area'></div>


#### 编辑个人资料

接口地址: /setting/profile

请求方式: POST

接口参数: 

名称 | 说明
:---:|:---:
realname|姓名
mobile|手机
telephone|固定电话
qq|qq
company_type|企业类型
job_type|工作性质

<div class='btn response-btn' data-method='POST' data-url='/setting/profile'>输入参数生成响应</div>

<div class='response-area'></div>


