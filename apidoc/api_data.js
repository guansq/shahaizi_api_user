define({ "api": [
  {
    "type": "GET",
    "url": "/comment/commentInfo",
    "title": "获取评论内容",
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
        "url": "http://shahaizi.api.user.dev.com/comment/commentInfo"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/comment/sendCommentInfo",
    "title": "发送评论内容",
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
        "url": "http://shahaizi.api.user.dev.com/comment/sendCommentInfo"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=DriverPack&a=getDriverDetail",
    "title": "司导详情",
    "name": "getDriverDetail",
    "group": "DriverPack",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "optional": false,
            "field": "drv_id",
            "description": "<p>{String}    司导ID</p>"
          }
        ]
      }
    },
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "Http/1.1 200 OK\n{\n\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.comindex.php?m=Api&c=DriverPack&a=getDriverDetail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=PackLine&a=getLocalLine",
    "title": "得到当地司导",
    "name": "getLocalLine",
    "group": "DriverPack",
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": " Http/1.1    200 OK\n{\n\"drv_id\"   : \"11\",//司导ID\n\"head_pic\" : \"http://xxx.jpg\",//司导图片\n\"user_name\" : \"司导姓名\",\n\"comment_level\" : \"1\",//评价等级\n\"local\" : \"\",//位置\n\"level\" : \"\",//等级\n\"grade\" : \"\",//评分\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/DriverPack.php",
    "groupTitle": "DriverPack",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.comindex.php?m=Api&c=PackLine&a=getLocalLine"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/file/uploadImg",
    "title": "上传图片",
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
        "url": "http://shahaizi.api.user.dev.com/file/uploadImg"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=LocalTalent&a=getIndexLocalTalent",
    "title": "得到首页当地达人列表",
    "name": "LocalTalent",
    "group": "Index",
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
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "     Http/1.1    200 OK\n{\n     \"talent_id\" :   \"1\",  //视屏ID\n     \"cover_img\" :   \"http://xxxx.jpg\",  //视屏封面图\n     \"name\"      :   \"张三\",  //发布人姓名\n     \"city\" :   \"东京\",  //发布人所在城市\n     \"id_type\" :   \"\",  //身份标签（有几个身份？）\n     \"good_num\" :   \"111\",  //点赞数\n\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/LocalTalent.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/index.php?m=Api&c=LocalTalent&a=getIndexLocalTalent"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/apiCode",
    "title": "返回码说明",
    "description": "<p>技术支持：<a href=\"http://www.ruitukeji.com\" target=\"_blank\">睿途科技</a></p>",
    "name": "apiCode",
    "group": "Index",
    "version": "0.0.0",
    "filename": "application/api/controller/Index.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/apiCode"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/appConfig",
    "title": "应用配置参数",
    "name": "appConfig",
    "group": "Index",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "defaultAvatar",
            "description": "<p>默认头像</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "share_percent",
            "description": "<p>分享佣金比例</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "grab_range",
            "description": "<p>通知附近司机接单范围</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "premium_rate",
            "description": "<p>保险费率</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "bond_person_amount",
            "description": "<p>个人保证金金额</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "bond_company_amount",
            "description": "<p>公司保证金金额</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "withdraw_begintime",
            "description": "<p>提现开始日期</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "withdraw_endtime",
            "description": "<p>提现结束日期</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "custom_phone",
            "description": "<p>客服电话</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "custom_email",
            "description": "<p>客服邮件</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "complain_phone",
            "description": "<p>投诉电话</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "weixin_limit",
            "description": "<p>微信限额</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "alipay_limit",
            "description": "<p>支付宝限额</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "tran_account",
            "description": "<p>转账银行账号</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "xx",
            "description": "<p>其他参数</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Index.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/appConfig"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index/getArticle",
    "title": "获取文章内容done",
    "name": "getArticle",
    "group": "Index",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>文章标识</p>"
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
            "field": "title",
            "description": "<p>文章标题.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>文章内容.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>文章标识.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Index.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/index/getArticle"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=HotGuide&a=getIndexHotGuide",
    "title": "得到首页热门动态",
    "name": "getIndexHotGuide",
    "group": "Index",
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
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "     Http/1.1    200 OK\n{\n     \"talent_id\" :   \"1\",  //视屏ID\n     \"cover_img\" :   \"http://xxxx.jpg\",  //视屏封面图\n     \"name\"      :   \"张三\",  //发布人姓名\n     \"city\" :   \"东京\",  //发布人所在城市\n     \"id_type\" :   \"\",  //身份标签（有几个身份？）\n     \"good_num\" :   \"111\",  //点赞数\n\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/HotGuide.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.comindex.php?m=Api&c=HotGuide&a=getIndexHotGuide"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=NewAction&a=getIndexNewAction",
    "title": "得到首页最新消息",
    "name": "getIndexNewAction",
    "group": "Index",
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
    "success": {
      "examples": [
        {
          "title": "Success-Response",
          "content": "     Http/1.1    200 OK\n{\n     \"talent_id\" :   \"1\",  //视屏ID\n     \"cover_img\" :   \"http://xxxx.jpg\",  //视屏封面图\n     \"title\" :   \"文章标题\",  //文章标题\n     \"name\"      :   \"张三\",  //发布人姓名\n     \"good_num\" :   \"111\",  //点赞数\n\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/NewAction.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.comindex.php?m=Api&c=NewAction&a=getIndexNewAction"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index/home",
    "title": "首页轮播图",
    "name": "home",
    "group": "Index",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": true,
            "field": "authorization-token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "list",
            "description": "<p>轮播图.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "list.id",
            "description": "<p>id.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "list.position",
            "description": "<p>序号.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.url",
            "description": "<p>跳转链接.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.src",
            "description": "<p>图片.</p>"
          },
          {
            "group": "Success 200",
            "type": "Object",
            "optional": false,
            "field": "unreadMsg",
            "description": "<p>未读消息.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Index.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/index/home"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/lastApk",
    "title": "获取最新apk下载地址",
    "name": "lastApk",
    "group": "Index",
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "url",
            "description": "<p>下载链接.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "versionNum",
            "description": "<p>真实版本号.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "version",
            "description": "<p>显示版本号.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Index.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/lastApk"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index/sendCaptcha",
    "title": "发送验证码",
    "name": "sendCaptcha",
    "group": "Index",
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
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "codeId",
            "description": "<p>此为客户端系统当前时间截 除去前两位后经MD5 加密后字符串.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "validationId",
            "description": "<p>codeIdvalidationId(此为手机号除去第一位后字符串+（codeId再次除去前三位） 生成字符串后经MD5加密后字符串) 后端接收到此三个字符串后      也同样生成validationId 与接收到的validationId进行对比 如果一致则发送短信验证码，否则不发送。同时建议对 codeId 进行唯一性检验   另外，错误时不要返回错误内容，只返回errCode，此设计仅限获取短信验证码</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Index.php",
    "groupTitle": "Index",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/index/sendCaptcha"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/message/delMessage",
    "title": "删除消息",
    "name": "delMessage",
    "group": "Message",
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
            "type": "String",
            "optional": false,
            "field": "msg_id",
            "description": "<p>消息</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Message.php",
    "groupTitle": "Message",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/message/delMessage"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/message/detail",
    "title": "我的消息-详情",
    "name": "detail",
    "group": "Message",
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
            "field": "id",
            "description": "<p>id.</p>"
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
            "field": "id",
            "description": "<p>消息ID.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>类型.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "title",
            "description": "<p>标题.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "content",
            "description": "<p>内容.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "isRead",
            "description": "<p>是否阅读</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "pushTime",
            "description": "<p>推送时间.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Message.php",
    "groupTitle": "Message",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/message/detail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/message/getUnRead",
    "title": "未读消息数量",
    "name": "getUnRead",
    "group": "Message",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": true,
            "field": "authorization-token",
            "description": "<p>token.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "list",
            "description": "<p>列表.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.name",
            "description": "<p>名称.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "list.unread",
            "description": "<p>未读数量.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.icon_url",
            "description": "<p>图标链接.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.push_type",
            "description": "<p>推送类型.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.msg",
            "description": "<p>列表文案.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Message.php",
    "groupTitle": "Message",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/message/getUnRead"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/message",
    "title": "我的消息-列表",
    "name": "index",
    "group": "Message",
    "header": {
      "fields": {
        "Header": [
          {
            "group": "Header",
            "type": "String",
            "optional": true,
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
            "type": "String",
            "optional": true,
            "field": "push_type",
            "defaultValue": "private",
            "description": "<p>消息类型. system=系统消息 private=私人消息</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>页码.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "pageSize",
            "defaultValue": "20",
            "description": "<p>每页数据量.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "list",
            "description": "<p>列表.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "list.id",
            "description": "<p>消息ID.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.type",
            "description": "<p>客户端类型 0货主端 1司机端.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.title",
            "description": "<p>标题.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.summary",
            "description": "<p>摘要.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "list.isRead",
            "description": "<p>是否阅读</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.pushTime",
            "description": "<p>推送时间.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>页码.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "pageSize",
            "description": "<p>每页数据量.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "dataTotal",
            "description": "<p>数据总数.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "pageTotal",
            "description": "<p>总页码数.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "unreadnum",
            "description": "<p>未读消息.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Message.php",
    "groupTitle": "Message",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/message"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=Api&c=PackLine&a=getQualityLine",
    "title": "得到精品路线",
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
        "url": "http://shahaizi.api.user.dev.comindex.php?m=Api&c=PackLine&a=getQualityLine"
      }
    ]
  },
  {
    "type": "GET",
    "url": "recommend/showMyRecommInfo",
    "title": "显示我的推荐信息",
    "name": "showMyRecommInfo",
    "group": "Recommend",
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
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "code",
            "description": "<p>推荐码</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Recommend.php",
    "groupTitle": "Recommend",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.comrecommend/showMyRecommInfo"
      }
    ]
  },
  {
    "type": "GET",
    "url": "recommend/showMyRecommList",
    "title": "显示我的推荐列表",
    "name": "showMyRecommList",
    "group": "Recommend",
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
            "optional": true,
            "field": "page",
            "defaultValue": "1",
            "description": "<p>页码.</p>"
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "pageSize",
            "defaultValue": "20",
            "description": "<p>每页数据量.</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Array",
            "optional": false,
            "field": "list",
            "description": "<p>列表</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.avatar",
            "description": "<p>被推荐人头像</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.name",
            "description": "<p>被推荐人名称</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "list.bonus",
            "description": "<p>奖励金</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "page",
            "description": "<p>页码.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "pageSize",
            "description": "<p>每页数据量.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "dataTotal",
            "description": "<p>数据总数.</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "pageTotal",
            "description": "<p>总页码数.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/Recommend.php",
    "groupTitle": "Recommend",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.comrecommend/showMyRecommList"
      }
    ]
  },
  {
    "type": "GET",
    "url": "index.php?m=api&c=LocalTalent&a=getLocalTalentDetail",
    "title": "得到当地达人详情",
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
        "url": "http://shahaizi.api.user.dev.comindex.php?m=api&c=LocalTalent&a=getLocalTalentDetail"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/index.php?m=Api&c=LocalTalent&a=getLocalTalentList",
    "title": "得到达人列表",
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
          "content": "     Http/1.1    200 OK\n{\n     \"status\": 1,\n     \"msg\": \"获取成功\",\n     \"result\": [\n         {\n         \"talent_id\" :   \"1\",  //视屏ID\n         \"cover_img\" :   \"http://xxxx.jpg\",  //视屏封面图\n         \"name\"      :   \"张三\",  //发布人姓名\n         \"city\" :   \"东京\",  //发布人所在城市\n         \"id_type\" :   \"\",  //身份标签（有几个身份？）\n         \"good_num\" :   \"111\",  //点赞数\n         },\n         {\n         \"talent_id\" :   \"1\",  //视屏ID\n         \"cover_img\" :   \"http://xxxx.jpg\",  //视屏封面图\n         \"name\"      :   \"张三\",  //发布人姓名\n         \"city\" :   \"东京\",  //发布人所在城市\n         \"id_type\" :   \"\",  //身份标签（有几个身份？）\n         \"good_num\" :   \"111\",  //点赞数\n         }\n     ]\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/LocalTalent.php",
    "groupTitle": "Talent",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/index.php?m=Api&c=LocalTalent&a=getLocalTalentList"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/User/changeAd",
    "title": "改变广告状态",
    "name": "changeAd",
    "group": "User",
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
            "type": "String",
            "optional": false,
            "field": "is_ad",
            "description": "<p>显示广告状态 0=显示，1=不显示</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/User/changeAd"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/user/getPersonAuthInfo",
    "title": "获取个人认证信息",
    "name": "getPersonAuthInfo",
    "group": "User",
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
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "auth_status",
            "description": "<p>认证状态（init=未认证，pass=认证通过，refuse=认证失败，delete=后台删除）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "auth_info",
            "description": "<p>认证失败原因</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "real_name",
            "description": "<p>真实姓名</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>绑定手机号</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "identity",
            "description": "<p>身份证号</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "sex",
            "description": "<p>性别 1=男 2=女 0=未知</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "hold_pic",
            "description": "<p>手持身份证</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "front_pic",
            "description": "<p>身份证正</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "back_pic",
            "description": "<p>身份证反</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/user/getPersonAuthInfo"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/user/info",
    "title": "获取用户信息",
    "name": "info",
    "group": "User",
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
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "id",
            "description": "<p>id.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "phone",
            "description": "<p>绑定手机号.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "type",
            "description": "<p>获取用户的类型. person-个人 company-公司</p>"
          },
          {
            "group": "Success 200",
            "type": "Number",
            "optional": false,
            "field": "sex",
            "description": "<p>性别 1=男 2=女 0=未知.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "avatar",
            "description": "<p>头像.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "real_name",
            "description": "<p>真实姓名.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "auth_status",
            "description": "<p>认证状态（init=未认证，check=认证中，pass=认证通过，refuse=认证失败，delete=后台删除）</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "bond_status",
            "description": "<p>保证金状态(init=未缴纳，checked=已缴纳,frozen=冻结)</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "bond",
            "description": "<p>保证金 保留两位小数点</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "recomm_code",
            "description": "<p>推荐码</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/user/info"
      }
    ]
  },
  {
    "type": "GET",
    "url": "/User/isAd",
    "title": "获取广告状态",
    "name": "isAd",
    "group": "User",
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
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "is_ad",
            "description": "<p>显示广告状态 0=显示，1=不显示</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/User/isAd"
      }
    ]
  },
  {
    "type": "POST",
    "url": "index.php?m=Api&c=User&a=login",
    "title": "用户登录",
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
          "content": "      Http/1.1   200 OK\n{\n\"status\": 1,\n\"msg\": \"登陆成功\",\n\"result\": {\n    \"user_id\": \"1\",\n    \"email\": \"398145059@qq.com\",\n    \"password\": \"e10adc3949ba59abbe56e057f20f883e\",\n    \"sex\": \"1\",\n    \"birthday\": \"2015-12-30\",\n    \"user_money\": \"9999.39\",\n    \"frozen_money\": \"0.00\",\n    \"pay_points\": \"5281\",\n    \"address_id\": \"3\",\n    \"reg_time\": \"1245048540\",\n    \"last_login\": \"1444134213\",\n    \"last_ip\": \"127.0.0.1\",\n    \"qq\": \"3981450598\",\n    \"mobile\": \"13800138000\",\n    \"mobile_validated\": \"0\",\n    \"oauth\": \"\",\n    \"openid\": null,\n    \"head_pic\": \"/Public/upload/head_pic/2015/12-28/56812d56854d0.jpg\",\n    \"province\": \"19\",\n    \"city\": \"236\",\n    \"district\": \"2339\",\n    \"email_validated\": \"1\",\n    \"nickname\": \"的广泛地\"\n    \"token\": \"9f3de86be794f81cdfa5ff3f30b99257\"        // 用于 app 登录\n    }\n}",
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
        "url": "http://shahaizi.api.user.dev.comindex.php?m=Api&c=User&a=login"
      }
    ]
  },
  {
    "type": "Get",
    "url": "/user/refreshToken",
    "title": "刷新token",
    "name": "refreshToken",
    "group": "User",
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
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "accessToken",
            "description": "<p>接口调用凭证（token有效期为7200秒）.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/user/refreshToken"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/index.php?m=Api&c=User&a=reg",
    "title": "用户注册",
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
            "description": "<p>手机号/用户名.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password",
            "description": "<p>密码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "password2",
            "description": "<p>确认密码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "code",
            "description": "<p>手机短信验证码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "unique_id",
            "description": "<p>手机唯一标识</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "capache",
            "description": "<p>图形验证码</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "recomm_code",
            "description": "<p>推荐码</p>"
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
          "content": "HTTP/1.1 200 OK\n{\n\"status\":1,\n\"msg\":\"注册成功\",\n\"result\":{\n    \"user_id\": \"13\",\n    \"email\": \"\",\n    \"password\": \"e10adc3949ba59abbe56e057f20f883e\",\n    \"sex\": \"0\",\n    \"birthday\": \"0000-00-00\",\n    \"user_money\": \"0.00\",\n    \"frozen_money\":\"0.00\",\n    \"pay_points\": \"0\",\n    \"address_id\": \"0\",\n    \"reg_time\": \"1452333288\",\n    \"last_login\": \"0\",\n    \"last_ip\": \"\",\n    \"qq\":\"\",\n    \"mobile\": \"13800138071\",\n    \"mobile_validated\": \"0\",\n    \"oauth\":\"\",\n    \"openid\": null,\n    \"head_pic\": null,\n    \"province\": \"0\",\n    \"city\": \"0\",\n    \"district\": \"0\",\n    \"email_validated\": \"0\",\n    \"nickname\": null\n    }\n}",
          "type": "json"
        }
      ]
    },
    "error": {
      "examples": [
        {
          "title": "Error-Response:",
          "content": "HTTP/1.1 404 Not Found\n{\n    \"status\": -1,\n    \"msg\": \"账号已存在\",\n    \"result\": \"\"\n}",
          "type": "json"
        }
      ]
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/index.php?m=Api&c=User&a=reg"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/User/forget",
    "title": "重置密码",
    "name": "resetPwd",
    "group": "User",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "account",
            "description": "<p>账号/手机号/邮箱.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "new_password",
            "description": "<p>加密的密码. 加密方式：MD5(&quot;RUITU&quot;+明文密码+&quot;KEJI&quot;).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "captcha",
            "description": "<p>验证码.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/User/forget"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/user/updateInfo",
    "title": "更新用户信息",
    "name": "updateInfo",
    "group": "User",
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
            "optional": true,
            "field": "sex",
            "description": "<p>性别 1=男 2=女 0=未知.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "avatar",
            "description": "<p>头像.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "nickName",
            "description": "<p>昵称.</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": true,
            "field": "payWay",
            "description": "<p>付款方式.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/user/updateInfo"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/User/updatePwd",
    "title": "修改密码",
    "name": "updatePwd",
    "group": "User",
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
            "type": "String",
            "optional": false,
            "field": "old_password",
            "description": "<p>加密的密码. 加密方式：MD5(&quot;RUITU&quot;+明文密码+&quot;KEJI&quot;).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "new_password",
            "description": "<p>加密的密码. 加密方式：MD5(&quot;RUITU&quot;+明文密码+&quot;KEJI&quot;).</p>"
          },
          {
            "group": "Parameter",
            "type": "String",
            "optional": false,
            "field": "repeat_password",
            "description": "<p>重复密码.</p>"
          }
        ]
      }
    },
    "version": "0.0.0",
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/User/updatePwd"
      }
    ]
  },
  {
    "type": "POST",
    "url": "/user/uploadAvatar",
    "title": "上传并修改头像",
    "name": "uploadAvatar",
    "group": "User",
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
          },
          {
            "group": "Parameter",
            "type": "Number",
            "optional": true,
            "field": "retType",
            "defaultValue": "json",
            "description": "<p>返回数据格式 默认=json  jsonp</p>"
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
    "filename": "application/api/controller/User.php",
    "groupTitle": "User",
    "sampleRequest": [
      {
        "url": "http://shahaizi.api.user.dev.com/user/uploadAvatar"
      }
    ]
  }
] });
