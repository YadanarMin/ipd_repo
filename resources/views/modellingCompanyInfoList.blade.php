@extends('layouts.baselayout')
@section('title', 'CCC - モデリング会社一覧')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/partnerCompany.css">
<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/modellingCompanyList.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
}
#partnerCompanyContactList{
    overflow: auto;
    white-space: nowrap;
    height: 500px;
    width: 100%;
    border : 1px solid #eee;
}
thead{
    background: #333030;
    color: white;
    font-size: 16px;
    
}
thead tr th {
    position: sticky;
    top: 0;
    background: #333030;
}
#minimizeBtn1,
#minimizeBtn2,
#minimizeBtn3,
#minimizeBtn4,
#minimizeBtn5,
#minimizeBtn6,
#minimizeBtn7,
#minimizeBtn8{
    border: 1px solid #cabcbc;
    border-radius: 3px;
    padding: 1px;
    color: white;
    background-color: #a9a5b1eb;
    cursor : pointer;
}

.inlineDiv{
    display : flex;
    justify-content: space-between;
}

.heading{

    display: flex;
    /* flex-wrap: nowrap; */
    justify-content: space-between;
    align-items: flex-end;
}

#searchCompanyContact{
    display: flex;
    width :100%;
    margin-bottom : 10px;
}

.select2-container{
   width : 300px;
  }

