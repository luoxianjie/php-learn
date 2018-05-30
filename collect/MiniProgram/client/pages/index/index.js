
const app = getApp()

Page({
  data: {
      city:'北京市',
      date:'2018-01-01',
      weather:{},
      imageSrc:{
        '晴':'../../images/1.png',
        '阵雨':'../../images/2.png',
        '多云':'../../images/3.png',
        '雷阵雨':'../../images/4.png'
      }
  },
  onLoad: function () {
    this.setData({
      date:this.getToday()
    })

    this.getWeather();
  },
  onPullDownRefresh:function(){
    wx.showNavigationBarLoading();
    
    this.getWeather();
    
    // 隐藏导航栏加载框  
    wx.hideNavigationBarLoading();
    // 停止下拉动作  
    wx.stopPullDownRefresh(); 
  },
  getWeather:function(){

    var that = this;

    // 获取地理位置
    wx.getLocation({
      success: function (res) {
        var location = { latitude: res.latitude, longitude: res.longitude };
        wx.request({
          url: 'http://project.com/index.php',
          method: 'POST',
          data: location,
          header: {
            'content-type':'application/x-www-form-urlencoded',
            'Accept': 'application/json'
          },
          success: function (res) {
            if (res.data.status == 200) {
              that.setData({
                city: res.data.city,
                weather: res.data.data.forecast
              });
            } else {
              wx.showModal({
                title: '提示',
                content: res.data.message + '请下拉刷新'
              })
            }
          }
        })
      },
    })
  },
  getToday:function(){
    var myDate = new Date();
    var result = myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' + myDate.getDate();
    return result;
  }
})
