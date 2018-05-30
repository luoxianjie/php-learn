// pages/happy/happy.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
      hasmore:false,
      message:'success',
      data:{},
      next:{
        max_behot_time:''
      }
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
      this.initData();
  },


  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
    wx.showNavigationBarLoading();
    this.pushData();
    // 隐藏导航栏加载框  
    wx.hideNavigationBarLoading();
    // 停止下拉动作  
    wx.stopPullDownRefresh(); 
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    this.getData();
  },
  publishTime:function(time) {
      var date = new Date();
      var now = Math.ceil(date.getTime()/1000);


      //一分钟前
      var lastMinute = now - 60;
      //一小时前
      var lastHour = now - 60*60;
      //一天前
      var lastDay = now - 60*60*24;
      //一月前
      var lastMonth = now - 60*60*24*30;
      //一年前
      var lastYear  = now - 60*60*24*365;

      if((lastYear - time)>0)
      {
          return Math.ceil((lastYear - time)/3600/24/365)+"年前";
      }
      if((lastMonth - time)>0){
          return Math.ceil((lastMonth - time)/3600/24/30)+"月前";
      }
      if((lastDay - time)>0){
          return Math.ceil((lastDay - time)/3600/24)+"天前";
      }
      if((lastHour - time)>0){
          return Math.ceil((lastHour - time)/3600)+"小时前";
      }
      if((lastMinute - time)>0){
          return Math.ceil((lastMinute - time)/60)+"分钟前";
      }
      if(lastMinute < time){
          return "刚刚";
      }
  },
  initData:function(){
    var that = this;
    wx.request({
      url: 'http://project.com/index.php?action=getHappyList',
      method: 'POST',
      data: {},
      header: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      success: function (rsp) {
        if(rsp.data.message == 'success'){
          for (var i = 0; i < rsp.data.data.length; i++) {
            rsp.data.data[i].publishTime = that.publishTime(rsp.data.data[i].behot_time);
          }
          
          that.setData({
            has_more: rsp.data.has_more,
            message: rsp.data.message,
            data: rsp.data.data,
            next: rsp.data.next
          });
        }
      }
    });
  },
  getData:function(){
    var that = this;
    wx.request({
      url: 'http://project.com/index.php?action=getHappyList',
      method: 'POST',
      data: {},
      header: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      success: function (rsp) {
        if (rsp.data.message == 'success') {
          for (var i = 0; i < rsp.data.data.length; i++) {
            rsp.data.data[i].publishTime = that.publishTime(rsp.data.data[i].behot_time);
          }
          for (var i in rsp.data.data) {
            that.data.data.push(rsp.data.data[i]);
          }
          that.setData({
            has_more: rsp.data.has_more,
            message: rsp.data.message,
            data: that.data.data,
            next: rsp.data.next
          });
        }
      }
    });
  },
  pushData:function(){
    var that = this;
    wx.request({
      url: 'http://project.com/index.php?action=getHappyList',
      method: 'POST',
      data: {},
      header: {
        'content-type': 'application/x-www-form-urlencoded',
        'Accept': 'application/json'
      },
      success: function (rsp) {
        if (rsp.data.message == 'success') {
          for (var i = 0; i < rsp.data.data.length; i++) {
            rsp.data.data[i].publishTime = that.publishTime(rsp.data.data[i].behot_time);
          }
          for (var i in that.data.data) {
            rsp.data.data.push(that.data.data[i]);
          }
          that.setData({
            has_more: rsp.data.has_more,
            message: rsp.data.message,
            data: rsp.data.data,
            next: rsp.data.next
          });
        }
      }
    });
  }
})