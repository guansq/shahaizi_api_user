define({ "api": [
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Car&a=getCarInfo",
    "title": "得到system_car信息done 管少秋",
    "name": "getCarInfo",
    "group": "Car",
    "version": "0.0.0",
    "filename": "application/api/controller/Car.php",
    "groupTitle": "Car",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Car&a=getCarInfo"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/comment/commentInfo",
    "title": "获取评论内容（待调试）wxx",
    "name": "commentInfo",
    "group": "Comment",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "authorization-token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "order_id",
            "description": "<p>订单ID</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "order_id",
            "description": "<p>订单ID</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "sp_id",
            "description": "<p>评论人ID</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "sp_name",
            "description": "<p>评价人的姓名</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "dr_id",
            "description": "<p>司机ID</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "dr_name",
            "description": "<p>司机姓名</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "post_time",
            "description": "<p>提交时间</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "limit_ship",
            "description": "<p>发货时效几星</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "attitude",
            "description": "<p>服务态度几星</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "satisfaction",
            "description": "<p>满意度 几星</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>评论文字</p>"
          },
          {
            "group": "Success 200",
            "type": "Int",
            "optional": false,
            "field": "status",
            "description": "<p>0=正常显示，1=不显示给司机</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Comment.php",
    "groupTitle": "Comment",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/comment/commentInfo"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/comment/sendCommentInfo",
    "title": "发送评论内容（待调试）wxx",
    "name": "sendCommentInfo",
    "group": "Comment",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "authorization-token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "order_id",
            "description": "<p>订单ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "limit_ship",
            "description": "<p>发货时效几星</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "attitude",
            "description": "<p>服务态度几星</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "satisfaction",
            "description": "<p>满意度 几星</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>评论文字</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Comment.php",
    "groupTitle": "Comment",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/comment/sendCommentInfo"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=BaseMessage&a=sendInterCaptcha",
    "title": "发送国际验证码done  管少秋",
    "name": "sendInterCaptcha",
    "group": "Common",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>手机号.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "opt",
            "description": "<p>验证码类型 reg=注册 resetpwd=找回密码 login=登陆 bind=绑定手机号.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/BaseMessage.php",
    "groupTitle": "Common",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=BaseMessage&a=sendInterCaptcha"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=BaseMessage&a=sendMailCaptcha",
    "title": "发送邮件验证码done  管少秋",
    "name": "sendMailCaptcha",
    "group": "Common",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mail",
            "description": "<p>邮箱</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "opt",
            "description": "<p>验证码类型 reg=注册 resetpwd=找回密码 login=登陆 bind=绑定手机号.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/BaseMessage.php",
    "groupTitle": "Common",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=BaseMessage&a=sendMailCaptcha"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Config&a=aboutUs",
    "title": "关于我们 h5页面 wxx",
    "name": "aboutUs",
    "group": "Config",
    "version": "0.0.0",
    "filename": "application/api/controller/Config.php",
    "groupTitle": "Config",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Config&a=aboutUs"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Config&a=feedBack",
    "title": "提交意见反馈 wxx",
    "name": "feedBack",
    "group": "Config",
    "version": "0.0.0",
    "filename": "application/api/controller/Config.php",
    "groupTitle": "Config",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Config&a=feedBack"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Config&a=getAllConfig",
    "title": "得到所有配置 wxx",
    "name": "getAllConfig",
    "group": "Config",
    "version": "0.0.0",
    "filename": "application/api/controller/Config.php",
    "groupTitle": "Config",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Config&a=getAllConfig"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=Config&a=getCountryNumber",
    "title": "得到国家区号done  管少秋",
    "name": "getCountryNumber",
    "group": "Config",
    "version": "0.0.0",
    "filename": "application/api/controller/Config.php",
    "groupTitle": "Config",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=Config&a=getCountryNumber"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Config&a=helpCenter",
    "title": "帮助中心 h5页面 wxx",
    "name": "helpCenter",
    "group": "Config",
    "version": "0.0.0",
    "filename": "application/api/controller/Config.php",
    "groupTitle": "Config",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Config&a=helpCenter"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=DriverPack&a=getAllDriver",
    "title": "得到全部司导done  管少秋",
    "name": "getAllDriver",
    "group": "DriverPack",
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": " Http/1.1    200 OK\n{\n\"seller_id\"   : \"11\",//商家端总ID\n\"drv_id\"   : \"11\",//司导ID\n\"drv_code\"   : \"11\",//司导code\n\"head_pic\" : \"http://xxx.jpg\",//司导图片\n\"seller_name\" : \"司导姓名\",\n\"score\" : \"1\",//星级\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=DriverPack&a=getAllDriver"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=DriverPack&a=getDriverDetail",
    "title": "司导详情 (待完成) 管少秋",
    "name": "getDriverDetail",
    "group": "DriverPack",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "seller_id",
            "description": "<p>{String}    商家ID</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "Http/1.1 200 OK\n    {\n    \"status\": 1,\n    \"msg\": \"成功\",\n        \"result\": {\n            \"preson_info\": {    //个人信息\n            \"seller_id\": 17,//商家ID\n            \"drv_id\": 2,//司导ID\n            \"drv_code\": \"20170908-1\",//司导code\n            \"head_pic\": null,//头像\n            \"seller_name\": \"少秋\",//名称\n            \"briefing\": null,//简介\n            \"country\": null,//家乡\n            \"putonghua\": null,//普通话\n            \"language\": null,//外语\n            \"type_info\": \"店主-司导-房东\"//职业\n            }\n        }\n    }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=DriverPack&a=getDriverDetail"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=DriverPack&a=oncePickup",
    "title": "单次接送done 管少秋",
    "name": "oncePickup",
    "group": "DriverPack",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>（rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "car_type_id",
            "description": "<p>车型ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "connect",
            "description": "<p>联系方式</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "drv_code",
            "description": "<p>指定司导</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "is_have_pack",
            "description": "<p>是否有行李0没有行李1有行李</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "total_num",
            "description": "<p>出行总人数</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "adult_num",
            "description": "<p>成人乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "child_num",
            "description": "<p>儿童乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "remark",
            "description": "<p>备注</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "start_address",
            "description": "<p>起始地地址</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "dest_address",
            "description": "<p>目的地地址</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "user_car_time",
            "description": "<p>用车时间</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=DriverPack&a=oncePickup"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=DriverPack&a=privateMake",
    "title": "私人定制done 管少秋",
    "name": "privateMake",
    "group": "DriverPack",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>（rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "car_type_id",
            "description": "<p>车型ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "connect",
            "description": "<p>联系方式</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "drv_code",
            "description": "<p>指定司导</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "is_have_pack",
            "description": "<p>是否有行李0没有行李1有行李</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "total_num",
            "description": "<p>出行总人数</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "adult_num",
            "description": "<p>成人乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "child_num",
            "description": "<p>儿童乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tour_time",
            "description": "<p>出行时间</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "end_address",
            "description": "<p>目的地</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tour_days",
            "description": "<p>游玩天数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tour_person_num",
            "description": "<p>游玩人数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "tour_favorite",
            "description": "<p>出行偏好</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "recommend_diner",
            "description": "<p>推荐餐馆</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "recommend_sleep",
            "description": "<p>推荐住宿</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=DriverPack&a=privateMake"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=DriverPack&a=receiveAirport",
    "title": "接机done 管少秋",
    "name": "receiveAirport",
    "group": "DriverPack",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>（rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "car_type_id",
            "description": "<p>车型ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "connect",
            "description": "<p>联系方式</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "drv_code",
            "description": "<p>指定司导</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "is_have_pack",
            "description": "<p>是否有行李0没有行李1有行李</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "total_num",
            "description": "<p>出行总人数</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "adult_num",
            "description": "<p>成人乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "child_num",
            "description": "<p>儿童乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "remark",
            "description": "<p>备注</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "flt_no",
            "description": "<p>航班号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "airport_name",
            "description": "<p>机场名</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "dest_address",
            "description": "<p>送达地点</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "start_time",
            "description": "<p>出发时间</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=DriverPack&a=receiveAirport"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=DriverPack&a=rentCarByDay",
    "title": "按天包车游done  管少秋",
    "name": "rentCarByDay",
    "group": "DriverPack",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>（rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "car_type_id",
            "description": "<p>车型ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "connect",
            "description": "<p>联系方式</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "drv_code",
            "description": "<p>指定司导</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "is_have_pack",
            "description": "<p>是否有行李0没有行李1有行李</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "total_num",
            "description": "<p>出行总人数</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "adult_num",
            "description": "<p>成人乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "child_num",
            "description": "<p>儿童乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "remark",
            "description": "<p>备注</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "dest_address",
            "description": "<p>目的地地址</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "pack_time",
            "description": "<p>包车日期</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=DriverPack&a=rentCarByDay"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=DriverPack&a=sendAirport",
    "title": "送机done 管少秋",
    "name": "sendAirport",
    "group": "DriverPack",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>（rent_car_by_day按天包车游-receive_airport接机-send_airport送机-once_pickup单次接送-private_person私人定制）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "car_type_id",
            "description": "<p>车型ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "connect",
            "description": "<p>联系方式</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "drv_code",
            "description": "<p>指定司导</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "is_have_pack",
            "description": "<p>是否有行李0没有行李1有行李</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "total_num",
            "description": "<p>出行总人数</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "adult_num",
            "description": "<p>成人乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "child_num",
            "description": "<p>儿童乘客数</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "remark",
            "description": "<p>备注</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "flt_no",
            "description": "<p>航班号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "airport_name",
            "description": "<p>机场名</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "start_address",
            "description": "<p>出发地点</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "start_time",
            "description": "<p>出发时间</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=DriverPack&a=sendAirport"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=File&a=uploadImg",
    "title": "上传图片done 管少秋",
    "name": "uploadImg",
    "group": "File",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": false,
            "field": "authorization-token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Image",
            "optional": false,
            "field": "file",
            "description": "<p>上传的文件 最大5M 支持'jpg', 'gif', 'png', 'jpeg'</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "url",
            "description": "<p>下载链接(绝对路径)</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/File.php",
    "groupTitle": "File",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=File&a=uploadImg"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=HotGuide&a=getHotGuideDetail",
    "title": "得到热门攻略详情done   管少秋",
    "name": "getHotGuideDetail",
    "group": "HotGuide",
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "Http/1.1 200 OK\n    {\n    \"status\": 1,\n    \"msg\": \"成功\",\n    \"result\": {\n    \"info\": {\n    \"guide_id\": 5,\n    \"title\": \"敲开斜角巷的石砖，探寻巫师们的魔法世界\",\n    \"cover_img\": \"https://z0.muscache.com/im/pictures/a35b3599-8a40-4022-8337-6677d9b94f52.jpg?aki_policy=large\",\n    \"summary\": \"英国魔法之旅\",\n    \"user_id\": 50,\n    \"user_name\": \"Ning\",\n    \"city\": \"英国\",\n    \"content\": \"这条约克大教堂不远处的巷子叫The Shambles，是的，就是传说中哈利波特里“斜角巷”的原型。我不是哈迷，但这条巷子当真有味道！这是英国最古老的街道，也是欧洲保存最完好的中世纪街道。虽然人潮汹涌，街道两旁都是各种纪念品商店，但整条街的风情还是显露无疑。古街路面上铺满鹅卵石，街道两边的房子向中间倾斜，房顶几乎相接，外墙乍看之下好像纸糊的一样弱不禁风，实际却已历经成百上千年的岁月。阳光洒过，巷子内留下一片神秘的淡紫蓝色投影，彷佛自成一界，而肉眼看不到的魔法世界就藏匿其中，巫师们的精彩故事正静悄悄上演。\",\n    \"read_num\": 331,\n    \"good_num\": 46,\n    \"status\": null,\n    \"create_at\": 1495296000,\n    \"update_at\": 1499356800\n    },\n    \"comment\": [\n    {\n    \"head_pic\": null,\n    \"nickname\": \"18451847701\",\n    \"add_time\": 1504839306,\n    \"spec_key_name\": \"\",\n    \"content\": \"这是我的评论\",\n    \"impression\": null,\n    \"comment_id\": 1,\n    \"zan_num\": 100,\n    \"is_anonymous\": 0,\n    \"reply_num\": null,\n    \"img\": [\n    \"/public/upload/goods/2016/04-21/57187dbb16571.jpg\",\n    \"/public/upload/goods/2016/04-21/57187dd92a26f.jpg\",\n    \"/public/upload/goods/2016/04-21/57187dd8e18e8.jpg\"\n    ],\n    \"parent_id\": [\n    {\n    \"reply_id\": 1,\n    \"comment_id\": 1,\n    \"parent_id\": 0,\n    \"content\": \"one\",\n    \"user_name\": \"a\",\n    \"to_name\": \"\",\n    \"deleted\": 0,\n    \"reply_time\": 2017\n    },\n    {\n    \"reply_id\": 2,\n    \"comment_id\": 1,\n    \"parent_id\": 1,\n    \"content\": \"two2\",\n    \"user_name\": \"b\",\n    \"to_name\": \"a\",\n    \"deleted\": 0,\n    \"reply_time\": 2017\n    },\n    {\n    \"reply_id\": 3,\n    \"comment_id\": 1,\n    \"parent_id\": 2,\n    \"content\": \"three3\",\n    \"user_name\": \"a\",\n    \"to_name\": \"b\",\n    \"deleted\": 0,\n    \"reply_time\": 2017\n    },\n    {\n    \"reply_id\": 4,\n    \"comment_id\": 1,\n    \"parent_id\": 0,\n    \"content\": \"好\",\n    \"user_name\": \"\",\n    \"to_name\": \"\",\n    \"deleted\": 0,\n    \"reply_time\": 2017\n    },\n    {\n    \"reply_id\": 5,\n    \"comment_id\": 1,\n    \"parent_id\": 0,\n    \"content\": \"不错\",\n    \"user_name\": \"\",\n    \"to_name\": \"\",\n    \"deleted\": 0,\n    \"reply_time\": 2017\n    }\n    ]\n    }\n    ]\n    }\n    }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/HotGuide.php",
    "groupTitle": "HotGuide",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=HotGuide&a=getHotGuideDetail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=HotGuide&a=getHotGuideList",
    "title": "得到热门攻略列表done  管少秋",
    "name": "getHotGuideList",
    "group": "HotGuide",
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "     Http/1.1    200 OK\n{\n     \"guide_id\" :   \"1\",  //ID\n     \"cover_img\" :   \"http://xxxx.jpg\",  //攻略图片\n     \"title\"     :   \"我得标题很长很长\",  //攻略标题\n     \"summary\"   :   \"这是我的摘要\",  //发布人摘要\n     \"name\"      :   \"张三\",  //发布人姓名\n     \"city\" :   \"东京\",  //发布人所在城市\n     \"type_info\" :   \"\",  //身份标签（有几个身份？）\n     \"good_num\" :   \"111\",  //点赞数\n\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/HotGuide.php",
    "groupTitle": "HotGuide",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=HotGuide&a=getHotGuideList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Index&a=home",
    "title": "得到首页相关数据done  管少秋",
    "name": "home",
    "group": "Index",
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "            Http/1.1    200 OK\n{\n    \"cover_img\" : 缩略图,\n    \"name\" : 姓名,\n    \"good_num\" : 赞数,\n    \"city\" : 地址,\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Index.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Index&a=home"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Message&a=getMessageDetail",
    "title": "得到消息详情（未完成）wxx",
    "name": "getMessageDetail",
    "group": "Message",
    "version": "0.0.0",
    "filename": "application/api/controller/Message.php",
    "groupTitle": "Message",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Message&a=getMessageDetail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Message&a=getMessageList",
    "title": "得到消息列表（未完成）wxx",
    "name": "getMessageList",
    "group": "Message",
    "version": "0.0.0",
    "filename": "application/api/controller/Message.php",
    "groupTitle": "Message",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Message&a=getMessageList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=PackLine&a=getQualityLine",
    "title": "得到精品路线（未完成） 管少秋",
    "name": "getQualityLine",
    "group": "PackLine",
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": " Http/1.1    200 OK\n{\n\"line_id\" : \"11\",//线路ID\n\"cover_img\" : \"http://xxx.jpg\",//线路风景\n\"line_title\" : \"线路标题\",//线路标题\n\"line_sum\" : \"\",//游玩次数\n\"line_grade\" : \"\",//线路评分\n\"line_level\" : \"\",//线路等级\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/PackLine.php",
    "groupTitle": "PackLine",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=PackLine&a=getQualityLine"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=PackLine&a=home",
    "title": "包车定制首页  done    管少秋",
    "name": "home",
    "group": "PackLine",
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "Http/1.1    200 OK\n   {\n   \"status\": 1,\n   \"msg\": \"成功\",\n   \"result\": {\n       \"index\": [\n       {\n       \"id\": 1,\n       \"name\": \"接机\",\n       \"sort\": 1\n       },\n       {\n       \"id\": 2,\n       \"name\": \"送机\",\n       \"sort\": 2\n       },\n       {\n       \"id\": 3,\n       \"name\": \"单次接送\",\n       \"sort\": 3\n       },\n       {\n       \"id\": 4,\n       \"name\": \"快速预订\",\n       \"sort\": 4\n       },\n       {\n       \"id\": 5,\n       \"name\": \"私人定制\",\n       \"sort\": 5\n       },\n       {\n       \"id\": 6,\n       \"name\": \"按天包车游\",\n       \"sort\": 6\n       }\n       ],\n       \"banner\": [\n       {\n       \"ad_link\": \"http://dev.tpshop.cn/index.php/Home/Topic/detail/topic_id/1\",\n       \"ad_name\": \"自定义广告名称\",\n       \"ad_code\": \"/public/upload/ad/2016/09-19/57dfb0fbf3660.jpg\"\n       },\n       {\n       \"ad_link\": \"javascript:void();\",\n       \"ad_name\": \"自定义广告名称\",\n       \"ad_code\": \"/public/upload/ad/2016/09-19/57dfb118f00cd.jpg\"\n       },\n       {\n       \"ad_link\": \"javascript:void();\",\n       \"ad_name\": \"自定义广告名称\",\n       \"ad_code\": \"/public/upload/ad/2016/09-19/57dfb1767a5bb.jpg\"\n       },\n       {\n       \"ad_link\": \"www.baidu.com\",\n       \"ad_name\": \"sec\",\n       \"ad_code\": \"/public/upload/ad/2017/09-06/25123a234d51076968680e09c9d27e8e.jpg\"\n       }\n       ],\n       \"line\": [],\n       \"driver\": [\n           {\n           \"seller_id\": 17,\n           \"head_pic\": null,\n           \"seller_name\": \"少秋\",\n           \"drv_code\": \"20170908-1\",\n           \"province\": 0,\n           \"city\": 0,\n           \"star\": 4,\n           \"line\": null\n           }\n       ]\n   }\n   }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/PackLine.php",
    "groupTitle": "PackLine",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=PackLine&a=home"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=PackOrder&a=getPackOrder",
    "title": "得到包车订单列表done 管少秋",
    "name": "getPackOrder",
    "group": "PackOrder",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>订单状态 0未支付 1待派单 2待接单 3进行中（待开始、待确认） 4待评价 5已完成 all为全部</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "Http/1.1     200 OK\n    {\n    \"status\": 1,\n    \"msg\": \"成功\",\n    \"result\": {\n        \"totalPages\": 1,\n        \"list\": [\n            {\n            \"order_sn\": \"201709091232\",\n            \"seller_id\": 19,\n            \"status\": 5,\n            \"title\": \"自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返\",\n            \"customer_name\": \"西班牙\",\n            \"drv_name\": \"醉生梦死\",\n            \"create_at\": 1504858382,\n            \"drv_phone\": null,\n            \"total_price\": 100,\n            \"real_price\": \"100.00\"\n            },\n            {\n            \"order_sn\": \"201709091232\",\n            \"seller_id\": 20,\n            \"status\": 0,\n            \"title\": \"自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返\",\n            \"customer_name\": \"西班牙\",\n            \"drv_name\": \"醉生梦死\",\n            \"create_at\": 1504858382,\n            \"drv_phone\": null,\n            \"total_price\": 100,\n            \"real_price\": \"100.00\"\n            },\n        ]\n    }\n    }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/PackOrder.php",
    "groupTitle": "PackOrder",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=PackOrder&a=getPackOrder"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=PackOrder&a=getPackOrderDetail",
    "title": "得到包车订单详情done 管少秋",
    "name": "getPackOrderDetail",
    "group": "PackOrder",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "air_id",
            "description": "<p>订单ID</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "Http/1.1     200 OK\n    \"result\": {\n        \"air_id\": 16,\n        \"order_sn\": \"201709091232\",\n        \"user_id\": 60,\n        \"seller_id\": 19,\n        \"allot_seller_id\": \",18,19,20,\",\n        \"customer_name\": \"西班牙\",\n        \"customer_phone\": 1322222222,\n        \"use_car_adult\": 10,\n        \"use_car_children\": null,\n        \"work_at\": 22,\n        \"work_pointlng\": 123.021,\n        \"work_pointlat\": 36.25,\n        \"work_address\": \"江苏省苏州市\",\n        \"dest_pointlng\": 125.236,\n        \"dest_pointlat\": 36.23,\n        \"dest_address\": \"英格兰\",\n        \"status\": 5,\n        \"pay_way\": 1,\n        \"total_price\": 100,\n        \"real_price\": \"100.00\",\n        \"is_pay\": 1,\n        \"pay_time\": 1505119625,\n        \"start_time\": 1508688000,\n        \"end_time\": 1505119723,\n        \"add_time_long\": null,\n        \"add_recharge\": null,\n        \"add_reason\": null,\n        \"drv_name\": \"醉生梦死\",\n        \"drv_id\": 3,\n        \"drv_code\": \"121540215\",\n        \"req_car_id\": 11245,\n        \"req_car_type\": \"大众桑塔纳\",\n        \"con_car_id\": 1,\n        \"con_car_type\": \"2\",\n        \"con_car_seat_num\": null,\n        \"type\": 1,\n        \"flt_no\": \"\",\n        \"mile_length\": 100,\n        \"discount_id\": 23,\n        \"user_message\": \"\",\n        \"create_at\": 1504858382,\n        \"update_at\": 1504858382,\n        \"title\": \"自由女神+华尔街+三一教堂+归零地+帝国大厦 包车两日游，纽约往返\",\n        \"is_use_car\": 1,\n        \"remark\": null,\n        \"drv_phone\": null\n    }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/PackOrder.php",
    "groupTitle": "PackOrder",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=PackOrder&a=getPackOrderDetail"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=PackOrder&a=payPackOrderByBalance",
    "title": "通过余额支付订单   管少秋",
    "name": "payPackOrderByBalance",
    "group": "PackOrder",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "air_id",
            "description": "<p>订单ID</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "price",
            "description": "<p>优惠后的金额</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/PackOrder.php",
    "groupTitle": "PackOrder",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=PackOrder&a=payPackOrderByBalance"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Payment&a=alipay_sign",
    "title": "得到支付宝签名（待调试） wxx",
    "name": "alipay_sign",
    "group": "Pay",
    "version": "0.0.0",
    "filename": "application/api/controller/Payment.php",
    "groupTitle": "Pay",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Payment&a=alipay_sign"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Wxpay&a=dopay",
    "title": "得到微信签名并下单（待调试） wxx",
    "name": "dopay",
    "group": "Pay",
    "version": "0.0.0",
    "filename": "application/api/controller/Wxpay.php",
    "groupTitle": "Pay",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Wxpay&a=dopay"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Region&a=getAllCity",
    "title": "得到国内全部城市（省市信息）done  管少秋",
    "name": "getAllCity",
    "group": "Region",
    "version": "0.0.0",
    "filename": "application/api/controller/Region.php",
    "groupTitle": "Region",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Region&a=getAllCity"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Region&a=getChildCity",
    "title": "得到国外地区的子级列表done    管少秋",
    "name": "getChildCity",
    "group": "Region",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "parent_id",
            "description": "<p>把当前的ID字段座位parent_id传过来</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Region.php",
    "groupTitle": "Region",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Region&a=getChildCity"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Region&a=getChildHotCity",
    "title": "得到子热门城市done     管少秋",
    "name": "getChildHotCity",
    "group": "Region",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "id",
            "description": "<p>父级城市ID</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Region.php",
    "groupTitle": "Region",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Region&a=getChildHotCity"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Region&a=getIndexCity",
    "title": "得到国外地区的首级列表done  管少秋",
    "name": "getIndexCity",
    "group": "Region",
    "version": "0.0.0",
    "filename": "application/api/controller/Region.php",
    "groupTitle": "Region",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Region&a=getIndexCity"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=Region&a=searchCity",
    "title": "搜索城市done    管少秋",
    "name": "searchCity",
    "group": "Region",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "name",
            "description": "<p>城市名称</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Region.php",
    "groupTitle": "Region",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=Region&a=searchCity"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=api&c=LocalTalent&a=getLocalTalentDetail",
    "title": "得到当地达人详情done  管少秋",
    "name": "getLocalTalentDetail",
    "group": "Talent",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "talent_id",
            "description": "<p>{String}  当地达人</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "     Http/1.1    200 OK\n{\n \"talent_id\" :    “”，\n \"drv_code\" :    “”，//司导CODE\n \"store_id\" :    “”，//店主ID\n \"user_id\" :    “”，//房东\n \"talent_id\" :    “”，\n \"talent_id\" :    “”，\n \"cover_img\" :   \"http://xxx.jpg\",\n \"video_url\" :   \"http://xxx.mp4\",\n \"name\"      :   \"张三\",  //发布人姓名\n \"id_type\" :   \"\",  //身份标签（有几个身份？）\n \"city\"  :   \"xxxxxx\",//所在城市地址\n \"good_num\"  :   \"111\",//点赞数\n \"desc\"  :   \"111\"//简介\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/LocalTalent.php",
    "groupTitle": "Talent",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=api&c=LocalTalent&a=getLocalTalentDetail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=LocalTalent&a=getLocalTalentList",
    "title": "得到达人列表  传入p 为 n代表第n页 done  管少秋",
    "name": "getLocalTalentList",
    "group": "Talent",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}  token.</p>"
          },
          {
            "group": "Parameter",
            "optional": true,
            "field": "p",
            "description": "<p>{String}    第几页，默认1</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "     Http/1.1    200 OK\n{\n     \"status\": 1,\n     \"msg\": \"获取成功\",\n     \"result\": [\n         {\n         \"talent_id\" :   \"1\",  //当地达人ID\n         \"title\"     :   \"\",//标题\n         \"cover_img\" :   \"http://xxxx.jpg\",  //视屏封面图\n         \"name\"      :   \"张三\",  //发布人姓名\n         \"city\" :   \"东京\",  //发布人所在城市\n         \"type_info\" :   \"\",  //身份标签（有几个身份？）\n         \"good_num\" :   \"111\",  //点赞数\n         },\n         {\n         \"talent_id\" :   \"1\",  //视屏ID\n         \"cover_img\" :   \"http://xxxx.jpg\",  //视屏封面图\n         \"name\"      :   \"张三\",  //发布人姓名\n         \"city\" :   \"东京\",  //发布人所在城市\n         \"type_info\" :   \"\",  //身份标签（有几个身份？）\n         \"good_num\" :   \"111\",  //点赞数\n         }\n     ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/LocalTalent.php",
    "groupTitle": "Talent",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=LocalTalent&a=getLocalTalentList"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=BindMail",
    "title": "绑定用户邮箱done  管少秋",
    "name": "BindMail",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mail",
            "description": "<p>绑定邮箱</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>绑定邮箱code</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=BindMail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=doAttention",
    "title": "进行关注（未完成） wxx",
    "name": "doAttention",
    "group": "UserCenter",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}   token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "UserCenter",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=NewAction&a=doAttention"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=getCollGuideList",
    "title": "个人页面收藏动态展示（未完成） wxx",
    "name": "getCollGuideList",
    "group": "UserCenter",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}   token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "UserCenter",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=NewAction&a=getCollGuideList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=getCollNewActionList",
    "title": "个人页面收藏动态展示（未完成）wxx",
    "name": "getCollNewActionList",
    "group": "UserCenter",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}   token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "UserCenter",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=NewAction&a=getCollNewActionList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=getIndexNewAction",
    "title": "得到首页最新消息（未完成） wxx",
    "name": "getIndexNewAction",
    "group": "UserCenter",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}   token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "UserCenter",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=NewAction&a=getIndexNewAction"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=getMyGuideList",
    "title": "个人页面攻略展示（未完成）wxx",
    "name": "getMyGuideList",
    "group": "UserCenter",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}   token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "UserCenter",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=NewAction&a=getMyGuideList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=getMyNewActionList",
    "title": "个人页面动态展示（未完成） wxx",
    "name": "getMyNewActionList",
    "group": "UserCenter",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}   token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "UserCenter",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=NewAction&a=getMyNewActionList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=publishNewAction",
    "title": "发布新动态（未完成）wxx",
    "name": "publishNewAction",
    "group": "UserCenter",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "token",
            "description": "<p>{String}   token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "UserCenter",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502index.php?m=Api&c=NewAction&a=publishNewAction"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=account",
    "title": "我的钱包（待调试） wxx",
    "name": "account",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=account"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=addAddress",
    "title": "收货地址添加（待调试）wxx",
    "name": "addAddress",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=addAddress"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=bindPhone",
    "title": "绑定手机done  管少秋",
    "name": "bindPhone",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>绑定手机号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "countroy_code",
            "description": "<p>绑定国家的区号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>绑定手机code</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=bindPhone"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=del_address",
    "title": "收货地址删除（待调试） wxx",
    "name": "del_address",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=del_address"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=flashToken",
    "title": "刷新token（返回信息同login一样） done 管少秋",
    "name": "flashToken",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=flashToken"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=forgetPassword",
    "title": "忘记密码通过短信done  管少秋",
    "name": "forgetPassword",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>手机号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>加密后的密码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>验证码</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "      Http/1.1   200 OK\n{\n\"status\": 1,\n\"msg\": \"密码已重置,请重新登录\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=forgetPassword"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=forgetPasswordByMail",
    "title": "忘记密码通过邮箱done  管少秋",
    "name": "forgetPasswordByMail",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mail",
            "description": "<p>邮箱号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>加密后的密码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>验证码</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "      Http/1.1   200 OK\n{\n\"status\": 1,\n\"msg\": \"密码已重置,请重新登录\",\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=forgetPasswordByMail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=getAddressList",
    "title": "收货地址列表（待调试） wxx",
    "name": "getAddressList",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=getAddressList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=getGoodsCollect",
    "title": "我的收藏路线（待完成） 管少秋",
    "name": "getGoodsCollect",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=getGoodsCollect"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=getPackCouponList",
    "title": "得到优惠券列表 done 管少秋",
    "name": "getPackCouponList",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "model_type",
            "description": "<p>模块类型 0为包车模块1为商城模块2为民宿模块</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>发放类型 0下单赠送 1 按用户发放 2 免费领取 3 线下发放</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "store_id",
            "description": "<p>传入包车模块所对应发放优惠券人的drv_id store_id home_id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": " Http/1.1    200     OK\n{\n\"status\": 1,\n\"msg\": \"获取成功\",\n\"result\": [\n{\n\"id\": 63,\n\"cid\": 25,\n\"type\": 0,\n\"uid\": 60,\n\"order_id\": 0,\n\"get_order_id\": null,\n\"use_time\": 0,\n\"code\": \"\",\n\"send_time\": 1477566074,\n\"store_id\": 1,\n\"status\": 0,\n\"deleted\": 0,\n\"drv_id\": null,\n\"model_type\": 0,\n\"home_id\": null,\n\"name\": \"TPshop100元券\",//满899减掉100\n\"use_type\": 0,\n\"money\": \"100.00\",\n\"use_start_time\": 1477497600,\n\"use_end_time\": 1536835755,\n\"condition\": \"899.00\"\n},\n]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=getPackCouponList"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=userInfo",
    "title": "获取用户信息done  管少秋",
    "name": "info",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n\"status\": 1,\n\"msg\": \"获取成功\",\n\"result\": {\n    \"user_id\": 146,\n    \"email\": \"\",\n    \"password\": \"90600d68b0f56d90c4c34284d8dfd138\",\n    \"sex\": 0,\n    \"birthday\": 0,\n    \"user_money\": \"0.00\",\n    \"frozen_money\": \"0.00\",\n    \"distribut_money\": \"0.00\",\n    \"pay_points\": \"0.0000\",\n    \"address_id\": 0,\n    \"reg_time\": 1504596640,\n    \"last_login\": 1504602255,\n    \"last_ip\": \"\",\n    \"qq\": \"\",\n    \"mobile\": \"18451847701\",\n    \"mobile_validated\": 1,\n    \"oauth\": \"\",\n    \"openid\": null,\n    \"unionid\": null,\n    \"head_pic\": null,\n    \"province\": 0,\n    \"city\": 0,\n    \"district\": 0,\n    \"email_validated\": 0,\n    \"nickname\": \"18451847701\",\n    \"level\": 1,\n    \"discount\": \"1.00\",\n    \"total_amount\": \"0.00\",\n    \"is_lock\": 0,\n    \"is_distribut\": 1,\n    \"first_leader\": 0,\n    \"second_leader\": 0,\n    \"third_leader\": 0,\n    \"fourth_leader\": null,\n    \"fifth_leader\": null,\n    \"sixth_leader\": null,\n    \"seventh_leader\": null,\n    \"token\": \"a279c833cebe5fb963ccba311e27c394\",\n    \"address\": null,\n    \"pay_passwd\": null,\n    \"pre_pay_points\": \"0.0000\",\n    \"optional\": \"0.0000\",\n    \"vipid\": 0,\n    \"paypoint\": \"0.00\",\n    \"coupon_count\": 0,\n    \"collect_count\": 0,\n    \"focus_count\": 0,\n    \"visit_count\": 0,\n    \"return_count\": 0,\n    \"waitPay\": 0,\n    \"waitSend\": 0,\n    \"waitReceive\": 0,\n    \"order_count\": 0,\n    \"message_count\": 0,\n    \"comment_count\": 0,\n    \"uncomment_count\": 0,\n    \"serve_comment_count\": 0,\n    \"cart_goods_num\": 0\n}\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=userInfo"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=login",
    "title": "用户登录done  管少秋",
    "name": "login",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>用户名.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>密码.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "unique_id",
            "description": "<p>手机端唯一标识 类似web pc端sessionid.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "pushToken",
            "description": "<p>消息推送token.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "capache",
            "description": "<p>图形验证码.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "push_id",
            "description": "<p>推送id，相当于第三方的reg_id.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "      Http/1.1   200 OK\n{\n\"status\": 1,\n\"msg\": \"登陆成功\",\n    \"result\": {\n    \"user_id\": \"1\",\n    \"email\": \"398145059@qq.com\",\n    \"password\": \"e10adc3949ba59abbe56e057f20f883e\",\n    \"sex\": \"1\",\n    \"birthday\": \"2015-12-30\",\n    \"user_money\": \"9999.39\",\n    \"frozen_money\": \"0.00\",\n    \"pay_points\": \"5281\",\n    \"address_id\": \"3\",\n    \"reg_time\": \"1245048540\",\n    \"last_login\": \"1444134213\",\n    \"last_ip\": \"127.0.0.1\",\n    \"qq\": \"3981450598\",\n    \"mobile\": \"13800138000\",\n    \"mobile_validated\": \"0\",\n    \"oauth\": \"\",\n    \"openid\": null,\n    \"head_pic\": \"/Public/upload/head_pic/2015/12-28/56812d56854d0.jpg\",\n    \"province\": \"19\",\n    \"city\": \"236\",\n    \"district\": \"2339\",\n    \"email_validated\": \"1\",\n    \"nickname\": \"的广泛地\"\n    \"token\": \"9f3de86be794f81cdfa5ff3f30b99257\"        // 用于 app 登录\n    \"expireTime\":\"1245048540\"         //token过期时间\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "      Http/1.1   404 NOT FOUND\n{\n\"status\": -1,\n\"msg\": \"请填写账号或密码\",\n\"result\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=login"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=password",
    "title": "修改用户密码done 管少秋",
    "name": "password",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "      Http/1.1   200 OK\n{\n\"status\": 1,\n\"msg\": \"密码修改成功\",\n\"result\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=password"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=points",
    "title": "我的钱包明细（待调试） wxx",
    "name": "points",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=points"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=recharge_list",
    "title": "充值记录（待调试） wxx",
    "name": "recharge_list",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=recharge_list"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=reg",
    "title": "用户注册done  管少秋",
    "name": "reg",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "username",
            "description": "<p>手机号（未加国家区号的手机号）/邮件名.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>密码加密方式：md5(TPSHOP密码)</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>phone 为手机/mail为邮件</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "countroy_code",
            "description": "<p>国家代码编号</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "code",
            "description": "<p>手机短信验证码或邮箱验证码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "push_id",
            "description": "<p>推送id，相当于第三方的reg_id</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response:",
          "content": "HTTP/1.1 200 OK\n{\n\"status\": 1,\n\"msg\": \"注册成功\",\n\"result\": {\n    \"user_id\": 146,\n    \"email\": \"\",\n    \"password\": \"90600d68b0f56d90c4c34284d8dfd138\",\n    \"sex\": 0,\n    \"birthday\": 0,\n    \"user_money\": \"0.00\",\n    \"frozen_money\": \"0.00\",\n    \"distribut_money\": \"0.00\",\n    \"pay_points\": \"0.0000\",\n    \"address_id\": 0,\n    \"reg_time\": 1504596640,\n    \"last_login\": 1504596640,\n    \"last_ip\": \"\",\n    \"qq\": \"\",\n    \"mobile\": \"18451847701\",\n    \"mobile_validated\": 1,\n    \"oauth\": \"\",\n    \"openid\": null,\n    \"unionid\": null,\n    \"head_pic\": null,\n    \"province\": 0,\n    \"city\": 0,\n    \"district\": 0,\n    \"email_validated\": 0,\n    \"nickname\": \"18451847701\",\n    \"level\": 1,\n    \"discount\": \"1.00\",\n    \"total_amount\": \"0.00\",\n    \"is_lock\": 0,\n    \"is_distribut\": 1,\n    \"first_leader\": 0,\n    \"second_leader\": 0,\n    \"third_leader\": 0,\n    \"fourth_leader\": null,\n    \"fifth_leader\": null,\n    \"sixth_leader\": null,\n    \"seventh_leader\": null,\n    \"token\": \"c34ba58aec24003f0abec19ae2688c86\",\n    \"address\": null,\n    \"pay_passwd\": null,\n    \"pre_pay_points\": \"0.0000\",\n    \"optional\": \"0.0000\",\n    \"vipid\": 0,\n    \"paypoint\": \"0.00\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n\"status\": -1,\n\"msg\": \"账号已存在\",\n\"result\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=reg"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=setDefaultAddress",
    "title": "设置默认收货地址（待调试） wxx",
    "name": "setDefaultAddress",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=setDefaultAddress"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=thirdLogin",
    "title": "第三方登录done（未调试） 管少秋",
    "name": "thirdLogin",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "unique_id",
            "description": "<p>第三方唯一标识</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "from",
            "description": "<p>来源 wx weibo alipay</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "nickname",
            "description": "<p>第三方返回昵称</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "head_pic",
            "description": "<p>头像路径</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-response",
          "content": "     Http/1.1    200 Ok\n{\n\"status\": 1,\n\"msg\": \"登陆成功\",\n\"result\": {\n\"user_id\": \"12\",\n\"email\": \"\",\n\"password\": \"\",\n\"sex\": \"0\",\n\"birthday\": \"0000-00-00\",\n\"user_money\": \"0.00\",\n\"frozen_money\": \"0.00\",\n\"pay_points\": \"0\",\n\"address_id\": \"0\",\n\"reg_time\": \"1452331498\",\n\"last_login\": \"0\",\n\"last_ip\": \"\",\n\"qq\": \"\",\n\"mobile\": \"\",\n\"mobile_validated\": \"0\",\n\"oauth\": \"wx\",\n\"openid\": \"2\",\n\"head_pic\": null,\n\"province\": \"0\",\n\"city\": \"0\",\n\"district\": \"0\",\n\"email_validated\": \"0\",\n\"nickname\": \"\"\n}\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-response",
          "content": "           Http/1.1    200 OK\n{\n      \"status\": -1,\n      \"msg\": \"参数有误\",\n      \"result\": \"\"\n      }",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=thirdLogin"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=unBindMail",
    "title": "解除绑定用户邮箱（开发中） 管少秋",
    "name": "unBindMail",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mail",
            "description": "<p>绑定邮箱</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=unBindMail"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=unBindPhone",
    "title": "更改绑定手机（开发中） 管少秋",
    "name": "unBindPhone",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "mobile",
            "description": "<p>绑定手机号</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=unBindPhone"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=updateUserInfo",
    "title": "更改用户信息done  管少秋",
    "name": "updateUserInfo",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "nickname",
            "description": "<p>昵称</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "qq",
            "description": "<p>QQ号码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "head_pic",
            "description": "<p>头像URL</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "sex",
            "description": "<p>性别（0 保密 1 男 2 女）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "birthday",
            "description": "<p>生日 （2015-01-05）</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "province",
            "description": "<p>省份ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "city",
            "description": "<p>城市ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "district",
            "description": "<p>地区ID</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "token",
            "description": "<p>token</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=updateUserInfo"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=withdrawals",
    "title": "申请提现（待调试） wxx",
    "name": "withdrawals",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=withdrawals"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=User&a=withdrawals_list",
    "title": "提现列表（待调试） wxx",
    "name": "withdrawals_list",
    "group": "User",
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shz.api.user.ruitukeji.cn:8502/index.php?m=Api&c=User&a=withdrawals_list"
      }
    ]
  }
] });
