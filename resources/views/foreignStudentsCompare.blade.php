@extends('layouts.baselayout')
@section('title', 'CCC - 留学生情報比較')

<!--CSS and JS file-->
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="../../public/css/foreignStudentsShow.css" />

<script type="text/javascript" src="../../public/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../public/js/jquery.multiselect.js"></script>
<script type="text/javascript" src="../../public/js/foreignStudentsShow.js"></script>
<script type="text/javascript" src="../../public/js/foreignStudentsCompare.js"></script>

<style>
table, th, td {
  border: 1px solid black;
}

tr, td{
    width: 150px;
    height: 50px;
    text-align: left;
    font-size: 14px;
    font-weight: bold;
}
.bg-color{
    background-color : #e9e9e9;
}

.genzai{
    background-color : #cecef5;
}

.customBtn{
    background: #9da4c7;
    float: right;
    color: white;
    margin-right: 10px;
    padding: 5px;
}
</style>
@endsection

@section('content')
@include('layouts.loading')
<div class="main-content">
    <div id="studentInfo">
        <h3 id="studentInfoTitle">留学生情報比較​</h3>
        
        <hr>
        <input name="loggingInUser" value="{{Session::get('userName')}}" id="loggingInUser" type="hidden">
        <div class="form-check chechBoxDiv">
                <input type="checkbox" class="form-check-input" id="now" checked>
                <label class="form-check-label">現在　&nbsp;</label>
                
                <input type="checkbox" class="form-check-input" id="student" checked>
                <label class="form-check-label">留学中 </label>
            
        </div>
        <table>
            <tbody>
                <tr id="name">
                    <td colspan="2" class="bg-color">氏名</td>
                    @foreach ($students as $student)
                        <td>{{ $student['first_name'] . $student['last_name'] }}</td>
                    @endforeach
                </tr>
                
                
                <tr id="place">
                    @if(count($hideOrShow) == 0 )
                        <td rowspan="2" class="bg-color miseRow" >
                        支店
                        <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeMise"></span>
                        </td>
                        <td class="genzai"><div class="mise"><p class="now">現在</p></div></td>
                        @foreach ($students as $student)
                            <td class="genzai" >
                                <div class="mise"><p class="now">{{ $student['genzai_branch_name']}}</p></div>
                            </td>
                        @endforeach
                    @else
                        @foreach($hideOrShow as $a)
                                  @if($a->isMiseShow == 1)
                                        <td rowspan="2" class="bg-color miseRow" >
                                        支店
                                        <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeMise"></span>
                                        </td>
                                        <td class="genzai"><div class="mise"><p class="now">現在</p></div></td>
                                        @foreach ($students as $student)
                                            <td class="genzai" >
                                                <div class="mise"><p class="now">{{ $student['genzai_branch_name']}}</p></div>
                                            </td>
                                        @endforeach
                                  @else
                                        <td rowspan="1" class="bg-color miseRow" >
                                        支店
                                        <span class="glyphicon glyphicon-plus customBtn" aria-hidden="true" id="minimizeMise"></span>
                                        </td>
                                        <td class="genzai"><div class="mise" style="display : none"><p class="now">現在</p></div></td>
                                        @foreach ($students as $student)
                                            <td class="genzai" >
                                                <div class="mise" style="display : none"><p class="now">{{ $student['genzai_branch_name']}}</p></div>
                                            </td>
                                        @endforeach
                                  @endif
                        @endforeach
                    
                    @endif
                </tr>
                
                    @if(count($hideOrShow) == 0 )
                    <tr>
                        <td class="bg-color"><div class="mise"><p class="past">留学</p></div></td>
                        @foreach ($students as $student)
                            <td>
                                <div class="mise"><p class="past">{{ $student['s_branch_name']}}</p></div>
                            </td>
                        @endforeach
                    </tr>
                    @else
                        @foreach($hideOrShow as $a)
                                  @if($a->isMiseShow == 1)
                                  <tr>
                                        <td class="bg-color"><div class="mise"><p class="past">留学</p></div></td>
                                        @foreach ($students as $student)
                                            <td>
                                                <div class="mise"><p class="past">{{ $student['s_branch_name']}}</p></div>
                                            </td>
                                        @endforeach
                                   </tr>
                                  @else
                                  <tr style="display : none">
                                        <td class="bg-color"><div class="mise" style="display : none"><p class="past">留学</p></div></td>
                                        @foreach ($students as $student)
                                            <td>
                                                <div class="mise" style="display : none"><p class="past">{{ $student['s_branch_name']}}</p></div>
                                            </td>
                                        @endforeach
                                  </tr>
                                  @endif
                        @endforeach
                    
                    @endif
                
                <tr id="obayashi">
                    @if(count($hideOrShow) == 0 )
                        <td rowspan="2" class="bg-color obayashiRow">
                            大林組所属
                            <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeObayashi"></span>
                        </td>
                        <td class="genzai">
                            <div class="obayashi"><p class="now">現在</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td class="genzai">
                                <div class="obayashi"><p class="now">{{ $student['genzai_dept_name'] }}</p></div>
                            </td>
                        @endforeach
                    @else
                        @foreach($hideOrShow as $a)
                            @if($a->isObayashiShow == 1)
                                <td rowspan="2" class="bg-color obayashiRow">
                                    大林組所属
                                    <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeObayashi"></span>
                                </td>
                                <td class="genzai">
                                    <div class="obayashi"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="obayashi"><p class="now">{{ $student['genzai_dept_name'] }}</p></div>
                                    </td>
                                @endforeach
                            @else
                                <td rowspan="1" class="bg-color obayashiRow">
                                    大林組所属
                                    <span class="glyphicon glyphicon-plus customBtn" aria-hidden="true" id="minimizeObayashi"></span>
                                </td>
                                <td class="genzai">
                                    <div class="obayashi" style="display:none"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="obayashi" style="display:none"><p class="now">{{ $student['genzai_dept_name'] }}</p></div>
                                    </td>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                    
                </tr>
                @if(count($hideOrShow) == 0 )
                    <tr>
                        <td class="bg-color">
                            <div class="obayashi"><p class="past">留学</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td>
                                <div class="obayashi"><p class="past">{{ $student['s_dept_name'] }}</p></div>
                            </td>
                        @endforeach
                    </tr>
                @else
                    @foreach($hideOrShow as $a)
                            @if($a->isObayashiShow == 1)
                                <tr>
                                    <td class="bg-color">
                                        <div class="obayashi"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="obayashi"><p class="past">{{ $student['s_dept_name'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @else
                                <tr style="display:none">
                                    <td class="bg-color">
                                        <div class="obayashi" style="display:none"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="obayashi" style="display:none"><p class="past">{{ $student['s_dept_name'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                    @endforeach
                @endif
                
                <tr id="code">
                    @if(count($hideOrShow) == 0 )
                        <td rowspan="2" class="bg-color codeRow">
                            社員コード
                            <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeCode"></span>
                        </td>
                        <td class="genzai">
                            <div class="code"><p class="now">現在</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td class="genzai">
                                <div class="code"><p class="now">{{ $student['code'] }}</p></div>
                            </td>
                        @endforeach
                    @else
                        @foreach($hideOrShow as $a)
                            @if($a->isCodeShow == 1)
                                <td rowspan="2" class="bg-color codeRow">
                                    社員コード
                                    <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeCode"></span>
                                </td>
                                <td class="genzai">
                                    <div class="code"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="code"><p class="now">{{ $student['code'] }}</p></div>
                                    </td>
                                @endforeach
                            @else
                                <td rowspan="1" class="bg-color codeRow">
                                    社員コード
                                    <span class="glyphicon glyphicon-plus customBtn" aria-hidden="true" id="minimizeCode"></span>
                                </td>
                                <td class="genzai">
                                    <div class="code" style="display:none"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="code" style="display:none"><p class="now">{{ $student['code'] }}</p></div>
                                    </td>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                    
                </tr>
                @if(count($hideOrShow) == 0 )
                    <tr>
                        <td class="bg-color">
                            <div class="code"><p class="past">留学</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td>
                                <div class="code"><p class="past">{{ $student['s_code'] }}</p></div>
                            </td>
                        @endforeach
                    </tr>
                @else
                    @foreach($hideOrShow as $a)
                            @if($a->isCodeShow == 1)
                                <tr>
                                    <td class="bg-color">
                                        <div class="code"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="code"><p class="past">{{ $student['s_code'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @else
                                <tr style="display:none">
                                    <td class="bg-color">
                                        <div class="code" style="display:none"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="code" style="display:none"><p class="past">{{ $student['s_code'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                    @endforeach
                @endif
                
                    <tr id="haken">
                    @if(count($hideOrShow) == 0 )
                        <td rowspan="2" class="bg-color hakenRow">
                            派遣元所属
                            <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeHaken"></span>
                        </td>
                        <td class="genzai"><div class="haken"><p class="now">現在</p></div></td>
                        @foreach ($students as $student)
                            <td class="genzai">
                                <div class="haken"><p class="now">{{ $student['genzai_haken_company_name'] }}</p></div>
                            </td>
                        @endforeach
                    @else
                        @foreach($hideOrShow as $a)
                            @if($a->isHakenShow == 1)
                                <td rowspan="2" class="bg-color hakenRow">
                                    派遣元所属
                                    <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeHaken"></span>
                                </td>
                                <td class="genzai"><div class="haken"><p class="now">現在</p></div></td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="haken"><p class="now">{{ $student['genzai_haken_company_name'] }}</p></div>
                                    </td>
                                @endforeach
                            @else
                                <td rowspan="1" class="bg-color hakenRow">
                                    派遣元所属
                                    <span class="glyphicon glyphicon-plus customBtn" aria-hidden="true" id="minimizeHaken"></span>
                                </td>
                                <td class="genzai"><div class="haken" style="display:none"><p class="now">現在</p></div></td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="haken" style="display:none"><p class="now">{{ $student['genzai_haken_company_name'] }}</p></div>
                                    </td>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                    
                </tr>
                @if(count($hideOrShow) == 0 )
                    <tr>
                        <td class="bg-color">
                            <div class="haken"><p class="past">留学</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td>
                                <div class="haken"><p class="past">{{ $student['s_haken_company_name'] }}</p></div>
                            </td>
                        @endforeach
                    </tr>
                @else
                    @foreach($hideOrShow as $a)
                            @if($a->isHakenShow == 1)
                                <tr>
                                    <td class="bg-color">
                                        <div class="haken"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="haken"><p class="past">{{ $student['s_haken_company_name'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @else
                                <tr style="display:none">
                                    <td class="bg-color">
                                        <div class="haken" style="display:none"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="haken" style="display:none"><p class="past">{{ $student['s_haken_company_name'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                    @endforeach
                @endif
                    
                <tr id="skill">
                    @if(count($hideOrShow) == 0 )
                        <td rowspan="2" class="bg-color skillRow">
                            スキル
                            <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeSkill"></span>
                        </td>
                        <td class="genzai">
                            <div class="skill"><p class="now">現在</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td class="genzai">
                                <div class="skill"><p class="now">{{ $student['genzai_skill'] }}</p></div>
                            </td>
                        @endforeach
                    
                    @else
                        @foreach($hideOrShow as $a)
                                  @if($a->isSkillShow == 1)
                                        <td rowspan="2" class="bg-color skillRow">
                                            スキル
                                            <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeSkill"></span>
                                        </td>
                                        <td class="genzai">
                                            <div class="skill"><p class="now">現在</p></div>
                                        </td>
                                        @foreach ($students as $student)
                                            <td class="genzai">
                                                <div class="skill"><p class="now">{{ $student['genzai_skill'] }}</p></div>
                                            </td>
                                        @endforeach
                                  
                                  @else
                                        <td rowspan="1" class="bg-color skillRow">
                                            スキル
                                            <span class="glyphicon glyphicon-plus customBtn" aria-hidden="true" id="minimizeSkill"></span>
                                        </td>
                                        <td class="genzai">
                                            <div class="skill" style="display : none"><p class="now">現在</p></div>
                                        </td>
                                        @foreach ($students as $student)
                                            <td class="genzai">
                                                <div class="skill"  style="display : none"><p class="now">{{ $student['genzai_skill'] }}</p></div>
                                            </td>
                                        @endforeach
                                  
                                  @endif
                        @endforeach
                    @endif
                </tr>
                @if(count($hideOrShow) == 0 )
                    <tr>
                        <td class="bg-color"><div class="skill"><p class="past">留学</p></div></td>
                        @foreach ($students as $student)
                            <td><div class="skill"><p class="past">{{ $student['s_skill'] }}</p></div></td>
                        @endforeach
                    </tr>
                @else
                    @foreach($hideOrShow as $a)
                                  @if($a->isSkillShow == 1)
                                    <tr>
                                        <td class="bg-color"><div class="skill"><p class="past">留学</p></div></td>
                                        @foreach ($students as $student)
                                            <td><div class="skill"><p class="past">{{ $student['s_skill'] }}</p></div></td>
                                        @endforeach
                                    </tr>
                                  @else
                                    <tr style="display :none">
                                        <td class="bg-color"><div class="skill" style="display :none"><p class="past">留学</p></div></td>
                                        @foreach ($students as $student)
                                            <td><div class="skill" style="display :none"><p class="past">{{ $student['s_skill'] }}</p></div></td>
                                        @endforeach
                                    </tr>
                                  @endif
                    @endforeach
                @endif
                
                
                <tr id="field">
                    @if(count($hideOrShow) == 0 )
                        <td rowspan="2" class="bg-color fieldRow">
                            分野
                            <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeField"></span>
                        </td>
                        <td class="genzai">
                            <div class="field"><p class="now">現在</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td class="genzai">
                                <div class="field"><p class="now">{{ $student['genzai_field'] }}</p></div>
                            </td>
                        @endforeach
                    @else
                        @foreach($hideOrShow as $a)
                            @if($a->isFieldShow == 1)
                                <td rowspan="2" class="bg-color fieldRow">
                                    分野
                                    <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeField"></span>
                                </td>
                                <td class="genzai">
                                    <div class="field"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="field"><p class="now">{{ $student['genzai_field'] }}</p></div>
                                    </td>
                                @endforeach
                            @else
                                <td rowspan="1" class="bg-color fieldRow">
                                    分野
                                    <span class="glyphicon glyphicon-plus customBtn" aria-hidden="true" id="minimizeField"></span>
                                </td>
                                <td class="genzai">
                                    <div class="field" style="display:none"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="field" style="display:none"><p class="now">{{ $student['genzai_field'] }}</p></div>
                                    </td>
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                </tr>
                @if(count($hideOrShow) == 0 )
                    <tr>
                        <td class="bg-color">
                            <div class="field"><p class="past">留学</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td>
                                <div class="field"><p class="past">{{ $student['s_field'] }}</p></div>
                            </td>
                        @endforeach
                    </tr>
                @else
                    @foreach($hideOrShow as $a)
                            @if($a->isFieldShow == 1)
                            <tr>
                                <td class="bg-color">
                                    <div class="field"><p class="past">留学</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td>
                                        <div class="field"><p class="past">{{ $student['s_field'] }}</p></div>
                                    </td>
                                @endforeach
                            </tr>
                            @else
                            <tr style="display :none">
                                <td class="bg-color">
                                    <div class="field" style="display :none"><p class="past">留学</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td>
                                        <div class="field" style="display :none"><p class="past">{{ $student['s_field'] }}</p></div>
                                    </td>
                                @endforeach
                            </tr>
                            @endif
                    @endforeach
                @endif
                
            
                
                
                
                
                
                
                <tr id="type">
                     @if(count($hideOrShow) == 0 )
                        <td rowspan="2" class="bg-color typeRow">
                            留学種別
                            <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeType"></span>
                        </td>
                        <td class="genzai">
                            <div class="type"><p class="now">現在</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td class="genzai">
                                <div class="type"><p class="now">{{ $student['genzai_type'] }}</p></div>
                            </td>
                        @endforeach
                     @else
                        @foreach($hideOrShow as $a)
                            @if($a->isTypeShow == 1)
                                <td rowspan="2" class="bg-color typeRow">
                                    留学種別
                                    <span class="glyphicon glyphicon-minus customBtn" aria-hidden="true" id="minimizeType"></span>
                                </td>
                                <td class="genzai">
                                    <div class="type"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="type"><p class="now">{{ $student['genzai_type'] }}</p></div>
                                    </td>
                                @endforeach
                            @else
                                <td rowspan="1" class="bg-color typeRow">
                                    留学種別
                                    <span class="glyphicon glyphicon-plus customBtn" aria-hidden="true" id="minimizeType"></span>
                                </td>
                                <td class="genzai">
                                    <div class="type" style="display:none"><p class="now">現在</p></div>
                                </td>
                                @foreach ($students as $student)
                                    <td class="genzai">
                                        <div class="type" style="display:none"><p class="now">{{ $student['genzai_type'] }}</p></div>
                                    </td>
                                @endforeach
                            @endif
                        @endforeach
                     @endif
                    
                </tr>
                @if(count($hideOrShow) == 0 )
                    <tr>
                        <td class="bg-color">
                            <div class="type"><p class="past">留学</p></div>
                        </td>
                        @foreach ($students as $student)
                            <td>
                                <div class="type"><p class="past">{{ $student['s_type'] }}</p></div>
                            </td>
                        @endforeach
                    </tr>
                @else
                    @foreach($hideOrShow as $a)
                            @if($a->isTypeShow == 1)
                                <tr>
                                    <td class="bg-color">
                                        <div class="type"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="type"><p class="past">{{ $student['s_type'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @else
                                <tr style="display:none">
                                    <td class="bg-color">
                                        <div class="type" style="display:none"><p class="past">留学</p></div>
                                    </td>
                                    @foreach ($students as $student)
                                        <td>
                                            <div class="type" style="display:none"><p class="past">{{ $student['s_type'] }}</p></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                    @endforeach
                @endif
                
                
                <tr id="start">
                    <td colspan="2" class="bg-color">留学開始</td>
                    @foreach ($students as $student)
                        @if($student['startDate'] == "0000-00-00")
                            <td></td>
                        @else
                            <td>{{ $student['startDate']}}</td>
                        @endif
                        
                    @endforeach
                </tr>
                <tr id="end">
                    <td colspan="2" class="bg-color">留学終了</td>
                    @foreach ($students as $student)
                        @if($student['endDate'] == "0000-00-00")
                            <td></td>
                        @else
                            <td>{{ $student['endDate']}}</td>
                        @endif
                    @endforeach
                </tr>
                <tr id="puchi1">
                    <td colspan="2" class="bg-color">プチ留学1</td>
                    @foreach ($students as $student)
                        @if($student['puchi1'] == "0000-00-00")
                            <td></td>
                        @else
                            <td>{{ $student['puchi1']}}</td>
                        @endif
                    @endforeach
                </tr>
                <tr id="puchi2">
                    <td colspan="2" class="bg-color">プチ留学2</td>
                    @foreach ($students as $student)
                        @if($student['puchi2'] == "0000-00-00")
                            <td></td>
                        @else
                            <td>{{ $student['puchi2']}}</td>
                        @endif
                    @endforeach
                </tr>
                <tr id="puchi3">
                    <td colspan="2" class="bg-color">プチ留学3</td>
                    @foreach ($students as $student)
                        @if($student['puchi3'] == "0000-00-00")
                            <td></td>
                        @else
                            <td>{{ $student['puchi3']}}</td>
                        @endif
                    @endforeach
                </tr>
                <tr id="puchi4">
                    <td colspan="2" class="bg-color">プチ留学4</td>
                    @foreach ($students as $student)
                        @if($student['puchi4'] == "0000-00-00")
                            <td></td>
                        @else
                            <td>{{ $student['puchi4']}}</td>
                        @endif
                    @endforeach
                </tr>
            </tbody>
        </table>
        
    </div>
    
</div>
<script>
  
</script>
@endsection