var util = require('../../utils/util.js')

//index.js
//获取应用实例
var app = getApp()
var canvas_instance=null;
Page({
  data: {
    //motto: 'Hello World',
    //userInfo: {}

    /**
    模式  值 说明
    缩放  scaleToFill 不保持纵横比缩放图片，使图片的宽高完全拉伸至填满 image 元素
    缩放  aspectFit 保持纵横比缩放图片，使图片的长边能完全显示出来。也就是说，可以完整地将图片显示出来。
    缩放  aspectFill  保持纵横比缩放图片，只保证图片的短边能完全显示出来。也就是说，图片通常只在水平或垂直方向是完整的，另一个方向将会发生截取。
    缩放  widthFix  宽度不变，高度自动变化，保持原图宽高比不变
    裁剪  top 不缩放图片，只显示图片的顶部区域
    裁剪  bottom  不缩放图片，只显示图片的底部区域
    裁剪  center  不缩放图片，只显示图片的中间区域
    裁剪  left  不缩放图片，只显示图片的左边区域
    裁剪  right 不缩放图片，只显示图片的右边区域
    裁剪  top left  不缩放图片，只显示图片的左上边区域
    裁剪  top right 不缩放图片，只显示图片的右上边区域
    裁剪  bottom left 不缩放图片，只显示图片的左下边区域
    裁剪  bottom right  不缩放图片，只显示图片的右下边区域
     */
    imageModeList:[
      {value:"scaleToFill",name:"拉伸至完全显示"},
      {value:"aspectFit",name:"按长边缩放"},
      {value:"aspectFill",name:"按短边缩放"},
      {value:"widthFix",name:"按需压扁"},
      {value:"top",name:"显示顶部区域"},
      {value:"bottom",name:"显示底部区域"},
      {value:"center",name:"显示中间区域"},
      {value:"left",name:"显示左边区域"},
      {value:"right",name:"显示右边区域"},
      {value:"top left",name:"显示左上边区域"},
      {value:"top right",name:"显示右上边区域"},
      {value:"bottom left",name:"显示左下边区域"},
      {value:"bottom right",name:"显示右下边区域"}
    ],
    imageModeIndex:1,

    word_image_src:'',
    word_image_mode:'aspectFit',
    word_image_height:0,
    word_image_width:0,
    word_title:'',
    word_content:'',
    word_signature:'',
    word_image_generated:'',
  },
  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  tapToSelectImage:function(){
    var that=this;
    wx.chooseImage({
      count: 1, // 默认9
      sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
      sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
      success: function (res) {
        // 返回选定照片的本地文件路径列表，tempFilePath可以作为img标签的src属性显示图片
        var tempFilePaths = res.tempFilePaths
        var data = that.data;
        data['word_image_src'] = tempFilePaths[0];
        that.setData(data);

        if(data['word_image_src']){
          wx.getImageInfo({
            src:data['word_image_src'],
            success: function (res) {
              console.log(res.width)
              console.log(res.height)
              var data = that.data;
              data['word_image_height']=res.height;
              data['word_image_width']=res.width;
              that.setData(data);
            }
          })
        }
      }
    })
  },
  bindTitleKeyInput: function (e) {
    var data=this.data;
    data['word_title']=e.detail.value;
    this.setData(data);
  },
  bindContentKeyInput: function (e) {
    var data = this.data;
    data['word_content'] = e.detail.value;
    this.setData(data);
  },
  bindSignatureKeyInput: function(e){
    var data = this.data;
    data['word_signature'] = e.detail.value;
    this.setData(data);
  },
  bindTapOnMake:function(e){
    var that=this;
    var data=this.data;
    console.log("MAKE: ",data);

    /*
    canvas_instance.setStrokeStyle("#00ff00")
    canvas_instance.setLineWidth(5)
    canvas_instance.rect(0, 0, 200, 200)
    canvas_instance.stroke()
    canvas_instance.setStrokeStyle("#ff0000")
    canvas_instance.setLineWidth(2)
    canvas_instance.moveTo(160, 100)
    canvas_instance.arc(100, 100, 60, 0, 2 * Math.PI, true)
    canvas_instance.moveTo(140, 100)
    canvas_instance.arc(100, 100, 40, 0, Math.PI, false)
    canvas_instance.moveTo(85, 80)
    canvas_instance.arc(80, 80, 5, 0, 2 * Math.PI, true)
    canvas_instance.moveTo(125, 80)
    canvas_instance.arc(120, 80, 5, 0, 2 * Math.PI, true)
    canvas_instance.stroke()
    */

    var x=0;var y=0;
    if(data['word_image_src']){
      x=data['word_image_width'];
      y=data['word_image_height'];
      console.log(x,y)
      if(x==0 || y==0){
        x=0;
        y=0;
      }
      else if(x>y){
        // x:y=[300]:?
        y=300.0*y/x;
        x=300;
      }else{
        // x:y=?:[100]
        x=100.0*x/y;
        y=100;
      }
      canvas_instance.drawImage(data['word_image_src'],0,0,x,y);
      console.log(data['word_image_src'],0,0,x,y)
    }
    var the_text="";
    the_text+=data['word_title']?(data['word_title']+"\n\n"):"";
    the_text+=data['word_content'];
    the_text+=data['word_signature']?('\n\n'+data['word_signature']):"";
    the_text+="\n\nPowered by Verbum";

    console.log(the_text);

    canvas_instance.setTextAlign('center');
    canvas_instance.setFontSize(20)

    var rows=the_text.split("\n");
    for(var row_index=0;row_index<rows.length;row_index++){
      y+=40;
      canvas_instance.fillText(rows[row_index], 150, y);
    }
    
    canvas_instance.draw();

    wx.canvasToTempFilePath({
      canvasId: 'TheCanvas',
      success: function(res) {
        console.log(res.tempFilePath)
        data=that.data;
        data['word_image_generated']=res.tempFilePath;
        that.setData(data);
      } 
    }) 
  },
  bindImageModePickerChange:function(e){
    var data = this.data;
    data['imageModeIndex'] = e.detail.value;
    data['word_image_mode'] = data.imageModeList[e.detail.value]['value'];
    this.setData(data);
  },
  bindShareImage:function(e){
    console.log('share')
  },
  bindSaveImage:function(e){
    var share_img=this.data['word_image_generated'];
    wx.saveImageToPhotosAlbum({
      filePath:share_img,
      success:function(res) {
        console.log('done save')
      }
    })
  },
  onLoad: function () {
    console.log('onLoad')
    var that = this
    //调用应用实例的方法获取全局数据
    // app.getUserInfo(function(userInfo){
    //   //更新数据
    //   that.setData({
    //     userInfo:userInfo
    //   })
    // })
  },
  canvasIdErrorCallback: function (e) {
    console.error(e.detail.errMsg)
  },
  onReady: function (e) {
    canvas_instance=wx.createCanvasContext('TheCanvas')
  },
  onShareAppMessage: function (res) {
    if (res.from === 'button') {
      // 来自页面内转发按钮
      console.log(res.target)
    }
    var share_img=this.data['word_image_generated'];
    return {
      //title: '虚无之语',
      //path: '/page/user?id=123',
      imageUrl:share_img,
      success: function(res) {
        // 转发成功
        console.log('done share')
      },
      fail: function(res) {
        // 转发失败
        console.log('failed share')
      }
    }
  }
})
