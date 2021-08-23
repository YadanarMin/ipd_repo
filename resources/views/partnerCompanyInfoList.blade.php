@extends('layouts.baselayout')
@section('title', 'CCC - 協力会社')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="../public/css/select2.min.css">
<link rel="stylesheet" href="../public/css/partnerCompany.css">


<script type="text/javascript" src="../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../public/js/partnerCompanyList.js"></script>
<script type="text/javascript" src="../public/js/select2/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>
.main-content{
	/*background-color: #f2f3f3;*/
	margin: 0 auto;
	width:90%;
	display : center;
}
#partnerCompanyList{
    
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
#minimizeBtn8,
#minimizeBtn9,
#minimizeBtn10,
#minimizeBtn11{
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
#searchDiv{
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
        <h3>協力会社一覧</h3>
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
    <div id="searchDiv">
        <div class="input-group" style="width : 30%; margin-right :5%">
          <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
          <input type="text" id="searchCompany" class="form-control" placeholder="会社名・職種・担当者・物件名で検索">
        </div>
        
        <div class="input-group">
            <select class="jobTypeSelect form-control" name="" multiple="multiple">
                
            </select>
            <span class="input-group-addon" id="searchBtn"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
          　<!--<button id="searchBtn" class="btn btn-primary"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>-->
        </div>
        
    </div>
    <div id="partnerCompanyList">
        <input name="loginUserName" value="{{Session::get('userName')}}" id="loginUserName" type="hidden">
        <table class="table table-bordered">
            <thead>
            
                <tr>
                    <th>会社名</th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="second">職種</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn2"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isJobTypeShow == 1)
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
                                
                                <div class="third"><span class="glyphicon glyphicon-triangle-top maruShowFirstYaruki"></span>やる気</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn3"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isYarukiShow == 1)
                                    
                                    <div class="third"><span class="glyphicon glyphicon-triangle-top maruShowFirstYaruki"></span>やる気</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn3"></span>
                                  @else
                                    
                                    <div class="third" style="display :none"><span class="glyphicon glyphicon-triangle-top maruShowFirstYaruki"></span>やる気</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn3"></span>
                                  @endif
                                @endforeach
                            @endif
                            
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="fouth"><span class="glyphicon glyphicon-triangle-top maruShowFirstRevit"></span>Revit所有</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn4"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isRevitShow == 1)
                                    <div class="fouth"><span class="glyphicon glyphicon-triangle-top maruShowFirstRevit"></span>Revit所有</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn4"></span>
                                  @else
                                    <div class="fouth" style="display : none"><span class="glyphicon glyphicon-triangle-top maruShowFirstRevit"></span>Revit所有</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn4"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="fifth"><span class="glyphicon glyphicon-triangle-top maruShowFirstIpd"></span>iPD留学</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn5"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isIpdShow == 1)
                                    <div class="fifth"><span class="glyphicon glyphicon-triangle-top maruShowFirstIpd"></span>iPD留学</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn5"></span>
                                  @else
                                    <div class="fifth" style="display : none"><span class="glyphicon glyphicon-triangle-top maruShowFirstIpd"></span>iPD留学</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn5"></span>
                                  @endif
                                @endforeach
                            @endif
                            
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="sixth"><span class="glyphicon glyphicon-triangle-top maruShowFirstSatellite"></span>サテライト経験</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn6"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isSatelliteExpShow == 1)
                                    <div class="sixth"><span class="glyphicon glyphicon-triangle-top maruShowFirstSatellite"></span>サテライト経験</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn6"></span>
                                  @else
                                    <div class="sixth" style="display : none"><span class="glyphicon glyphicon-triangle-top maruShowFirstSatellite"></span>サテライト経験</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn6"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="seven">サテライト物件名</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn7"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isSatelliteProjNameShow == 1)
                                    <div class="seven">サテライト物件名</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn7"></span>
                                  @else
                                    <div class="seven" style="display : none">サテライト物件名</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn7"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="eight">備考</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn8"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isRemarkShow == 1)
                                    <div class="eight">備考</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn8"></span>
                                  @else
                                    <div class="eight" style="display : none">備考</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn8"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="nine">担当者名</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn9"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isInchargeNameShow == 1)
                                    <div class="nine">担当者名</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn9"></span>
                                  @else
                                    <div class="nine" style="display : none">担当者名</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn9"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="ten">電話番号</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn10"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isPhoneShow == 1)
                                    <div class="ten">電話番号</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn10"></span>
                                  @else
                                    <div class="ten" style="display : none">電話番号</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn10"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th>
                    <th>
                        <div class="inlineDiv">
                            @if(count($hideOrShow) == 0 )
                                <div class="eleven">メール</div>
                                <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn11"></span>
                            @else
                                @foreach($hideOrShow as $a)
                                  @if($a->isEmailShow == 1)
                                    <div class="eleven">メール</div>
                                    <span class="glyphicon glyphicon-minus" aria-hidden="true" id="minimizeBtn11"></span>
                                  @else
                                    <div class="eleven" style="display : none">メール</div>
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true" id="minimizeBtn11"></span>
                                  @endif
                                @endforeach
                            @endif
                        </div>
                    </th> 
                </tr>
            </thead>
            
            <tbody id="searchableCompanyList">
                @foreach($partnerCompanyInfoList as $partnerCompany)
                <tr>
                    <td>{{ $partnerCompany['name'] }}</td>
                    <td>
                        @if(count($hideOrShow) == 0)
                            <div class="second">{{ $partnerCompany['industry_type'] }}</div>
                        @else
                            @foreach($hideOrShow as $a)
                                  @if($a->isJobTypeShow == 1)
                                   <div class="second">{{ $partnerCompany['industry_type'] }}</div>
                                  @else
                                    <div class="second" style="display : none">{{ $partnerCompany['industry_type'] }}</div>
                                  @endif
                            @endforeach
                        @endif
                    </td>
                    
                    <td>
                        @if(count($hideOrShow) == 0)
                            <div class="third">
                                @if($partnerCompany['yaruki'] == "NULL")
                                
                                @else
                                    {{ $partnerCompany['yaruki'] }}
                                @endif
                            </div>
                        @else
                            @foreach($hideOrShow as $a)
                                  @if($a->isYarukiShow == 1)
                                    <div class="third">
                                        @if($partnerCompany['yaruki'] == "NULL")
                                    
                                        @else
                                            {{ $partnerCompany['yaruki'] }}
                                        @endif
                                    </div>
                                  @else
                                  <div class="third" style="display : none">
                                        @if($partnerCompany['yaruki'] == "NULL")
                                
                                        @else
                                            {{ $partnerCompany['yaruki'] }}
                                        @endif
                                  </div>
                                  @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if(count($hideOrShow) == 0)
                            <div class="fouth">
                                @if($partnerCompany['revit'] == 'NULL')
                                @else
                                    {{ $partnerCompany['revit'] }}
                                @endif
                                
                            </div>
                        @else
                            @foreach($hideOrShow as $a)
                                  @if($a->isRevitShow == 1)
                                    <div class="fouth">
                                        @if($partnerCompany['revit'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['revit'] }}
                                        @endif
                                    </div>
                                  @else
                                    <div class="fouth" style="display : none">
                                        @if($partnerCompany['revit'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['revit'] }}
                                        @endif
                                    </div>
                                  @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if(count($hideOrShow) == 0)
                            <div class="fifth">
                                @if($partnerCompany['iPDStudent'] == 'NULL')
                                @else
                                    {{ $partnerCompany['iPDStudent'] }}
                                @endif
                            </div>
                        @else
                            @foreach($hideOrShow as $a)
                                  @if($a->isIpdShow == 1)
                                  <div class="fifth">
                                        @if($partnerCompany['iPDStudent'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['iPDStudent'] }}
                                        @endif
                                  </div>
                                  @else
                                  <div class="fifth" style="display : none">
                                        @if($partnerCompany['iPDStudent'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['iPDStudent'] }}
                                        @endif
                                  </div>
                                  @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if(count($hideOrShow) == 0)
                            <div class="sixth">
                                @if($partnerCompany['satelliteExp'] == 'NULL')
                                @else
                                    {{ $partnerCompany['satelliteExp'] }}
                                @endif
                            </div>
                        @else
                            @foreach($hideOrShow as $a)
                                  @if($a->isSatelliteExpShow == 1)
                                  <div class="sixth">
                                        @if($partnerCompany['satelliteExp'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['satelliteExp'] }}
                                        @endif
                                  </div>
                                  @else
                                  <div class="sixth" style="display : none">
                                        @if($partnerCompany['satelliteExp'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['satelliteExp'] }}
                                        @endif
                                  </div>
                                  @endif
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if(count($hideOrShow) == 0)
                            <div class="seven">
                                @if( $partnerCompany['satelliteName'] == 'NULL')
                                @else
                                    {{ $partnerCompany['satelliteName'] }}
                                @endif
                            </div>
                        @else
                            @foreach($hideOrShow as $a)
                                  @if($a->isSatelliteProjNameShow == 1)
                                  <div class="seven">
                                        @if( $partnerCompany['satelliteName'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['satelliteName'] }}
                                        @endif
                                  </div>
                                  @else
                                  <div class="seven" style="display : none">
                                        @if( $partnerCompany['satelliteName'] == 'NULL')
                                        @else
                                            {{ $partnerCompany['satelliteName'] }}
                                        @endif
                                  </div>
                                  @endif
                            @endforeach
                        @endif
                    </td>
                    
                    <td>
                         @if(count($hideOrShow) == 0)
                            <div class="eight">
                                @if($partnerCompany['remark'] == 'NULL' )
                                @else
                                    {{ $partnerCompany['remark'] }}
                                @endif
                            </div>
                        @else
                            @foreach($hideOrShow as $a)
                                  @if($a->isRemarkShow == 1)
                                  <div class="eight">
                                        @if($partnerCompany['remark'] == 'NULL' )
                                        @else
                                            {{ $partnerCompany['remark'] }}
                                        @endif
                                  </div>
                                  @else
                                  <div class="eight" style="display : none">
                                        @if($partnerCompany['remark'] == 'NULL' )
                                        @else
                                            {{ $partnerCompany['remark'] }}
                                        @endif
                                  </div>
                                  @endif
                            @endforeach
                        @endif
                    </td>
                    
                    @if(count($partnerCompany['incharge']) > 0)
                        <td>
                            
                                @for ($i = 0; $i < count($partnerCompany['incharge']); $i++)
                                    <div>
                                        
                                            @if(count($hideOrShow) == 0)
                                                <div class="nine">{{ $partnerCompany['incharge'][$i]['name']  }}</div>
                                            @else
                                                @foreach($hideOrShow as $a)
                                                      @if($a->isInchargeNameShow == 1)
                                                      <div class="nine">{{ $partnerCompany['incharge'][$i]['name']  }}</div>
                                                      @else
                                                      <div class="nine" style="display : none">{{ $partnerCompany['incharge'][$i]['name']  }}</div>
                                                      @endif
                                                @endforeach
                                            @endif
                                        
                                    </div>
                                @endfor
                            
                        </td>
                        <td>
                            
                                @for ($i = 0; $i < count($partnerCompany['incharge']); $i++)
                                    <div>
                                        
                                            @if(count($hideOrShow) == 0)
                                                <div class="ten">{{ $partnerCompany['incharge'][$i]['phone']  }}</div>
                                            @else
                                                @foreach($hideOrShow as $a)
                                                      @if($a->isPhoneShow == 1)
                                                      <div class="ten">{{ $partnerCompany['incharge'][$i]['phone']  }}</div>
                                                      @else
                                                      <div class="ten" style="display : none">{{ $partnerCompany['incharge'][$i]['phone']  }}</div>
                                                      @endif
                                                @endforeach
                                            @endif
                                        
                                    </div>
                                @endfor
                            
                        </td>
                        <td>
                            
                                @for ($i = 0; $i < count($partnerCompany['incharge']); $i++)
                                    <div>
                                        
                                            @if(count($hideOrShow) == 0)
                                                <div class="eleven">{{ $partnerCompany['incharge'][$i]['mail']  }}</div>
                                            @else
                                                @foreach($hideOrShow as $a)
                                                      @if($a->isEmailShow == 1)
                                                      <div class="eleven">{{ $partnerCompany['incharge'][$i]['mail']  }}</div>
                                                      @else
                                                      <div class="eleven" style="display : none">{{ $partnerCompany['incharge'][$i]['mail']  }}</div>
                                                      @endif
                                                @endforeach
                                            @endif
                                        
                                    </div>
                                @endfor
                            
                        </td>
                    @else
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="nine"></div>
                            @else
                                @foreach($hideOrShow as $a)
                                      @if($a->isInchargeNameShow == 1)
                                      <div class="nine"></div>
                                      @else
                                      <div class="nine" style="display : none"></div>
                                      @endif
                                @endforeach
                            @endif
                        </td>
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="ten"></div>
                            @else
                                @foreach($hideOrShow as $a)
                                      @if($a->isPhoneShow == 1)
                                      <div class="ten"></div>
                                      @else
                                      <div class="ten" style="display : none"></div>
                                      @endif
                                @endforeach
                            @endif
                        </td>   
                        <td>
                            @if(count($hideOrShow) == 0)
                                <div class="eleven"></div>
                            @else
                                @foreach($hideOrShow as $a)
                                      @if($a->isEmailShow == 1)
                                      <div class="eleven"></div>
                                      @else
                                      <div class="eleven" style="display : none"></div>
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