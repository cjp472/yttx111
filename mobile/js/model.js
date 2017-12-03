
/**
 * 订单对象 （类）
 * @param {Object} order
 */
function Order(order){
    //列表
    this.OrderID = order.OrderID; // 订单ID
    this.OrderSN = order.OrderSN; // 订单编号
    this.OrderSendType = order.OrderSendType; //> 发货方式
    this.OrderSendStatus = order.OrderSendStatus; // 发货状态
    this.OrderSendStatusName = order.OrderSendStatus; // 发货状态
    this.OrderPayType = order.OrderPayType; // 付款方式
    this.OrderPayStatus = order.OrderPayStatus; // 付款状态
    this.OrderPayStatusName = order.OrderPayStatus; // 付款状态
    this.OrderRemark = order.OrderRemark; // 备注
    this.OrderTotal = order.OrderTotal; // 金额
    this.OrderIntegral = order.OrderIntegral; // 已支付金额
    this.OrderStatus = order.OrderStatus ;// 订单状态
    this.OrderStatusName = order.OrderStatus ;// 订单状态
    this.OrderDate = order.OrderDate * 1000; // 订单提交时间戳
    
    //详细 header
    this.OrderReceiveCompany = ''; // 收货单位
    this.OrderReceiveName = ''; // 收货人
    this.OrderReceivePhone = ''; // 联系电话
    this.OrderReceiveAdd = ''; // 收货地址
    this.InvoiceType = ''; // 开票
    this.InvoiceTax = ''; // 税点
    this.DeliveryDate = ''; //  交货时间
    this.OrderType = ''; // 下单用户
    this.OrderSaler = ''; // 是否销售代表代下
    this.OrderFrom = ''; // 来源*
   
   //详细 body
    this.goodses = [];
    this.log = [];
    /*
    [Name] // 商品名称
    [Coding] // 商品编号
    [Units] // 单位
    [ContentColor] // 颜色
    [ContentSpecification] // 规格
    [ContentPrice] // 价格
    [ContentNumber] // 数量
    [ContentPercent] // 折扣
    [conType] // 买品赠品，c:买品，g:赠品
    */
}
// 订单对象方法
Order.prototype = {
  constructor: Order,
  setOrder: function(action,content,service,trueName){
      if(action == 'cancel'){
          // 审核通过
          if(this.OrderStatus != '0'){
              DHB.showMsg('当前订单不能取消!');
              return;
          }
      }
      
      // 防止重复提交
      this.cancelStatus = false;
      if(this.cancelStatus){
          return;
      }
      // 待审核状态 可以取消订单
      var params = {
          f: 'setOrder',
          v: {
              action: action,
              orderId: this.OrderID,
              content: content
          }
      },
      _this = this;
      service.saveData(params).success(function(data){
          if(data.rStatus == '100'){
              if(action == 'cancel'){
                  DHB.showMsg('取消成功!');
                  _this.OrderStatus = 8;
                  _this.OrderStatusName = '客户端取消';
              }else{
                  DHB.showMsg('留言成功!');
                  var obj = {
                      Name: trueName,
                      'Date': (new Date()).getTime()/1000,
                      Status: '客户留言',
                      Content: content
                  };
                  if(!_this.log){
                      _this.log = [];
                  }
                  _this.log.splice(0,0,obj);
              }
              
          }else{
              DHB.showMsg('操作失败!');
          }
      });
  }  
};


/**
 * 付款单对象 （类）
 * @param {Object} payment
 */
function Payment(payment){
    this.FinanceID = payment.FinanceID; // ID
    this.FinanceOrder = payment.FinanceOrder == '0'? '预付款': payment.FinanceOrder ; // 相关订单
    this.FinanceTotal = payment.FinanceTotal; // 金额
    this.FinanceToDate = payment.FinanceToDate; // 付款时间
    this.FinanceUpDate = payment.FinanceUpDate == '' ? '' : payment.FinanceUpDate * 1000; // 确认到帐时间
    this.FinanceDate = payment.FinanceDate * 1000; // 填写时间
    this.FinanceFlag = payment.FinanceFlag; // 状态 
    this.FinanceFlagName = payment.FinanceFlag; // 状态 
    
    
    this.FinanceAbout = ''; // 备注
    this.FinanceType = ''; // 类型
    this.AccountsBank = ''; // 银行
    this.AccountsNO = ''; // 帐号
    this.AccountsName = ''; // 帐号
}

/**
 * 发货单对象 （类）
 * @param {Object} transport
 */
function Transport(transport){
    this.ConsignmentID = transport.ConsignmentID; // ID
    this.ConsignmentOrder = transport.ConsignmentOrder; // ID
    this.ConsignmentNO = transport.ConsignmentNO || '暂无'; // 物流编号
    this.ConsignmentMan = transport.ConsignmentMan; // 发货人
    this.ConsignmentDate = transport.ConsignmentDate; // 发货时间
    this.ConsignmentFlag = transport.ConsignmentFlag; // 收货状态
    this.ConsignmentFlagName = transport.ConsignmentFlag; // 收货状态
    this.InceptAddress = transport.InceptAddress; // 收货地址
    this.ConsignmentRemark = ''; // 备注
    this.ConsignmentMoneyType = ''; // 运费付款方式
    this.ConsignmentMoney = ''; // 运费
    this.InceptMan = ''; // 收货人
    this.InceptCompany = ''; // 收货单位
    this.InceptPhone = ''; // 联系电话
    this.InputDate = ''; // 操作是时间
    this.LogisticsName = ''; // 物流公司
    this.LogisticsCode = ''; // 物流公司
    
    //详细 body
    this.goodses = [];
}

/**
 * 商品对象 （类）
 * @param {Object} goods
 */
function Goods(goods){
    /*
    [ID] // 商品ID
    [Name] // 商品名称
    [Coding] // 商品编号
    [Barcode] // 商品条码
    [Units] // 单位
    [Casing] // 包装
    [Picture] // 图片
    [Color] // 颜色
    [Specification] // 规格
    [Model] // 型号
    [Price] // 价格
    [PictureBig] // 大图
    [Content] // 详细介绍
    [Package] // 整包装出货数量
    [FieldContent] // 自定义字段
    [PicArray] // 多图
    */
}
