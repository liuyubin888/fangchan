var PartPage = function(data){

    /*
        pageInfo     包含如下参数

        CurrentPage  当前页码值  
        PageSize     每页显示条数，默认10条
        TotalCount   总条数
        TotalPage    总页数
    */
    this.pageInfo = data.pageInfo;
    this.divId = data.divId;   
    this.reBack = data.reBack; //点击分页回调函数
    this.pageCur = 'current_btn';
    this.pageCurNum = 8;   //显示分页按钮个数
    this.prevPage = 'prevPage';
    this.nextPage = 'nextPage';
    this.prevBtnMsgNot = '<a href="javascript:;" class="paginate_button disabled">上一页</a>';
    this.prevBtnMsg = '<a href="javascript:;" class="paginate_button" id="'+ this.prevPage +'">上一页</a>';
    this.nextBtnMsgNot = '<a href="javascript:;" class="paginate_button disabled">下一页</a>';
    this.nextBtnMsg = '<a href="javascript:;" class="paginate_button" id="'+ this.nextPage +'">下一页</a>';
    this.curPageAttrName = 'data-page';

}

PartPage.prototype = {
    init:function(){
        if(this.pageCurNum % 2 == 0){
            this.pageCurNum+=1;
        }
        this.createPage();
        this.bindEvent();
        
    },
    createPage:function(){
        var _this  = this;
        var html = "";
        var CurrentPage = Number(_this.pageInfo.CurrentPage);
        html += '<span class="mgcurrentNum" style="color: #000;border: none;">共' + _this.pageInfo.TotalCount + '条记录</span>';


        if( _this.pageInfo.TotalCount > 0){  //没有数据不显示
            html += '<span class="mgcurrentNum" style="color: #000;border: none;">第' + CurrentPage + '/' + _this.pageInfo.TotalPage + '页</span>';
        }
        
        
        if(CurrentPage == '1'){  //第一页，没有上一页
            html += _this.prevBtnMsgNot;   
        }else if(CurrentPage > '1'){
            // html += '<a href="javascript:;" '+ _this.curPageAttrName +'="1" class="'+ _this.pageCur +'">首页</a>';
            html += '<a href="javascript:;" class="paginate_button '+ _this.pageCur +'" '+ _this.curPageAttrName +'="1">首页</a>';    
            html += _this.prevBtnMsg;
        }
        


        /*计算开始和结束*/

        //算出前后最多相隔几个
        var neighborNum = (_this.pageCurNum - 1) / 2;

        //页码按钮结束位置相加数
        var mantissa = _this.pageCurNum - 1;

        //分页开始位置
        var pageBtnSatrt = 1;

        //分页结束数
        var pageEnd = 0;
        if(CurrentPage < _this.pageCurNum){

            var _tmp1 = _this.pageCurNum - CurrentPage;
            if(_tmp1 >= neighborNum){
                pageBtnSatrt = 1;
            }else if(_tmp1 < neighborNum){
                var _tmp2 = neighborNum - _tmp1;
                pageBtnSatrt = _tmp2+=1;
            }

        }else if(CurrentPage == _this.pageCurNum){
            pageBtnSatrt = neighborNum+=1;
        }else if(CurrentPage > _this.pageCurNum){
            var _tmp3 = (Number(CurrentPage) - _this.pageCurNum) + 1;
            pageBtnSatrt = _tmp3+=neighborNum;
        }
        
        pageEnd = pageBtnSatrt + mantissa;
        if(pageEnd > _this.pageInfo.TotalPage){
            pageEnd = _this.pageInfo.TotalPage;
        }
        
        /*计算开始和结束*/


        for(var i=pageBtnSatrt;i<=pageEnd;i++){
            if(i == CurrentPage){  //当前页不能点击
                // html += '<li class="active"><a href="javascript:;" >' + i + '</a></li>';    
                html += '<a href="javascript:;" class="paginate_button current">'+i+'</a>';    
            }else{
                // html += '<li><a href="javascript:;" '+ _this.curPageAttrName +'="' + i + '" class="'+ _this.pageCur +'">' + i + '</a></li>';
                html += '<a href="javascript:;" class="paginate_button '+ _this.pageCur +'" '+ _this.curPageAttrName +'="' + i + '">'+ i +'</a>';    
            }
            
        }

        if(CurrentPage >= _this.pageInfo.TotalPage){  //没有下一页
            html += this.nextBtnMsgNot;   
        }else if(CurrentPage < _this.pageInfo.TotalPage){
            html += this.nextBtnMsg;  
            // html += '<li><a href="javascript:;" '+ _this.curPageAttrName +'="'+ _this.pageInfo.TotalPage +'" class="'+ _this.pageCur +'">尾页</a></li>';
            html += '<a href="javascript:;" class="paginate_button '+ _this.pageCur +'" '+ _this.curPageAttrName +'="' + _this.pageInfo.TotalPage + '">尾页</a>';    
        }

        $('#'+this.divId).empty().append(html);
    },
    bindEvent:function(){
        var _this = this;
        //跳转第几页
        $('.'+_this.pageCur).click(function(){
            var CurrentPage = $(this).attr(_this.curPageAttrName);
            _this.reBack(CurrentPage);
        }); 

        //上一页
        $('#'+_this.prevPage).click(function(){
            _this.pageInfo.CurrentPage = Number(_this.pageInfo.CurrentPage);
            var CurrentPage = _this.pageInfo.CurrentPage -= 1;
            _this.reBack(CurrentPage);
        });

        //下一页
        $('#'+_this.nextPage).click(function(){
            _this.pageInfo.CurrentPage = Number(_this.pageInfo.CurrentPage);
            var CurrentPage = _this.pageInfo.CurrentPage += 1;
            _this.reBack(CurrentPage);
        });
    }
}