</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    <div class="heading">
        <h3>モデリング会社一覧</h3>
        <div class="btn-group customBtnGroup" role="group" aria-label="...">
          <button type="button" class="btn btn-default" id="insertBtn" onclick="InsertBtn();" >
              <span class="glyphicon glyphicon-pencil">　入力</span>
          </button>
          <button type="button" class="btn btn-default" id="showBtn" onclick="ShowBtn();">
              <span class="glyphicon glyphicon-th-list"> 一覧</span>
        　</button>
        </div>
    </div>
    
    <hr>
    <div id="searchCompanyContact">
        <div class="input-group" style="width : 30%; margin-right :5%">
          <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
          <input type="text" id="searchPartnerCompany" class="form-control" placeholder="会社名・職種・担当者・支店で検索">
        </div>
        
        <div class="input-group">
            <select class="partnerJobTypeSelect form-control" name="" multiple="multiple">
                
            </select>
            <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
          　<!--<button id="searchBtn" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>-->
        </div>
        
    </div>
    <div id="partnerCompanyContactList">
        <input name="loginUserNameForContactView" value="{{Session::get('userName')}}" id="loginUserNameForContactView" type="hidden">
        <table class="table table-bordered">
            <thead>
            
                <tr>
                   <th>パートナー会社</th>
                   <th>会社名</th>
                   <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="second">職種</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn2"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPartnerJobTypeShow == 1)
                                    <div class="second">職種</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn2"></span>
                                  @else
                                    <div class="second" style="display: none">職種</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn2"></span>
                                  @endif
                                @endforeach
                            @endif
                            
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="third">支店</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn3"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPartnerCompanyBranchShow == 1)
                                    <div class="third">支店</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn3"></span>
                                  @else
                                    <div class="third" style="display :none">支店</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn3"></span>
                                  @endif
                                @endforeach
                            @endif
                            
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="fouth">郵便番号</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn4"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPartnerMailCodeShow == 1)
                                    <div class="fouth">郵便番号</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn4"></span>
                                  @else
                                    <div class="fouth" style="display : none">郵便番号</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn4"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="fifth">会社住所</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn5"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPartnerCompanyAddressShow == 1)
                                    <div class="fifth">会社住所</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn5"></span>
                                  @else
                                    <div class="fifth" style="display : none">会社住所</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn5"></span>
                                  @endif
                                @endforeach
                            @endif
                            
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="sixth">担当者名</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn6"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPartnerInchargeNameShow == 1)
                                    <div class="sixth">担当者名</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn6"></span>
                                  @else
                                    <div class="sixth" style="display : none">担当者名</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn6"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="seven">電話番号</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn7"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPartnerPhoneShow == 1)
                                    <div class="seven">電話番号</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn7"></span>
                                  @else
                                    <div class="seven" style="display : none">電話番号</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn7"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="eight">メール</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn8"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPartnerEmailShow == 1)
                                    <div class="eight">メール</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn8"></span>
                                  @else
                                    <div class="eight" style="display : none">メール</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn8"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody id="searchableCompanyContactList">
                @foreach($modellingCompanyList as $modellingCompany)
                    <tr>
                        <td>
                            @if($modellingCompany['isPartnerCompany'] == 0)
                                <input class="form-check-input" type="checkbox" id="isPartnerCompany" name="isPartnerCompany">
                            @else
                                <input class="form-check-input" type="checkbox" id="isPartnerCompany" name="isPartnerCompany" checked disabled>
                            @endif
                        
                        </td>
                    
                        <td>{{ $modellingCompany['name'] }}</td>
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="second">{{ $modellingCompany['industry_type'] }}</div>
                            @else
                                @foreach($hideOrShow as $a)
                                      @if($a->isPartnerJobTypeShow == 1)
                                       <div class="second">{{ $modellingCompany['industry_type'] }}</div>
                                      @else
                                        <div class="second" style="display : none">{{ $modellingCompany['industry_type'] }}</div>
                                      @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="third">{{ $modellingCompany['branch'] }}</div>
                            @else
                                @foreach($hideOrShow as $a)
                                      @if($a->isPartnerCompanyBranchShow == 1)
                                      <div class="third">{{ $modellingCompany['branch'] }}</div>
                                      @else
                                      <div class="third" style="display : none">{{ $modellingCompany['branch'] }}</div>
                                      @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="fouth">{{ $modellingCompany['postal_code'] }}</div>
                            @else
                                @foreach($hideOrShow as $a)
                                      @if($a->isPartnerMailCodeShow == 1)
                                        <div class="fouth">{{ $modellingCompany['postal_code'] }}</div>
                                      @else
                                        <div class="fouth" style="display : none">{{ $modellingCompany['postal_code'] }}</div>
                                      @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="fifth">{{ $modellingCompany['address'] }}</div>
                            @else
                                @foreach($hideOrShow as $a)
                                      @if($a->isPartnerCompanyAddressShow == 1)
                                      <div class="fifth">{{ $modellingCompany['address'] }}</div>
                                      @else
                                      <div class="fifth" style="display : none">{{ $modellingCompany['address'] }}</div>
                                      @endif
                                @endforeach
                            @endif
                        </td>
                        
                        @if(count($modellingCompany['incharge']) > 0)
                        <td>
                            
                                @for ($i = 0; $i < count($modellingCompany['incharge']); $i++)
                                    <div>
                                        
                                            @if(count($hideOrShow) == 0)
                                                <div class="sixth">{{ $modellingCompany['incharge'][$i]['name'] }}</div>
                                            @else
                                                @foreach($hideOrShow as $a)
                                                      @if($a->isPartnerInchargeNameShow == 1)
                                                      <div class="sixth">{{ $modellingCompany['incharge'][$i]['name'] }}</div>
                                                      @else
                                                      <div class="sixth" style="display : none">{{ $modellingCompany['incharge'][$i]['name'] }}</div>
                                                      @endif
                                                @endforeach
                                            @endif
                                        
                                    </div>
                                @endfor
                            
                        </td>
                        <td>
                            
                                @for ($i = 0; $i < count($modellingCompany['incharge']); $i++)
                                    <div>
                                        
                                            @if(count($hideOrShow) == 0)
                                                <div class="seven">{{ $modellingCompany['incharge'][$i]['phone'] }}</div>
                                            @else
                                                @foreach($hideOrShow as $a)
                                                      @if($a->isPartnerPhoneShow == 1)
                                                      <div class="seven">{{ $modellingCompany['incharge'][$i]['phone'] }}</div>
                                                      @else
                                                      <div class="seven" style="display : none">{{ $modellingCompany['incharge'][$i]['phone'] }}</div>
                                                      @endif
                                                @endforeach
                                            @endif
                                        
                                    </div>
                                @endfor
                            
                        </td>
                        <td>
                            
                                @for ($i = 0; $i < count($modellingCompany['incharge']); $i++)
                                    <div>
                                        
                                            @if(count($hideOrShow) == 0)
                                                <div class="eight">{{ $modellingCompany['incharge'][$i]['mail'] }}</div>
                                            @else
                                                @foreach($hideOrShow as $a)
                                                      @if($a->isPartnerEmailShow == 1)
                                                      <div class="eight">{{ $modellingCompany['incharge'][$i]['mail'] }}</div>
                                                      @else
                                                      <div class="eight" style="display : none">{{ $modellingCompany['incharge'][$i]['mail'] }}</div>
                                                      @endif
                                                @endforeach
                                            @endif
                                        
                                    </div>
                                @endfor
                            
                        </td>
                    @else
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="sixth"></div>
                            @else
                                @foreach($hideOrShow as $a)
                                    @if($a->isPartnerInchargeNameShow == 1)
                                        <div class="sixth"></div>
                                    @else
                                        <div class="sixth" style="display : none"></div>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="seven"></div>
                            @else
                                @foreach($hideOrShow as $a)
                                    @if($a->isPartnerPhoneShow == 1)
                                        <div class="seven"></div>
                                    @else
                                        <div class="seven" style="display : none"></div>
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="eight"></div>
                            @else
                                @foreach($hideOrShow as $a)
                                    @if($a->isPartnerEmailShow == 1)
                                        <div class="eight"></div>
                                    @else
                                        <div class="eight" style="display : none"></div>
                                    @endif
                                @endforeach
                            @endif
                         </td>
                    @endif
                        
                    
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
       
       
       
    });
</script>
@endsection